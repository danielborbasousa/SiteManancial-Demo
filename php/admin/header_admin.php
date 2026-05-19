<?php
// Shared admin header include located inside php/admin/ for easier inclusion from admin pages.
?>
<nav class="navbar navbar-expand-lg navbar-dark w-100 p-3 px-4">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
        <img src="../../assets/logo.png" alt="Logotipo" class="logo me-2" /> Painel Admin
    </a>
    <div class="d-flex gap-2 align-items-center ms-auto">
        <div class="theme-toggle-container">
            <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
            <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
            <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
        </div>
        <?php
        $user_id = isset($_SESSION['Usuario_id']) ? (int) $_SESSION['Usuario_id'] : 0;
        $total_unread = 0;
        if ($user_id > 0 && isset($conn)) {
            $sql_unread = "SELECT COUNT(*) as cnt FROM ID_NOTIFICACAO WHERE IDF_ID = $user_id AND (IDN_LIDA = 0 OR IDN_LIDA IS NULL)";
            $res_unread = mysqli_query($conn, $sql_unread);
            if ($res_unread && $row_un = mysqli_fetch_assoc($res_unread)) {
                $total_unread = (int) $row_un['cnt'];
            }
        }
        ?>
        <a href="notificacoes.php" class="text-decoration-none position-relative notif-bell-link" style="font-size:1.2rem; color: var(--primary-light);">
            <i class="fas fa-bell"></i>
            <?php if ($total_unread > 0) { ?>
                <span class="notif-badge position-absolute d-inline-flex align-items-center justify-content-center rounded-circle" style="background:#dc2626;color:#fff;font-size:0.7rem;width:20px;height:20px;right:-6px;top:-6px;border:2px solid rgba(0,0,0,0.15);"><?php echo $total_unread; ?></span>
            <?php } ?>
        </a>
        <a href="dashboard.php" class="btn btn-sm btn-outline-light">Voltar ao Painel</a>
    </div>
</nav>
