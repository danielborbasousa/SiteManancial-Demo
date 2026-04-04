<?php
session_start(); // Inicia a sessão do usuário
include("conexao.php"); // Inclui o arquivo de conexão com o banco de dados
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}
// Módulo da página principal (dashboard)

// Buscar vídeos do banco de dados
$sql_videos = "SELECT * FROM ID_CONTENT WHERE IDCT_TIPO = 'VIDEO' OR IDCT_TIPO = 'video' ORDER BY IDCT_ORDEM";
$resultado_videos = mysqli_query($conn, $sql_videos);
$videos = array();
if($resultado_videos && mysqli_num_rows($resultado_videos) > 0) {
    while($video = mysqli_fetch_assoc($resultado_videos)) {
        $videos[] = $video;
    }
}

// Remove vídeos com URL inválida e ajusta caminho local para execução via /php/
$videos_validos = array();
foreach($videos as $video_item) {
    $url_original = isset($video_item["IDCT_URL"]) ? trim($video_item["IDCT_URL"]) : "";

    if($url_original == "") {
        continue;
    }

    if(strpos($url_original, "videos/") === 0) {
        $url_publica = "../" . $url_original;
        if(file_exists($url_publica)) {
            $video_item["IDCT_URL"] = $url_publica;
            $videos_validos[] = $video_item;
        }
        continue;
    }

    if(strpos($url_original, "../videos/") === 0) {
        if(file_exists($url_original)) {
            $videos_validos[] = $video_item;
        }
        continue;
    }

    if(strpos($url_original, "http://") === 0 || strpos($url_original, "https://") === 0) {
        $videos_validos[] = $video_item;
    }
}
$videos = $videos_validos;

// Fallback para demonstracao local quando nao houver video valido no banco
if(count($videos) == 0 && file_exists("../videos/Neymar.MP4")) {
    $videos[] = array(
        "IDCT_TITULO" => "Video de teste",
        "IDCT_DESCRICAO" => "Arquivo local para demonstracao",
        "IDCT_URL" => "../videos/Neymar.MP4"
    );
}

// Define o primeiro video valido para iniciar no mini player
$video_inicial = null;
foreach($videos as $video_item) {
    if(isset($video_item["IDCT_URL"]) && $video_item["IDCT_URL"] != "") {
        $video_inicial = $video_item;
        break;
    }
}

// Mantem o padrao de feed com varios cards
$videos_feed = $videos;
while(count($videos_feed) < 5) {
    $videos_feed[] = array(
        "IDCT_TITULO" => "Conteudo em breve",
        "IDCT_DESCRICAO" => "Novo video sera publicado em breve",
        "IDCT_URL" => ""
    );
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Missão Evangélica Manancial da Esperança</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark position-absolute w-100 p-3">
        <!-- Barra de navegação com logo e links -->
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#"><img src="../assets/logo.png" alt="Logotipo da Missão Evangélica Manancial da Esperança" class="logo me-2" /> Missão Evangélica Manancial da Esperança</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardusuario.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <div class="d-flex align-items-center gap-4">
                    <a href="busca.php" id="searchIcon" style="cursor: pointer; text-decoration: none;">🔍</a>
                    <a href="notificacoes.php" id="notifIcon" style="cursor: pointer; text-decoration: none;">🔔</a>
                    <a href="perfil.php" id="profileIcon" class="nav-icon" style="cursor: pointer; text-decoration: none; font-size: 1.25rem; line-height: 1; display: flex; align-items: center; justify-content: center;">👤</a>
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

            <?php if($video_inicial): ?>
                <div class="mb-3" style="max-width: 520px; background: #000; border-radius: 8px; overflow: hidden;">
                    <video id="miniPlayer" controls style="width: 100%; height: 200px; object-fit: cover;" src="<?php echo htmlspecialchars($video_inicial['IDCT_URL']); ?>">
                        Seu navegador não suporta vídeo HTML5.
                    </video>
                    <div class="p-2">
                        <small id="miniTitulo" class="text-white fw-bold d-block"><?php echo htmlspecialchars($video_inicial['IDCT_TITULO']); ?></small>
                        <small id="miniDescricao" class="text-info"><?php echo htmlspecialchars($video_inicial['IDCT_DESCRICAO']); ?></small>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">Nenhum vídeo disponível no momento.</div>
            <?php endif; ?>

            <div class="stream-carousel">
                <?php foreach($videos_feed as $video): ?>
                    <?php
                        $url_video = isset($video['IDCT_URL']) ? $video['IDCT_URL'] : '';
                        $titulo_video = isset($video['IDCT_TITULO']) ? $video['IDCT_TITULO'] : 'Video';
                        $descricao_video = isset($video['IDCT_DESCRICAO']) ? $video['IDCT_DESCRICAO'] : '';
                        $tem_video = ($url_video != '');
                    ?>
                    <div class="stream-item" style="min-width: 230px;">
                        <div class="stream-thumbnail" style="height: 120px; <?php echo $tem_video ? 'cursor:pointer;' : 'opacity:0.7;'; ?>"
                            <?php if($tem_video): ?>
                            onclick="var p=document.getElementById('miniPlayer'); if(p){p.src='<?php echo htmlspecialchars($url_video); ?>'; p.load();} document.getElementById('miniTitulo').innerText='<?php echo htmlspecialchars(addslashes($titulo_video)); ?>'; document.getElementById('miniDescricao').innerText='<?php echo htmlspecialchars(addslashes($descricao_video)); ?>';"
                            <?php endif; ?>>
                            <?php if($tem_video): ?>▶<?php else: ?>Em breve<?php endif; ?>
                            <div class="play-circle"><div class="play-triangle"></div></div>
                        </div>

                        <div class="mt-2">
                            <h6 class="mb-1 fw-bold" style="font-size: 13px;"><?php echo htmlspecialchars($titulo_video); ?></h6>
                            <?php if($tem_video): ?>
                                <a href="assistir_video.php?url=<?php echo urlencode($url_video); ?>&titulo=<?php echo urlencode($titulo_video); ?>&descricao=<?php echo urlencode($descricao_video); ?>" class="text-info" style="font-size:12px;">Abrir vídeo</a>
                            <?php else: ?>
                                <small class="text-muted" style="font-size:12px;">Aguardando novo conteúdo</small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>