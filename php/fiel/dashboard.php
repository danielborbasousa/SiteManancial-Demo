<?php
include("../conexao.php");

auth_require();

// Redirecionar admin para seu painel
if (isset($_SESSION["Usuario_tipo"]) && $_SESSION["Usuario_tipo"] === "admin") {
    header("location: ../admin/dashboard.php");
    exit;
}

// Buscar vídeos
$sql_videos = "SELECT IDCT_ID, IDCT_TITULO, IDCT_DESCRICAO, IDCT_URL FROM ID_CONTENT WHERE LOWER(IDCT_TIPO) = 'video' ORDER BY IDCT_ID DESC LIMIT 12";
$resultado_videos = mysqli_query($conn, $sql_videos);
$videos = array();
if($resultado_videos && mysqli_num_rows($resultado_videos) > 0) {
    while($video = mysqli_fetch_assoc($resultado_videos)) {
        if(isset($video["IDCT_URL"]) && strpos($video["IDCT_URL"], "videos/") === 0) {
            if(file_exists("../../" . $video["IDCT_URL"])) {
                $videos[] = $video;
            }
        } else {
            $videos[] = $video;
        }
    }
}

$videos_recentes = array_slice($videos, 0, 3);
$videos_restantes = max(0, count($videos) - count($videos_recentes));

