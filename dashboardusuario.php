<?php
session_start(); // Inicia a sessão do usuário
include("conexao.php"); // Inclui o arquivo de conexão com o banco de dados
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}
// Módulo da página principal (dashboard)
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Missão Evangélica Manancial da Esperança</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark position-absolute w-100 p-3">
        <!-- Barra de navegação com logo e links -->
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#"><img src="logo.png" alt="Logotipo da Missão Evangélica Manancial da Esperança" class="logo me-2" /> Missão Evangélica Manancial da Esperança</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardusuario.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <div class="d-flex align-items-center gap-4">
                    <a href="busca.php" id="searchIcon" style="cursor: pointer; text-decoration: none;">🔍</a>
                    <a href="notificacoes.php" id="notifIcon" style="cursor: pointer; text-decoration: none;">🔔</a>
                    <a href="perfil.php" id="profileIcon" class="nav-icon" style="cursor: pointer; text-decoration: none;"></a>
                    <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
                </div>
            </div>
        </div>
    </nav>
<br><br>
    <header class="hero-section px-5">
        <!-- Seção principal (hero) com título e botões -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 z-1">
                    <h1 class="display-4 fw-bold mb-3">Bem-vindo à nossa plataforma</h1>
                    <h4 class="text-info mb-4">Vídeos • Mensagens • Estudos</h4>
                    <p class="lead text-light opacity-75 mb-4">
                        Explore nossos cursos e mensagens vindas do globo da igreja. Navegue pelo conteúdo exclusivo da Missão Evangélica Manancial da Esperança.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#videos" class="btn btn-light fw-bold px-4 py-2">▶ Assistir</a>
                        <a href="sobre.php" class="btn btn-outline-light fw-bold px-4 py-2">ⓘ Mais informacoes</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="videos" class="container-fluid px-5 py-4">
        <!-- Conteúdo principal da página -->
        
        <section class="mb-5">
            <!-- Seção de vídeos em destaque -->
            <h5 class="mb-3 fw-bold">Vídeos em destaque</h5>
            <div class="stream-carousel">
                
                <div class="stream-item">
                    <div class="stream-thumbnail active">
                        IMGEXEMPLO
                        <div class="play-circle"><div class="play-triangle"></div></div>
                    </div>
                    <div class="progress mt-2 rounded-0 bg-secondary" style="height: 4px;">
                        <div class="progress-bar bg-white w-50" role="progressbar"></div>
                    </div>
                    <div class="mt-2">
                        <h6 class="mb-0 fw-bold">Lorem Ipsum Lorem Ipsum</h6>
                        <small class="text-info opacity-75">Lorem Ipsum</small>
                    </div>
                </div>

                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                
            </div>
        </section>

        <section>
            <!-- Seção de outros vídeos -->
            <h5 class="mb-3 fw-bold">Outros vídeos</h5>
            <div class="stream-carousel">
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
                <div class="stream-item" role="button" aria-label="Vídeo">
                    <div class="stream-thumbnail">Vídeo</div>
                </div>
            </div>
        </section>
    </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>