document.addEventListener('DOMContentLoaded', () => {
    const sindicoBtn = document.getElementById('sindicoBtn');
    const moradorBtn = document.getElementById('moradorBtn');
    const tipoUsuario = document.getElementById('tipo_usuario');
    const hideableFields = document.querySelectorAll('.hideable');

    // síndico selecionado por padrão
    tipoUsuario.value = 'sindico';
    sindicoBtn.classList.add('active');

    sindicoBtn.addEventListener('click', () => {
        tipoUsuario.value = 'sindico';
        sindicoBtn.classList.add('active');
        moradorBtn.classList.remove('active');
        hideableFields.forEach(el => el.classList.remove('show'));
    });

    moradorBtn.addEventListener('click', () => {
        tipoUsuario.value = 'morador';
        moradorBtn.classList.add('active');
        sindicoBtn.classList.remove('active');
        hideableFields.forEach(el => el.classList.add('show'));
    });
});