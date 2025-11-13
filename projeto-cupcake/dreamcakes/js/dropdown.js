document.addEventListener('DOMContentLoaded', function () {
    const dropdownBtn = document.querySelector('.dropdown-btn');
    const dropdownContent = document.querySelector('.dropdown-content');

    // Inicialmente esconde o menu
    dropdownContent.style.display = 'none';

    dropdownBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    });

    // Fecha o menu ao clicar fora
    document.addEventListener('click', function () {
        dropdownContent.style.display = 'none';
    });

    // Impede que clique dentro do menu feche ele
    dropdownContent.addEventListener('click', function (e) {
        e.stopPropagation();
    });
});