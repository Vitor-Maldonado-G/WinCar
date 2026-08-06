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


</body> 
</html> 

