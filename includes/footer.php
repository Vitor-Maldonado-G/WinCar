</div>
 <footer class="bg-dark text-white text-center py-3 mt-auto">
    <p class="mb-0">&copy; 2026 WinCar - Todos os direitos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function alternarSenha(idCampo, botao) {
        const campo = document.getElementById(idCampo);
        const icone = botao.querySelector('i');

        if (campo.type === 'password') {
            campo.type = 'text';
            icone.classList.remove('bi-eye');
            icone.classList.add('bi-eye-slash');
        } else {
            campo.type = 'password';
            icone.classList.remove('bi-eye-slash');
            icone.classList.add('bi-eye');
        }
    }
</script>

<script>
    const botaoTema = document.getElementById('toggleTema');
    const iconeTema = botaoTema.querySelector('i');

    function atualizarIconeTema() {
        if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
            iconeTema.classList.remove('bi-moon-stars');
            iconeTema.classList.add('bi-sun');
        } else {
            iconeTema.classList.remove('bi-sun');
            iconeTema.classList.add('bi-moon-stars');
        }
    }

    atualizarIconeTema();

    botaoTema.addEventListener('click', function() {
        const estaEscuro = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        if (estaEscuro) {
            document.documentElement.removeAttribute('data-bs-theme');
            localStorage.setItem('wincar-tema', 'light');
        } else {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('wincar-tema', 'dark');
        }

        atualizarIconeTema();
    });
</script>


</body> 
</html> 

