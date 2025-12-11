document.addEventListener('DOMContentLoaded', function () {
  const sidebar = document.querySelector('.sidebar');
  const toggle = document.querySelector('.menu-toggle'); // botão que abre/fecha
  let backdrop = document.querySelector('.sidebar-backdrop');


  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.className = 'sidebar-backdrop';
    if (sidebar && sidebar.parentNode) sidebar.parentNode.insertBefore(backdrop, sidebar.nextSibling);
    else document.body.appendChild(backdrop);
  }

  function openSidebar() {
    sidebar.classList.add('open');
    document.body.classList.add('sidebar-open');
    backdrop.classList.add('active');
  }
  function closeSidebar() {
    sidebar.classList.remove('open');
    document.body.classList.remove('sidebar-open');
    backdrop.classList.remove('active');
  }

  if (toggle && sidebar) {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      if (sidebar.classList.contains('open')) closeSidebar();
      else openSidebar();
    });
  }

  
  backdrop.addEventListener('click', closeSidebar);

  
  if (sidebar) {
    sidebar.addEventListener('touchstart', function () {
      document.body.classList.add('sidebar-open');
    }, { passive: true });

    sidebar.addEventListener('touchend', function () {
      
    }, { passive: true });
  }

  
  if (sidebar && sidebar.classList.contains('open')) openSidebar();
});