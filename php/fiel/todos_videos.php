<?php
include("../conexao.php");

auth_require();

$pagina = isset($_GET["pagina"]) ? max(1, (int) $_GET["pagina"]) : 1;
$itens_por_pagina = 12;
$offset = ($pagina - 1) * $itens_por_pagina;

// Buscar total de vídeos
$sql_total = "SELECT COUNT(*) as total FROM ID_CONTENT WHERE LOWER(IDCT_TIPO) = 'video' AND IDCT_PUBLICADO = 1";
$res_total = mysqli_query($conn, $sql_total);
$total_videos = 0;
if ($res_total && mysqli_num_rows($res_total) > 0) {
    $row = mysqli_fetch_assoc($res_total);
    $total_videos = (int) $row["total"];
}
$total_paginas = ceil($total_videos / $itens_por_pagina);

// Buscar vídeos com paginação
$videos = array();
$sql_videos = "
    SELECT IDCT_ID, IDCT_TITULO, IDCT_DESCRICAO, IDC_ID, IDCT_URL, IDCT_CRIADO_EM, IDCT_ORDEM
    FROM ID_CONTENT 
    WHERE LOWER(IDCT_TIPO) = 'video' AND IDCT_PUBLICADO = 1
    ORDER BY IDCT_CRIADO_EM DESC
    LIMIT $offset, $itens_por_pagina
";
$res_videos = mysqli_query($conn, $sql_videos);
if ($res_videos && mysqli_num_rows($res_videos) > 0) {
    while ($video = mysqli_fetch_assoc($res_videos)) {
        $videos[] = $video;
    }
}

