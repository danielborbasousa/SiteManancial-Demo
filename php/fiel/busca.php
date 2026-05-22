<?php
session_start();
include("../conexao.php");
auth_require();

$busca = "";
$encontrou = false;
$resultado = false;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $busca = trim($_POST["busca"] ?? "");
    $busca_db = mysqli_real_escape_string($conn, $busca);
    $sql = "SELECT * FROM ID_CONTENT WHERE IDCT_TITULO LIKE '%$busca_db%' OR IDCT_DESCRICAO LIKE '%$busca_db%' ORDER BY IDCT_ID DESC";
    $resultado = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missão Manancial | Buscar Conteúdo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <style>
        .search-result-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-main);
        }

        .btn-ver-video {
            background: #0b2f6b;
            border: 1px solid #1e4d9b;
            color: #fff;
            font-weight: 600;
        }

        .btn-ver-video:hover,
        .btn-ver-video:focus {
            background: #123f8f;
            border-color: #2f63b8;
            color: #fff;
        }
    </style>
</head>
<body>
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

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-4">Buscar conteúdo</h1>
            
            <form method="POST" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="busca" class="form-control custom-input" placeholder="Digite o titulo ou descricao..." value="<?php echo $busca; ?>">
                    <button class="btn btn-light" type="submit">Buscar</button>
                </div>
            </form>

            <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if($resultado && mysqli_num_rows($resultado) > 0) {
            ?>
                <div>
                    <h5>Resultados encontrados:</h5>
                    <div class="list-group mt-3">
                        <?php 
                        while($conteudo = mysqli_fetch_assoc($resultado)) {
                            $tipo = strtoupper((string) ($conteudo['IDCT_TIPO'] ?? ''));
                            $eh_video = (strtolower((string) ($conteudo['IDCT_TIPO'] ?? '')) === 'video');
                            $link_video = "assistir_video.php?id=" . (int) ($conteudo['IDCT_ID'] ?? 0);
                        ?>
                            <div class="list-group-item list-group-item-action search-result-item">
                                <h6 class="mb-1"><?php echo htmlspecialchars((string) ($conteudo['IDCT_TITULO'] ?? 'Sem título')); ?></h6>
                                <p class="mb-2"><?php echo htmlspecialchars((string) ($conteudo['IDCT_DESCRICAO'] ?? '')); ?></p>
                                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                    <small>Tipo: <?php echo htmlspecialchars($tipo); ?></small>
                                    <?php if ($eh_video) { ?>
                                        <a href="<?php echo htmlspecialchars($link_video); ?>" class="btn btn-sm btn-ver-video">
                                            <i class="fas fa-play me-1"></i>Ver Vídeo
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php 
                        }
                        ?>
                    </div>
                </div>
            <?php 
                } else {
            ?>
                <div class="alert alert-warning">
                    Nenhum conteúdo encontrado para "<?php echo htmlspecialchars($busca); ?>".
                </div>
            <?php 
                }
            }
            ?>

                    <!-- back button removed (now in navbar) -->
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/theme.js"></script>
</body>
</html>
