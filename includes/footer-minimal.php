<?php
?>
</div>
<script>
    window.addEventListener('beforeunload', function() {
        const loginButton = document.getElementById('loginButton');
        if (loginButton) {
            loginButton.classList.remove('loading');
            loginButton.innerHTML = 'Entrar no Sistema';
        }
    });
</script>
</body>

</html>