// Buscar cursos para filtro
$cursos = array();
$sql_cursos = "SELECT IDC_ID, IDC_TITULO FROM ID_CURSO ORDER BY IDC_TITULO";
$res_cursos = mysqli_query($conn, $sql_cursos);
if ($res_cursos && mysqli_num_rows($res_cursos) > 0) {
    while ($curso = mysqli_fetch_assoc($res_cursos)) {
        $cursos[] = $curso;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missão Manancial | Vídeos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
</head>
<body style="transition: background 0.3s ease;">
    <?php $usuario_nome = $_SESSION['Usuario_nome'] ?? 'Usuário'; ?>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="../../assets/logo.png" alt="Logo" style="height:50px; margin-right:1rem;" />
                <span>Missão Manancial</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php"><i class="fas fa-info-circle me-2"></i>Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php"><i class="fas fa-envelope me-2"></i>Contato</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <a href="dashboard.php" class="btn btn-sm btn-outline-light">Voltar ao Painel</a>
                    <a href="busca.php" class="text-decoration-none" style="font-size: 1.2rem; color: var(--primary-light);"><i class="fas fa-search"></i></a>
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
                    <div class="theme-toggle-container">
                        <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
                        <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
                        <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars(substr($usuario_nome, 0, 12)); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-circle me-2"></i>Meu Perfil</a></li>
                            <li><a class="dropdown-item" href="notificacoes.php"><i class="fas fa-bell me-2"></i>Notificações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="sair.php"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-5 px-4">
        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <h1 class="mb-2"><i class="fas fa-film me-2" style="color: #60a5fa;"></i>Biblioteca de vídeos</h1>
                    <p class="text-muted">Total de <?php echo $total_videos; ?> vídeos disponíveis</p>
                </div>

                <!-- GRID DE VÍDEOS -->
                <?php if (count($videos) > 0) { ?>
                    <div class="row g-4 mb-5">
                        <?php foreach ($videos as $video) { ?>
                            <?php
                                $preview_url = "";
                                if (!empty($video["IDCT_URL"])) {
                                    if (strpos($video["IDCT_URL"], "videos/") === 0) {
                                        $arquivo_local = "../../" . $video["IDCT_URL"];
                                        if (file_exists($arquivo_local)) {
                                            $preview_url = $arquivo_local;
                                        }
                                    } else {
                                        $preview_url = $video["IDCT_URL"];
                                    }
                                }
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                        <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;" onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 40px rgba(96,165,250,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                        
                                        <!-- THUMBNAIL -->
                                        <div style="height: 180px; background: #000; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                            <?php if ($preview_url !== "") { ?>
                                                <video muted playsinline preload="metadata" style="width:100%; height:100%; object-fit:cover; display:block; background:#0b1220;" onloadedmetadata="if (this.currentTime < 0.15) { try { this.currentTime = 0.15; } catch (e) {} } this.pause();">
                                                    <source src="<?php echo htmlspecialchars($preview_url); ?>">
                                                </video>
                                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(180deg, rgba(7,16,29,0.05), rgba(7,16,29,0.28)); z-index: 1;"></div>
                                            <?php } else { ?>
                                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle, rgba(96,165,250,0.2) 0%, transparent 70%);"></div>
                                            <?php } ?>
                                            <i class="fas fa-play-circle" style="font-size: 3.5rem; color: #60a5fa; z-index: 2; position: relative;"></i>
                                        </div>

                                        <!-- CONTEÚDO -->
                                        <div style="padding: 1rem; flex-grow: 1; display: flex; flex-direction: column;">
                                            <h6 class="mb-2" style="font-weight: 700; color: var(--text-main); line-height: 1.3;">
                                                <?php echo htmlspecialchars(substr($video["IDCT_TITULO"], 0, 60)) . (strlen($video["IDCT_TITULO"]) > 60 ? "..." : ""); ?>
                                            </h6>
                                            
                                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: auto; min-height: 40px;">
                                                <?php echo htmlspecialchars(substr($video["IDCT_DESCRICAO"], 0, 80)) . (strlen($video["IDCT_DESCRICAO"]) > 80 ? "..." : ""); ?>
                                            </p>

                                            <div style="display: flex; gap: 0.5rem; font-size: 0.8rem; color: var(--text-muted); margin-top: 1rem;">
                                                <span><i class="fas fa-calendar-alt me-1"></i><?php echo date('d/m/Y', strtotime($video["IDCT_CRIADO_EM"])); ?></span>
                                            </div>
                                        </div>

                                        <!-- BOTÃO -->
                                        <div style="padding: 0.8rem; border-top: 1px solid var(--border-color);">
                                            <a href="assistir_video.php?id=<?php echo (int) $video["IDCT_ID"]; ?>" class="btn btn-sm btn-light w-100" style="background: linear-gradient(135deg, #60a5fa, #a78bfa); border: none; color: white;">
                                                <i class="fas fa-play me-1"></i>Assistir
                                            </a>
                                        </div>
                                    </div>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- PAGINAÇÃO -->
                    <?php if ($total_paginas > 1) { ?>
                        <nav aria-label="Paginação" class="mb-5">
                            <ul class="pagination justify-content-center">
                                <?php if ($pagina > 1) { ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>" style="background: var(--bg-light); border: 1px solid var(--border-color); color: var(--text-main);">
                                            <i class="fas fa-chevron-left me-1"></i>Anterior
                                        </a>
                                    </li>
                                <?php } ?>

                                <?php 
                                $inicio = max(1, $pagina - 2);
                                $fim = min($total_paginas, $pagina + 2);
                                
                                if ($inicio > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?pagina=1" style="background: var(--bg-light); border: 1px solid var(--border-color); color: var(--text-main);">1</a></li>';
                                    if ($inicio > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }

                                for ($i = $inicio; $i <= $fim; $i++) {
                                    $ativo = ($i == $pagina) ? ' active' : '';
                                    $bg = ($i == $pagina) ? 'background: linear-gradient(135deg, #60a5fa, #a78bfa); border-color: transparent;' : 'background: var(--bg-light); border: 1px solid var(--border-color);';
                                    echo "<li class=\"page-item$ativo\"><a class=\"page-link\" href=\"?pagina=$i\" style=\"$bg color: " . ($i == $pagina ? "white" : "var(--text-main)") . ";\">$i</a></li>";
                                }

                                if ($fim < $total_paginas) {
                                    if ($fim < $total_paginas - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"?pagina=$total_paginas\" style=\"background: var(--bg-light); border: 1px solid var(--border-color); color: var(--text-main);\">$total_paginas</a></li>";
                                }
                                ?>

                                <?php if ($pagina < $total_paginas) { ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>" style="background: var(--bg-light); border: 1px solid var(--border-color); color: var(--text-main);">
                                            Próxima<i class="fas fa-chevron-right ms-1"></i>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </nav>
                    <?php } ?>
                <?php } else { ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #60a5fa; opacity: 0.3;"></i>
                        <p class="mt-3 text-muted">Nenhum vídeo disponível no momento</p>
                        <!-- back button removed (now in navbar) -->
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
