<?php
session_start();
include("../conexao.php");

if (!isset($_SESSION["Usuario_logado"])) {
    header("location:../../login.php");
    exit;
}

if (!isset($_SESSION["Usuario_tipo"]) || $_SESSION["Usuario_tipo"] !== "admin") {
    header("location:../fiel/dashboard.php");
    exit;
}

// Buscar estatísticas
$stats = array(
    'total_usuarios' => 0,
    'usuarios_pendentes' => 0,
    'usuarios_aprovados' => 0,
    'total_videos' => 0,
    'total_cursos' => 0,
    'total_matriculas' => 0,
    'taxa_conclusao' => 0
);

// Total de usuários
$sql = "SELECT COUNT(*) as cnt FROM ID_FIEL";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['total_usuarios'] = $row['cnt'];
}

// Usuários pendentes
$sql = "SELECT COUNT(*) as cnt FROM ID_FIEL WHERE IDF_STATUS = 'pendente'";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['usuarios_pendentes'] = $row['cnt'];
}

// Usuários aprovados
$sql = "SELECT COUNT(*) as cnt FROM ID_FIEL WHERE IDF_STATUS = 'aprovado'";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['usuarios_aprovados'] = $row['cnt'];
}

// Total de vídeos
$sql = "SELECT COUNT(*) as cnt FROM ID_CONTENT WHERE LOWER(IDCT_TIPO) = 'video'";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['total_videos'] = $row['cnt'];
}

// Total de cursos
$sql = "SELECT COUNT(*) as cnt FROM ID_CURSO";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['total_cursos'] = $row['cnt'];
}

// Total de matrículas
$sql = "SELECT COUNT(*) as cnt FROM ID_MATRICULA";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['total_matriculas'] = $row['cnt'];
}

// Taxa de conclusão média
$sql = "SELECT AVG(IDMATR_PERCENTUAL) as media FROM ID_MATRICULA WHERE IDMATR_PERCENTUAL > 0";
$res = mysqli_query($conn, $sql);
if ($res && $row = mysqli_fetch_assoc($res)) {
    $stats['taxa_conclusao'] = round($row['media'] ?? 0, 1);
}

// Usuários recentes
$usuarios_recentes = array();
$sql = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_STATUS, IDF_CRIADO_EM FROM ID_FIEL ORDER BY IDF_CRIADO_EM DESC LIMIT 5";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $usuarios_recentes[] = $row;
    }
}

// Usuários pendentes
$usuarios_pendentes = array();
$sql = tabela_tem_coluna($conn, 'ID_FIEL', 'IDF_FILIAL_ID') && tabela_tem_coluna($conn, 'ID_FIEL', 'IDF_CRIADO_EM')
    ? "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FUNCAO, IDF_ENDERECO, IDF_STATUS, IDF_ATIVO, IDF_FILIAL_ID, IDF_CRIADO_EM, IDF_FILIAL FROM ID_FIEL WHERE IDF_STATUS = 'pendente' ORDER BY IDF_CRIADO_EM DESC LIMIT 10"
    : "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FUNCAO, IDF_ENDERECO, IDF_STATUS, IDF_ATIVO, IDF_FILIAL AS IDL_NOME, NULL AS IDF_CRIADO_EM FROM ID_FIEL WHERE IDF_STATUS = 'pendente' ORDER BY IDF_ID DESC LIMIT 10";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $usuarios_pendentes[] = $row;
    }
}

