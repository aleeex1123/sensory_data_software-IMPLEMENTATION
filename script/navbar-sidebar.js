document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');

    if (userIcon && userDropdown) {
        userIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = userDropdown.style.opacity === '1';
            userDropdown.style.opacity = isVisible ? '0' : '1';
            userDropdown.style.pointerEvents = isVisible ? 'none' : 'auto';
            userDropdown.style.transform = isVisible ? 'translateY(-10px)' : 'translateY(0)';
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target) && !userIcon.contains(e.target)) {
                userDropdown.style.opacity = '0';
                userDropdown.style.pointerEvents = 'none';
                userDropdown.style.transform = 'translateY(-10px)';
            }
        });
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarFooter = document.getElementById('sidebar-footer');

    if (sidebarToggle && sidebar) {
        let sidebarOpen = false;
        sidebarToggle.onclick = function() {
            if (!sidebarOpen) {
                sidebar.style.left = '0';
                sidebarFooter.style.left = '0';
                sidebarOpen = true;
            } else {
                sidebar.style.left = '-280px';
                sidebarFooter.style.left = '-280px';
                sidebarOpen = false;
            }
        };
    }

    document.querySelectorAll('.sidebar-link-group').forEach(group => {
        const link = group.querySelector('.sidebar-link');
        const submenu = group.querySelector('.sidebar-submenu');
        if (link && submenu) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if (submenu.style.height === 'auto') {
                    submenu.style.display = 'none';
                    submenu.style.height = '0';
                } else {
                    submenu.style.display = 'block';
                    submenu.style.height = 'auto';
                }
            });
        }
    });
});