// Estatísticas do usuário
$usuario_nome = $_SESSION["Usuario_nome"] ?? "Usuário";
$usuario_email = $_SESSION["Usuario_logado"] ?? "";
$eh_admin = isset($_SESSION["Usuario_tipo"]) && $_SESSION["Usuario_tipo"] === "admin";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missão Manancial | Início</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
    <style>
        .hero-dashboard { background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(16,185,129,0.05) 100%); padding: 3rem 2rem; border-radius: 16px; margin-bottom: 3rem; transition: all 0.3s ease; }
        .video-card { background: rgba(255,255,255,0.04); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: all 0.3s; cursor: pointer; }
        .video-card:hover { background: rgba(96,165,250,0.1); border-color: var(--primary-light); transform: translateY(-8px); box-shadow: 0 12px 30px rgba(96,165,250,0.2); }
        .video-thumbnail { position: relative; height: 160px; background: #000; display: flex; align-items: center; justify-content: center; color: var(--primary-light); overflow: hidden; }
        .video-thumbnail video { width: 100%; height: 100%; object-fit: cover; display: block; background: #0b1220; }
        .video-preview-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(7,16,29,0.05), rgba(7,16,29,0.28)); z-index: 1; pointer-events: none; }
        .video-play-btn { position: absolute; font-size: 2.5rem; opacity: 0.8; transition: all 0.3s; }
        .video-card:hover .video-play-btn { opacity: 1; transform: scale(1.2); }
        .video-play-link { position: absolute; inset: 0; z-index: 3; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .video-play-link:hover { text-decoration: none; }
        .video-play-link:focus-visible { outline: 2px solid var(--primary-light); outline-offset: -4px; }
        .video-info { padding: 1.5rem; }
        .video-title { font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .video-desc { font-size: 0.9rem; color: var(--text-muted); display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .quick-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-box { background: rgba(96,165,250,0.08); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; text-align: center; transition: all 0.3s ease; }
        .stat-box:hover { background: rgba(96,165,250,0.15); }
        .stat-number { font-size: 1.8rem; font-weight: 700; color: var(--primary-light); }
        .stat-label { font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem; }
        /* navbar styling is controlled by css/styles.css to follow the selected theme */
        .nav-link { transition: color 0.3s ease; }
        .nav-link:hover { color: var(--primary-light); }

        .footer-faith-panel {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(96,165,250,0.15);
            border-radius: 18px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .footer-faith-panel::before,
        .footer-faith-panel::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .footer-faith-panel::before {
            width: 180px;
            height: 180px;
            background: rgba(96,165,250,0.12);
            top: -70px;
            right: -40px;
            animation: footerFloat 8s ease-in-out infinite;
        }

        .footer-faith-panel::after {
            width: 120px;
            height: 120px;
            background: rgba(16,185,129,0.12);
            bottom: -40px;
            left: -30px;
            animation: footerFloat 10s ease-in-out infinite reverse;
        }

        .footer-faith-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 1.5rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .footer-faith-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-faith-brand img {
            width: 58px;
            height: 58px;
            object-fit: contain;
            filter: drop-shadow(0 0 18px rgba(96,165,250,0.35));
        }

        .footer-faith-title {
            margin: 0;
            color: #60a5fa;
            font-weight: 700;
        }

        .footer-faith-icons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .footer-faith-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(96,165,250,0.18);
            color: #93c5fd;
            font-size: 1.5rem;
            box-shadow: 0 0 0 rgba(96,165,250,0.0);
            animation: footerPulse 2.8s ease-in-out infinite;
        }

        .footer-faith-icon:nth-child(2) { animation-delay: 0.35s; }
        .footer-faith-icon:nth-child(3) { animation-delay: 0.7s; }
        .footer-faith-icon:nth-child(4) { animation-delay: 1.05s; }

        .footer-faith-verse {
            margin-top: 1rem;
            padding: 1rem 1.2rem;
            border-left: 3px solid #60a5fa;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            color: var(--text-muted);
        }

        .footer-faith-bible {
            font-size: 0.92rem;
            color: #93c5fd;
            margin-top: 0.4rem;
        }

        @keyframes footerPulse {
            0%, 100% { transform: translateY(0) scale(1); box-shadow: 0 0 0 0 rgba(96,165,250,0.0); }
            50% { transform: translateY(-4px) scale(1.04); box-shadow: 0 10px 30px rgba(96,165,250,0.18); }
        }

        @keyframes footerFloat {
            0%, 100% { transform: translateY(0) translateX(0) scale(1); }
            50% { transform: translateY(-10px) translateX(6px) scale(1.04); }
        }

        @media (max-width: 768px) {
            .footer-faith-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-faith-brand {
                justify-content: center;
            }
        }

        :root[data-theme="light"] .hero-dashboard .text-white,
        :root[data-theme="light"] .hero-dashboard .lead,
        :root[data-theme="light"] section p.text-white,
        :root[data-theme="light"] .site-footer .text-muted,
        :root[data-theme="light"] .site-footer .text-muted a,
        :root[data-theme="light"] .site-footer .text-muted.text-decoration-none,
        :root[data-theme="light"] .site-footer p,
        :root[data-theme="light"] .site-footer li a {
            color: #0f172a !important;
        }
    </style>
</head>
<body style="transition: background 0.3s ease;">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="../../assets/logo.png" alt="Logo" style="height:50px; margin-right:1rem;">
                <span>Missão Manancial</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="fas fa-home me-2"></i>Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php"><i class="fas fa-info-circle me-2"></i>Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php"><i class="fas fa-envelope me-2"></i>Contato</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <a href="busca.php" class="text-decoration-none" style="font-size: 1.2rem; color: var(--primary-light);">
                        <i class="fas fa-search"></i>
                    </a>
                    
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
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars(substr($usuario_nome, 0, 10)); ?>
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

    <main class="container-fluid px-4 py-5" style="max-width: 1400px; margin: 0 auto;">
        <!-- HERO SECTION -->
        <div class="hero-dashboard">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>! </h1>
                    <p class="lead text-white mb-0">
                        Explore nossos vídeos, conteúdos exclusivos e fortaleça seu crescimento espiritual na comunidade.
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div style="display: flex; justify-content: flex-end; gap: 1rem; flex-wrap: wrap;">
                        <a href="sobre.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-info-circle me-2"></i>Sobre a Missão
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ESTATÍSTICAS RÁPIDAS -->
        <div class="quick-stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo count($videos); ?></div>
                <div class="stat-label">Vídeos Disponíveis</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">∞</div>
                <div class="stat-label">Crescimento</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">100%</div>
                <div class="stat-label">Gratuito</div>
            </div>
        </div>

        <!-- SEÇÃO DE VÍDEOS -->
        <section class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-1"><i class="fas fa-play-circle me-2" style="color: #60a5fa;"></i>Conteúdos Recentes</h2>
                    <p class="text-white">Nossos vídeos e aulas mais recentes para sua edificação espiritual</p>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                    <?php if ($videos_restantes > 0) { ?>
                        <span class="badge rounded-pill bg-danger px-3 py-2" style="font-size: 0.95rem;">+<?php echo $videos_restantes; ?></span>
                    <?php } ?>
                    <a href="todos_videos.php" class="btn btn-outline-light btn-sm">Ver Todos</a>
                </div>
            </div>

            <?php if (count($videos_recentes) > 0) { ?>
                <div class="row g-3">
                    <?php foreach ($videos_recentes as $video) { ?>
                        <?php
                            $tem_id = isset($video["IDCT_ID"]) && (int) $video["IDCT_ID"] > 0;
                            $link_video = $tem_id
                                ? "assistir_video.php?id=" . (int) $video["IDCT_ID"]
                                : "assistir_video.php?url=" . urlencode($video["IDCT_URL"] ?? "") . "&titulo=" . urlencode($video["IDCT_TITULO"] ?? "Vídeo") . "&descricao=" . urlencode($video["IDCT_DESCRICAO"] ?? "");
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
                        <div class="col-md-6 col-lg-4">
                            <div class="video-card">
                                <div class="video-thumbnail">
                                    <a href="<?php echo $link_video; ?>" class="video-play-link" aria-label="Assistir vídeo <?php echo htmlspecialchars($video["IDCT_TITULO"]); ?>">
                                    <?php if ($preview_url !== "") { ?>
                                        <video muted playsinline preload="metadata" onloadedmetadata="if (this.currentTime < 0.15) { try { this.currentTime = 0.15; } catch (e) {} } this.pause();">
                                            <source src="<?php echo htmlspecialchars($preview_url); ?>">
                                        </video>
                                        <div class="video-preview-overlay"></div>
                                    <?php } else { ?>
                                        <i class="fas fa-play video-play-btn"></i>
                                    <?php } ?>
                                    <i class="fas fa-play video-play-btn" style="z-index: 2;"></i>
                                    </a>
                                </div>
                                <div class="video-info">
                                    <div class="video-title"><?php echo htmlspecialchars($video["IDCT_TITULO"]); ?></div>
                                    <div class="video-desc"><?php echo htmlspecialchars($video["IDCT_DESCRICAO"]); ?></div>
                                    
                                    <a href="<?php echo $link_video; ?>" class="btn btn-sm btn-light mt-3 w-100">
                                        <i class="fas fa-play me-2"></i>Assistir Agora
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-video" style="font-size: 3rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Nenhum vídeo disponível no momento. Em breve teremos novos conteúdos!</p>
                </div>
            <?php } ?>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container-fluid px-4">
            <div class="footer-faith-panel">
                <div class="footer-faith-grid">
                    <div>
                        <div class="footer-faith-brand">
                            <img src="../../assets/logo.png" alt="Logo Missão Manancial">
                            <div>
                                <h6 class="footer-faith-title">Missão Evangélica</h6>
                                <p class="text-muted mb-0">Manancial da Esperança - Fortalecendo vidas através da fé, tecnologia e comunidade.</p>
                            </div>
                        </div>
                        <div class="footer-faith-verse">
                            <div><i class="fas fa-quote-left me-2" style="color:#60a5fa;"></i>“Lâmpada para os meus pés é a tua palavra e luz para o meu caminho.”</div>
                            <div class="footer-faith-bible">Salmo 119:105</div>
                        </div>
                    </div>
                    <div>
                        <div class="footer-faith-icons">
                            <span class="footer-faith-icon" title="Fé"><i class="fas fa-cross"></i></span>
                            <span class="footer-faith-icon" title="Luz"><i class="fas fa-sun"></i></span>
                            <span class="footer-faith-icon" title="Paz"><i class="fas fa-dove"></i></span>
                            <span class="footer-faith-icon" title="Esperança"><i class="fas fa-hands-praying"></i></span>
                        </div>
                        <p class="text-muted small mt-3 mb-0 text-center text-md-start">Que cada acesso à plataforma seja um momento de edificação, esperança e comunhão.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts após 5 segundos
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-info)').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 3000);
            });
        }, 500);
    </script>
</body>
</html>