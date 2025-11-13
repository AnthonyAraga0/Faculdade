<?php
session_start();

require_once __DIR__ . '/config.php';

// Google OAuth config (ajuste seu client_id/secret)
$google_oauth_client_id = '';
$google_oauth_client_secret = '';
$google_oauth_redirect_uri = '';
$google_oauth_version = 'v3';

function abortBlocked($email = null) {
    // limpa qualquer sessão iniciada
    if (session_status() === PHP_SESSION_ACTIVE) {
        // remove variáveis de sessão
        $_SESSION = [];
        // destrói cookie de sessão se existir
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    // redireciona para a tela de login com flag de bloqueio
    $location = 'login.php?blocked=1';
    if (!empty($email)) {
        $location .= '&email=' . rawurlencode($email);
    }
    header('Location: ' . $location);
    exit;
}

if (isset($_GET['code']) && !empty($_GET['code'])) {
    // troca code por access_token
    $params = [
        'code' => $_GET['code'],
        'client_id' => $google_oauth_client_id,
        'client_secret' => $google_oauth_client_secret,
        'redirect_uri' => $google_oauth_redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);

    if (isset($response['access_token']) && !empty($response['access_token'])) {
        // pega perfil do Google
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/' . $google_oauth_version . '/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($response, true);

        if (isset($profile['email'])) {
            $google_name_parts = [];
            $google_name_parts[] = isset($profile['given_name']) ? preg_replace('/[^a-zA-Z0-9 ]/s', '', $profile['given_name']) : '';
            $google_name_parts[] = isset($profile['family_name']) ? preg_replace('/[^a-zA-Z0-9 ]/s', '', $profile['family_name']) : '';
            $email = $profile['email'];
            $nome = trim(implode(' ', $google_name_parts));
            $foto = isset($profile['picture']) ? $profile['picture'] : (isset($profile['foto']) ? $profile['foto'] : '');

            $id = null;
            $tipo = 0;
            $bloq = 0;
            $account = null;

            // usa PDO se disponível
            if (isset($pdo) && $pdo instanceof PDO) {
                try {
                    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
                    $stmt->execute([$email]);
                    $account = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($account) {
                        // checa bloqueio imediatamente
                        if (!empty($account['bloq']) && (int)$account['bloq'] === 1) abortBlocked();
                        $id = $account['id'];
                    } else {
                        $stmt = $pdo->prepare('INSERT INTO usuarios (email, nome, foto, criacao, metodo, bloq, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)');
                        $criacao = date('Y-m-d H:i:s');
                        $metodo = 'google';
                        $bloq = 0;
                        $tipo = 0;
                        $stmt->execute([$email, $nome, $foto, $criacao, $metodo, $bloq, $tipo]);
                        $id = $pdo->lastInsertId();
                        $account = [
                            'id' => $id,
                            'email' => $email,
                            'bloq' => $bloq,
                            'tipo' => $tipo
                        ];
                    }
                } catch (Exception $ex) {
                    exit('DB error: ' . $ex->getMessage());
                }

            // ou usa MySQLi ($conn) se disponível
            } elseif (isset($conn) && ($conn instanceof mysqli || get_class($conn) === 'mysqli')) {
                $stmt = $conn->prepare("SELECT id, bloq, tipo FROM usuarios WHERE email = ? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $account = $res ? $res->fetch_assoc() : null;
                    $stmt->close();

                    if ($account) {
                        // checa bloqueio imediatamente
                        if (!empty($account['bloq']) && (int)$account['bloq'] === 1) abortBlocked();
                        $id = $account['id'];
                    } else {
                        $criacao = date('Y-m-d H:i:s');
                        $metodo = 'google';
                        $bloq = 0;
                        $tipo = 0;
                        $stmt = $conn->prepare("INSERT INTO usuarios (email, nome, foto, criacao, metodo, bloq, tipo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        if ($stmt) {
                            // 5 strings then 2 integers -> 'sssssii'
                            $stmt->bind_param("sssssii", $email, $nome, $foto, $criacao, $metodo, $bloq, $tipo);
                            $stmt->execute();
                            $id = $stmt->insert_id;
                            $stmt->close();
                            $account = [
                                'id' => $id,
                                'email' => $email,
                                'bloq' => $bloq,
                                'tipo' => $tipo
                            ];
                        } else {
                            exit('DB insert prepare failed: ' . $conn->error);
                        }
                    }
                } else {
                    exit('DB prepare failed: ' . $conn->error);
                }

            } else {
                exit('Nenhuma conexão com o banco (PDO ou MySQLi) encontrada em config.php');
            }

            // checa bloqueio (aplicado também para o caso do INSERT acima por segurança)
            $blocked = !empty($account['bloq']) && (int)$account['bloq'] === 1;
            if ($blocked) abortBlocked();

            // autentica sessão e marca tipo
            session_regenerate_id(true);
            $_SESSION['google_loggedin'] = true;
            $_SESSION['google_id'] = $id;
            $_SESSION['tipo'] = !empty($account['tipo']) && (int)$account['tipo'] === 1 ? 1 : 0;

            // redireciona
            header('Location: ../dreamcakes/inicio/inicio.php');
            exit;
        } else {
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        exit('Invalid access token! Please try again later!');
    }
} else {
    // redireciona para o consent screen do Google
    $params = [
        'response_type' => 'code',
        'client_id' => $google_oauth_client_id,
        'redirect_uri' => $google_oauth_redirect_uri,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit;
}
?>