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
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Todos os Vídeos - Missão Manancial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
</head>
<body style="transition: background 0.3s ease;">
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3" style="background: rgba(7,16,29,0.98); border-bottom: 1px solid var(--border-color); transition: all 0.3s ease;">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo site_url('php/fiel/dashboard.php'); ?>">
                <img src="../../assets/logo.png" alt="Logotipo" class="logo me-2" /> Missão Manancial
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?php echo site_url('php/fiel/dashboard.php'); ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="#">Todos os Vídeos</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center">
                    <div class="theme-toggle-container">
                        <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
                        <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
                        <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-5 px-4">
        <div class="row">
            <div class="col-12">
                <div class="mb-4">
                    <h1 class="mb-2"><i class="fas fa-film me-2" style="color: #60a5fa;"></i>Biblioteca de Vídeos</h1>
                    <p class="text-muted">Total de <?php echo $total_videos; ?> vídeos disponíveis</p>
                </div>

                <!-- GRID DE VÍDEOS -->
                <?php if (count($videos) > 0) { ?>
                    <div class="row g-4 mb-5">
                        <?php foreach ($videos as $video) { ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <a href="assistir_video.php?id=<?php echo (int) $video["IDCT_ID"]; ?>" style="text-decoration: none; color: inherit;">
                                    <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: all 0.3s ease; cursor: pointer; height: 100%; display: flex; flex-direction: column;" onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 40px rgba(96,165,250,0.2)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                        
                                        <!-- THUMBNAIL -->
                                        <div style="height: 180px; background: #000; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle, rgba(96,165,250,0.2) 0%, transparent 70%);"></div>
                                            <i class="fas fa-play-circle" style="font-size: 3.5rem; color: #60a5fa; z-index: 2;"></i>
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
                                            <button class="btn btn-sm btn-light w-100" style="background: linear-gradient(135deg, #60a5fa, #a78bfa); border: none; color: white;">
                                                <i class="fas fa-play me-1"></i>Assistir
                                            </button>
                                        </div>
                                    </div>
                                </a>
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
                        <a href="<?php echo site_url('php/fiel/dashboard.php'); ?>" class="btn btn-light mt-3">
                            <i class="fas fa-arrow-left me-1"></i>Voltar ao Dashboard
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
