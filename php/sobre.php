<?php
session_start();
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboardusuario.php">
                <img src="../assets/logo.png" alt="Logotipo" class="logo me-2" /> Missao Evangelica Manancial da Esperança
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardusuario.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link active" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-3">Sobre a plataforma</h1>
            <p>
                Esta plataforma foi criada para disponibilizar videos, mensagens e estudos da Missao Evangelica
                Manancial da Esperanca de forma simples e organizada.
            </p>
            <p>
                O objetivo e facilitar o acesso ao conteudo da igreja para membros e visitantes, usando uma
                interface facil de navegar.
            </p>
            <a href="dashboardusuario.php" class="btn btn-light mt-2">Voltar para o painel</a>
        </div>
    </main>
</body>
</html>
