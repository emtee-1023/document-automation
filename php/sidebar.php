<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Home</div>
                <a class="nav-link" href="index">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-home"></i></div>
                    Dashboard
                </a>
                <div class="sb-sidenav-menu-heading">Utilities</div>

                <a class="nav-link" href="clients">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                    Clients
                </a>

                <a class="nav-link" href="courts">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-gavel"></i></div>
                    Courts
                </a>

                <a class="nav-link" href="cases">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-briefcase"></i></div>
                    Cases
                </a>
                <a class="nav-link" href="case-docs">
                    <div class="sb-nav-link-icon"><i class="fa-regular fa-folder-open"></i></div>
                    Case Documents
                </a>

                <a class="nav-link" href="bill-clients">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-money-check-dollar"></i></i></div>
                    Invoice Clients
                </a>
                <a class="nav-link" href="reminders">
                    <div class="sb-nav-link-icon"><i class="fa-regular fa-calendar"></i></i></div>
                    Reminders
                </a>

                <a class="nav-link" href="tasks">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-list-check"></i></div>
                    Task Manager
                </a>

                <div class="sb-sidenav-menu-heading">Doc-Automation</div>
                <a class="nav-link" href="doc-automation">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-print"></i></div>
                    Doc Automation
                </a>

                <?php
                if ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'super admin') {
                    echo
                    '
                                <div class="sb-sidenav-menu-heading">Firm</div>
                                <a class="nav-link" href="firm-settings">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-gear"></i></div>
                                    Firm Settings
                                </a>
                                <a class="nav-link" href="firm-users">
                                    <div class="sb-nav-link-icon"><i class="fa-solid fa-address-card"></i></i></div>
                                    Manage Firm Users                            
                                </a>
                                ';
                }
                ?>
            </div>
        </div>
        <div class="sb-sidenav-footer bg-dark">
        </div>
    </nav>
</div>

<!-- fas fa-tachometer-alt -->