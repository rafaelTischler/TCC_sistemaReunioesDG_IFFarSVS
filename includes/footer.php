<?php
?>
</div> <!--fechamento do container-->
</div> <!--fechamento do main-content-->

<script>
    //configurações da sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        if (menuToggle && sidebar) {
            //alternar sidebar em mobile
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('open');
            });

            //fechar sidebar ao clicar fora dela em mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 992 &&
                    !sidebar.contains(event.target) &&
                    !menuToggle.contains(event.target) &&
                    sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            });
        }
    });
</script>
</body>

</html>