// Vídeos recentes
$videos_recentes = array();
$sql = "SELECT IDCT_ID, IDCT_TITULO, IDCT_CRIADO_EM FROM ID_CONTENT WHERE LOWER(IDCT_TIPO) = 'video' ORDER BY IDCT_CRIADO_EM DESC LIMIT 5";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $videos_recentes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Missão Evangélica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
    <style>
        :root {
            --admin-primary: #1e40af;
            --admin-primary-light: #3b82f6;
            --admin-accent: #f59e0b;
            --admin-success: #10b981;
            --admin-danger: #ef4444;
            --admin-warning: #f59e0b;
        }

        body {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a2a4e 0%, #0f1b35 100%);
            border-right: 1px solid rgba(96, 165, 250, 0.2);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            padding-top: 2rem;
            z-index: 1000;
            animation: slideInLeft 0.5s ease-out;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 8px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(96, 165, 250, 0.2);
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-logo img {
            height: 40px;
            width: auto;
        }

        .sidebar-logo h5 {
            margin: 0;
            font-weight: 800;
            color: var(--primary-light);
            font-size: 1.1rem;
        }

        .sidebar-logo small {
            color: var(--text-muted);
            display: block;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu-item {
            animation: slideInLeft 0.5s ease-out backwards;
        }

        .sidebar-menu-item:nth-child(1) { animation-delay: 0.2s; }
        .sidebar-menu-item:nth-child(2) { animation-delay: 0.25s; }
        .sidebar-menu-item:nth-child(3) { animation-delay: 0.3s; }
        .sidebar-menu-item:nth-child(4) { animation-delay: 0.35s; }
        .sidebar-menu-item:nth-child(5) { animation-delay: 0.4s; }
        .sidebar-menu-item:nth-child(6) { animation-delay: 0.45s; }
        .sidebar-menu-item:nth-child(7) { animation-delay: 0.5s; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 1rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin: 0.25rem 0.75rem;
            border-radius: 8px;
            position: relative;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover {
            color: var(--text-main);
            background: rgba(96, 165, 250, 0.1);
            padding-left: 2rem;
        }

        .sidebar-link:hover i {
            transform: translateX(3px);
        }

        .sidebar-link.active {
            color: var(--primary-light);
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.2), transparent);
            border-left: 3px solid var(--primary-light);
            padding-left: calc(1.5rem - 3px);
        }

        /* Main Content */
        .admin-main {
            margin-left: 280px;
            flex: 1;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-light) 100%);
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            background: rgba(30, 41, 59, 0.8);
            border-bottom: 1px solid rgba(96, 165, 250, 0.2);
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            animation: fadeInDown 0.5s ease-out;
        }

        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-content {
            flex: 1;
            padding: 2rem 1.5rem;
            overflow-y: auto;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(96, 165, 250, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.3s; }
        .stat-card:nth-child(2) { animation-delay: 0.35s; }
        .stat-card:nth-child(3) { animation-delay: 0.4s; }
        .stat-card:nth-child(4) { animation-delay: 0.45s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.55s; }
        .stat-card:nth-child(7) { animation-delay: 0.6s; }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.1), transparent);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(96, 165, 250, 0.5);
            background: rgba(30, 41, 59, 0.8);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.15);
        }

        .stat-card:hover::before {
            right: -20%;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            color: var(--text-main);
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
        }

        .stat-change {
            color: var(--accent);
            font-size: 0.85rem;
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Action Cards */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(21, 34, 56, 0.5));
            border: 1px solid rgba(96, 165, 250, 0.3);
            border-radius: 12px;
            padding: 1.75rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .action-card:nth-child(1) { animation-delay: 0.65s; }
        .action-card:nth-child(2) { animation-delay: 0.7s; }
        .action-card:nth-child(3) { animation-delay: 0.75s; }
        .action-card:nth-child(4) { animation-delay: 0.8s; }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(96, 165, 250, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-6px);
            border-color: rgba(59, 130, 246, 0.6);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.2);
        }

        .action-card:hover::before {
            opacity: 1;
        }

        .action-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .action-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-light));
            transition: transform 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.1);
        }

        .action-title {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1.1rem;
        }

        .action-desc {
            color: var(--text-muted);
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }

        .action-cta {
            align-self: flex-start;
            color: var(--primary-light);
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            z-index: 1;
            transition: gap 0.3s ease;
        }

        .action-card:hover .action-cta {
            gap: 0.75rem;
        }

        /* Data Tables */
        .data-table {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(96, 165, 250, 0.2);
            border-radius: 12px;
            overflow: hidden;
            margin-top: 1rem;
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }

        .data-table-header {
            background: rgba(15, 27, 53, 0.8);
            border-bottom: 1px solid rgba(96, 165, 250, 0.3);
            padding: 1rem 1.5rem;
        }

        .data-table-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .data-table table {
            width: 100%;
            margin: 0;
        }

        .data-table th {
            background: rgba(30, 41, 59, 0.8);
            border-bottom: 1px solid rgba(96, 165, 250, 0.2);
            padding: 1rem 1.5rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            border-bottom: 1px solid rgba(96, 165, 250, 0.1);
            padding: 1rem 1.5rem;
            color: var(--text-main);
        }

        .data-table tbody tr {
            transition: background 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(96, 165, 250, 0.05);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-approved {
            background: rgba(16, 185, 129, 0.2);
            color: var(--accent);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: var(--admin-warning);
        }

        .status-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: var(--admin-danger);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 240px;
            }

            .admin-main {
                margin-left: 240px;
            }

            .admin-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .stat-value {
                font-size: 2rem;
            }

            .action-grid {
                grid-template-columns: 1fr;
            }

            .admin-header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        @media (max-width: 576px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: 200px;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Theme Specific Colors */
        .stat-card.primary { --icon-bg: linear-gradient(135deg, #3b82f6, #60a5fa); }
        .stat-card.success { --icon-bg: linear-gradient(135deg, #10b981, #34d399); }
        .stat-card.warning { --icon-bg: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .stat-card.danger { --icon-bg: linear-gradient(135deg, #ef4444, #f87171); }
    </style>
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="../../assets/logo.png" alt="Logo" />
                <div>
                    <h5>Admin Panel</h5>
                    <small>Missão Evangélica</small>
                </div>
            </div>
        </div>

        <nav>
            <ul class="sidebar-menu px-2">
                <li class="sidebar-menu-item">
                    <a href="dashboard.php" class="sidebar-link active">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin_conteudos.php" class="sidebar-link">
                        <i class="fas fa-video"></i>
                        <span>Gerenciar Vídeos</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin_aprovar_usuarios.php" class="sidebar-link">
                        <i class="fas fa-user-check"></i>
                        <span>Aprovar Usuários</span>
                        <?php if ($stats['usuarios_pendentes'] > 0): ?>
                            <span class="badge bg-danger position-absolute" style="right: 1rem;">
                                <?php echo $stats['usuarios_pendentes']; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin_visao_usuario.php" class="sidebar-link">
                        <i class="fas fa-tasks"></i>
                        <span>Ver Progresso</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin_gerenciar_permissoes.php" class="sidebar-link">
                        <i class="fas fa-lock"></i>
                        <span>Permissões</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="admin_gerenciar_usuarios.php" class="sidebar-link">
                        <i class="fas fa-cog"></i>
                        <span>Configurações</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="../fiel/sair.php" class="sidebar-link" style="margin-top: auto;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">
        <!-- HEADER -->
        <header class="admin-header">
            <div class="admin-header-content">
                <div>
                    <h1>
                        <i class="fas fa-chart-pie"></i>
                        Dashboard
                    </h1>
                </div>
                <div class="admin-header-actions">
                    <div class="theme-toggle-container">
                        <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
                        <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
                        <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="admin-content">

            <!-- STATISTICS GRID -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-label">Total de Usuários</div>
                    <div class="stat-value"><?php echo $stats['total_usuarios']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i>
                        <?php echo $stats['usuarios_aprovados']; ?> aprovados
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-label">Pendentes de Aprovação</div>
                    <div class="stat-value"><?php echo $stats['usuarios_pendentes']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-exclamation-circle"></i>
                        Requer ação
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-label">Total de Vídeos</div>
                    <div class="stat-value"><?php echo $stats['total_videos']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-check-circle"></i>
                        Disponíveis
                    </div>
                </div>

                <div class="stat-card primary">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-label">Total de Cursos</div>
                    <div class="stat-value"><?php echo $stats['total_cursos']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-check"></i>
                        Cadastrados
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-label">Matrículas Ativas</div>
                    <div class="stat-value"><?php echo $stats['total_matriculas']; ?></div>
                    <div class="stat-change">
                        <i class="fas fa-users-cog"></i>
                        Em andamento
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-label">Taxa de Conclusão</div>
                    <div class="stat-value"><?php echo $stats['taxa_conclusao']; ?>%</div>
                    <div class="stat-change">
                        <i class="fas fa-trending-up"></i>
                        Média de progresso
                    </div>
                </div>
            </div>

            <!-- ACTION CARDS -->
            <h3 style="color: var(--text-main); margin-bottom: 1.5rem; font-weight: 700; animation: fadeInUp 0.6s ease-out 0.5s both;">
                <i class="fas fa-bolt me-2"></i>Ações Rápidas
            </h3>
            <div class="action-grid">
                <a href="admin_conteudos.php" class="action-card">
                    <div class="action-card-header">
                        <div class="action-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <div class="action-title">Novo Vídeo</div>
                        </div>
                    </div>
                    <div class="action-desc">Adicione um novo conteúdo em vídeo para os cursos</div>
                    <div class="action-cta">
                        Ir para Vídeos
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="admin_aprovar_usuarios.php" class="action-card">
                    <div class="action-card-header">
                        <div class="action-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <div class="action-title">Aprovar Usuários</div>
                        </div>
                    </div>
                    <div class="action-desc">Revise e aprove <?php echo $stats['usuarios_pendentes']; ?> solicitação(ões) pendente(s)</div>
                    <div class="action-cta">
                        Gerenciar Aprovações
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="admin_visao_usuario.php" class="action-card">
                    <div class="action-card-header">
                        <div class="action-icon" style="background: linear-gradient(135deg, #10b981, #34d399);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="action-title">Ver Progresso</div>
                        </div>
                    </div>
                    <div class="action-desc">Acompanhe o progresso dos usuários nos cursos</div>
                    <div class="action-cta">
                        Visualizar Progresso
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="admin_gerenciar_permissoes.php" class="action-card">
                    <div class="action-card-header">
                        <div class="action-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <div class="action-title">Permissões</div>
                        </div>
                    </div>
                    <div class="action-desc">Configure os acessos e permissões dos usuários</div>
                    <div class="action-cta">
                        Gerenciar Permissões
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>

            <!-- PENDING NOTIFICATIONS -->
            <?php if (count($usuarios_pendentes) > 0): ?>
                <div class="data-table" style="margin-top: 2rem; border-left: 4px solid var(--admin-warning);">
                    <div class="data-table-header">
                        <p class="data-table-title">
                            <i class="fas fa-bell"></i>
                            Solicitações Pendentes (<?php echo count($usuarios_pendentes); ?>)
                        </p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Função</th>
                                <th>Data</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios_pendentes as $user): ?>
                                <tr style="background: rgba(245, 158, 11, 0.05);">
                                    <td><strong><?php echo htmlspecialchars($user['IDF_NOME']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['IDF_EMAIL']); ?></td>
                                    <td><?php echo htmlspecialchars($user['IDF_FUNCAO'] ?? 'N/A'); ?></td>
                                    <td><?php echo !empty($user['IDF_CRIADO_EM']) ? date('d/m/Y H:i', strtotime($user['IDF_CRIADO_EM'])) : 'N/A'; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#verDadosModal<?php echo $user['IDF_ID']; ?>" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                            <i class="fas fa-eye me-1"></i>Ver Dados
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Ver Dados -->
                                <div class="modal fade" id="verDadosModal<?php echo $user['IDF_ID']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                <h5 class="modal-title"><i class="fas fa-user me-2"></i>Dados do Usuário - <?php echo htmlspecialchars($user['IDF_NOME']); ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Nome:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_NOME']); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>E-mail:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_EMAIL']); ?></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Telefone:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_TELEFONE'] ?? 'N/A'); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>CPF:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_CPF'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Função:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_FUNCAO'] ?? 'N/A'); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Data de Solicitação:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo !empty($user['IDF_CRIADO_EM']) ? date('d/m/Y H:i', strtotime($user['IDF_CRIADO_EM'])) : 'N/A'; ?></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Endereço:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_ENDERECO'] ?? 'N/A'); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Status:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDF_STATUS'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Ativo:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo !empty($user['IDF_ATIVO']) ? 'Sim' : 'Não'; ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Filial:</strong><br>
                                                        <span style="color: var(--text-muted);"><?php echo htmlspecialchars($user['IDL_NOME'] ?? $user['IDF_FILIAL'] ?? 'N/A'); ?></span>
                                                    </div>
                                                </div>

                                                <hr style="border-color: var(--border-color);">

                                                <div class="alert alert-info" role="alert">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Verifique os dados acima com os registros da filial para confirmar a identidade do usuário.
                                                </div>
                                            </div>
                                            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                                <a href="admin_aprovar_usuarios.php" class="btn btn-primary">
                                                    <i class="fas fa-check me-2"></i>Ir para Aprovações
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- RECENT USERS -->
            <?php if (count($usuarios_recentes) > 0): ?>
                <div class="data-table" style="margin-top: 2rem;">
                    <div class="data-table-header">
                        <p class="data-table-title">
                            <i class="fas fa-user-plus"></i>
                            Usuários Recentes
                        </p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios_recentes as $user): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($user['IDF_NOME']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($user['IDF_EMAIL']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $user['IDF_STATUS']; ?>">
                                            <i class="fas fa-<?php echo $user['IDF_STATUS'] === 'aprovado' ? 'check-circle' : 'hourglass-half'; ?>"></i>
                                            <?php echo ucfirst($user['IDF_STATUS']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($user['IDF_CRIADO_EM'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- RECENT VIDEOS -->
            <?php if (count($videos_recentes) > 0): ?>
                <div class="data-table">
                    <div class="data-table-header">
                        <p class="data-table-title">
                            <i class="fas fa-film"></i>
                            Vídeos Recentes
                        </p>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Data de Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos_recentes as $video): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($video['IDCT_TITULO']); ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($video['IDCT_CRIADO_EM'])); ?></td>
                                    <td>
                                        <a href="admin_editar_conteudo.php?id=<?php echo $video['IDCT_ID']; ?>" class="btn btn-sm btn-outline-light" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add fade-down animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
