<?php
session_start();
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}

if(!isset($_SESSION["Usuario_tipo"]) || $_SESSION["Usuario_tipo"] !== "admin") {
    header("location:dashboardusuario.php");
    exit;
}

$admin_email = $_SESSION["Usuario_logado"];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboardadmin.php">
                <img src="../assets/logo.png" alt="Logotipo" class="logo me-2" /> Painel Administrativo
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardadmin.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-3">Bem-vindo, administrador</h1>
            <p class="mb-2"><strong>Login:</strong> <?php echo htmlspecialchars($admin_email); ?></p>
            <p class="text-info">Seu perfil foi identificado como administrador no login.</p>
            <a href="dashboardusuario.php" class="btn btn-light mt-2">Ver painel de usuario</a>
        </div>
    </main>
</body>
</html>
