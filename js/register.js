document.addEventListener('DOMContentLoaded', () => {

    const botaoSindico = document.getElementById('sindicoBtn');
    const botaoMorador = document.getElementById('moradorBtn');
    const campoTipoUsuario = document.getElementById('tipo_usuario');
    const camposEscondidos = document.querySelectorAll('.hideable');

    function trocarPerfil(usuario) {
        campoTipoUsuario.value = usuario;

        if (usuario === 'sindico') {
            botaoSindico.classList.add('active');
            botaoMorador.classList.remove('active');
            camposEscondidos.forEach(campo => campo.classList.remove('show'));
        } else {
            botaoMorador.classList.add('active');
            botaoSindico.classList.remove('active');
            camposEscondidos.forEach(campo => campo.classList.add('show'));
        }
    }


    trocarPerfil('sindico');

    botaoSindico.addEventListener('click', () => trocarPerfil('sindico'));
    botaoMorador.addEventListener('click', () => trocarPerfil('morador'));
});
