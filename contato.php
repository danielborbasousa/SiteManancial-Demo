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
    <title>Contato - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboardusuario.php">
                <img src="logo.png" alt="Logotipo" class="logo me-2" /> Missao Evangelica Manancial da Esperança
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardusuario.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contato.php">Contato</a></li>
                </ul>
                <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-3">Contato</h1>
            <p class="mb-1"><strong>Email:</strong> contato@igreja.com</p>
            <p class="mb-1"><strong>Telefone:</strong> (11) 99999-9999</p>
            <p class="mb-3"><strong>Endereco:</strong> Rua Exemplo, 100 - Centro</p>

            <h5 class="mt-4">Formulario simples</h5>
            <form method="post" action="#" class="mt-3">
                <div class="mb-3">
                    <input type="text" class="form-control custom-input" placeholder="Seu nome" required>
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control custom-input" placeholder="Seu e-mail" required>
                </div>
                <div class="mb-3">
                    <textarea class="form-control custom-input" rows="4" placeholder="Sua mensagem" required></textarea>
                </div>
                <button type="submit" class="btn btn-light">Enviar</button>
            </form>
            <a href="dashboardusuario.php" class="btn btn-secondary mt-3">Voltar para o painel</a>
        </div>
    </main>
</body>
</html>
