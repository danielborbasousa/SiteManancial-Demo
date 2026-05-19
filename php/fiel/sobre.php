<?php
session_start();
include("../conexao.php");
auth_require();

if(!isset($_SESSION["Usuario_logado"])) {
    header("location:../../login.php");
    exit;
}

$painel_link = "dashboard.php";
$titulo_nav = "Missao Evangelica Manancial da Esperança";
if(isset($_SESSION["Usuario_tipo"]) && $_SESSION["Usuario_tipo"] === "admin") {
    $painel_link = "../admin/admin_conteudos.php";
    $titulo_nav = "Painel Administrativo";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missão Manancial | Sobre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
                    <li class="nav-item"><a class="nav-link active" href="sobre.php"><i class="fas fa-info-circle me-2"></i>Sobre</a></li>
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
            <h1 class="mb-3">Sobre a missão</h1>
            <p>
                Esta plataforma foi criada para disponibilizar videos, mensagens e estudos da Missao Evangelica
                Manancial da Esperanca de forma simples e organizada.
            </p>
            <p>
                O objetivo e facilitar o acesso ao conteudo da igreja para membros e visitantes, usando uma
                interface facil de navegar.
            </p>
            <!-- back button removed (now in navbar) -->
        </div>
    </main>
</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/theme.js"></script>
</html>
