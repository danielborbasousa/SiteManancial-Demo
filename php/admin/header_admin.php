<?php
// Shared admin header include located inside php/admin/ for easier inclusion from admin pages.
?>
<nav class="navbar navbar-expand-lg navbar-dark w-100 p-100 px-4">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="admin_conteudos.php">
        <img src="../../assets/logo.png" alt="Logotipo" class="logo me-2" /> Painel Admin
    </a>
    <div class="d-flex gap-2 align-items-center ms-auto">
        <div class="theme-toggle-container">
            <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
            <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
            <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
        </div>
        <a href="dashboard.php" class="btn btn-sm btn-outline-light">Voltar ao Painel</a>
    </div>
</nav>
