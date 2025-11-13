document.addEventListener('DOMContentLoaded', function () {
    fetch('http://localhost/projeto-cupcake/dreamcakes/carrinho/buscar-dados-usuario.php')
        .then(res => res.json())
        .then(data => {
            if (data) {
                document.getElementById('endereco').value = data.endereco || '';
                document.getElementById('cep').value = data.cep || '';
            }
        });
});