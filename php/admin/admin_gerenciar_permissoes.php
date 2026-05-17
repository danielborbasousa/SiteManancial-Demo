<?php
include("../conexao.php");

auth_require(array('admin'));

$mensagem = "";
$erro = "";

// Processar mudança de permissão
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = isset($_POST["usuario_id"]) ? (int) $_POST["usuario_id"] : 0;
    $acao = isset($_POST["acao"]) ? trim($_POST["acao"]) : "";

    if ($usuario_id <= 0 || !in_array($acao, array('promover', 'remover'))) {
        $erro = "Requisição inválida.";
    } else {
        // Buscar dados do usuário
        $sql_user = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL FROM ID_FIEL WHERE IDF_ID = $usuario_id LIMIT 1";
        $res_user = mysqli_query($conn, $sql_user);
        
        if ($res_user && mysqli_num_rows($res_user) > 0) {
            $user = mysqli_fetch_assoc($res_user);
            
            if ($acao === 'promover') {
                // Verificar se já é admin
                $sql_check = "SELECT IDA_ID FROM ID_ADMIN WHERE IDA_FIEL_ID = $usuario_id LIMIT 1";
                $res_check = mysqli_query($conn, $sql_check);
                
                if ($res_check && mysqli_num_rows($res_check) > 0) {
                    $erro = "Este usuário já é administrador.";
                } else {
                    // Promover a admin
                    $sql_promote = "INSERT INTO ID_ADMIN (IDA_FIEL_ID, IDA_NIVEL, IDA_ATIVO) VALUES ($usuario_id, 'EDITOR', 1)";
                    if (mysqli_query($conn, $sql_promote)) {
                        $mensagem = "Usuário '" . htmlspecialchars($user["IDF_NOME"]) . "' promovido a ADMIN com sucesso!";
                        
                        // Criar notificação para o usuário
                        $notif_titulo = "Você foi promovido a Administrador!";
                        $notif_msg = "Parabéns! Você agora tem acesso ao painel administrativo. Faça login novamente para acessar.";
                        $sql_notif = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO) VALUES ($usuario_id, '$notif_titulo', '$notif_msg', 'promocao')";
                        @mysqli_query($conn, $sql_notif);
                    } else {
                        $erro = "Erro ao promover usuário: " . mysqli_error($conn);
                    }
                }
            } else if ($acao === 'remover') {
                // Verificar se é admin
                $sql_check = "SELECT IDA_ID FROM ID_ADMIN WHERE IDA_FIEL_ID = $usuario_id LIMIT 1";
                $res_check = mysqli_query($conn, $sql_check);
                
                if (!$res_check || mysqli_num_rows($res_check) == 0) {
                    $erro = "Este usuário não é administrador.";
                } else {
                    $admin_row = mysqli_fetch_assoc($res_check);
                    $admin_id = (int) $admin_row["IDA_ID"];
                    
                    // Remover permissão de admin
                    $sql_remove = "DELETE FROM ID_ADMIN WHERE IDA_ID = $admin_id LIMIT 1";
                    if (mysqli_query($conn, $sql_remove)) {
                        $mensagem = "Permissão de administrador removida de '" . htmlspecialchars($user["IDF_NOME"]) . "' com sucesso!";
                        
                        // Criar notificação
                        $notif_titulo = "Permissões alteradas";
                        $notif_msg = "Sua permissão de administrador foi removida. Você continuará tendo acesso como usuário regular.";
                        $sql_notif = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO) VALUES ($usuario_id, '$notif_titulo', '$notif_msg', 'permissao_alterada')";
                        @mysqli_query($conn, $sql_notif);
                    } else {
                        $erro = "Erro ao remover permissão: " . mysqli_error($conn);
                    }
                }
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}

// Buscar usuários e seu status de admin
$usuarios = array();
$sql_users = "
    SELECT 
        f.IDF_ID, f.IDF_NOME, f.IDF_EMAIL, f.IDF_STATUS, f.IDF_FUNCAO,
        CASE WHEN a.IDA_ID IS NOT NULL THEN 1 ELSE 0 END as eh_admin,
        a.IDA_NIVEL
    FROM ID_FIEL f
    LEFT JOIN ID_ADMIN a ON f.IDF_ID = a.IDA_FIEL_ID
    WHERE f.IDF_STATUS = 'aprovado'
    ORDER BY f.IDF_NOME
";
$res_users = mysqli_query($conn, $sql_users);
if ($res_users && mysqli_num_rows($res_users) > 0) {
    while ($user = mysqli_fetch_assoc($res_users)) {
        $usuarios[] = $user;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Permissões - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo site_url('php/admin/admin_conteudos.php'); ?>">
                <img src="../../assets/logo.png" alt="Logotipo" class="logo me-2" /> Admin Panel
            </a>
            <div class="d-flex gap-2 align-items-center ms-auto">
                <div class="theme-toggle-container">
                    <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
                    <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
                    <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
                </div>
                <a href="dashboard.php" class="btn btn-sm btn-outline-light">Voltar ao Painel</a>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-5 px-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4"><i class="fas fa-shield-alt me-2"></i>Gerenciar Permissões de Administrador</h1>
                <p class="text-muted mb-4">Promova usuários a administrador ou remova suas permissões</p>

                <?php if ($mensagem !== "") { ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($mensagem); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <?php if ($erro !== "") { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($erro); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <!-- LISTA DE USUÁRIOS -->
                <div class="card" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Usuários (<?php echo count($usuarios); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($usuarios) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead style="border-bottom: 2px solid var(--border-color);">
                                        <tr>
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>Função</th>
                                            <th>Status</th>
                                            <th>Permissão</th>
                                            <th>Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $user) { ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong></td>
                                                <td><small><?php echo htmlspecialchars($user["IDF_EMAIL"]); ?></small></td>
                                                <td><?php echo htmlspecialchars($user["IDF_FUNCAO"]); ?></td>
                                                <td>
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aprovado</span>
                                                </td>
                                                <td>
                                                    <?php if ($user["eh_admin"] == 1) { ?>
                                                        <span class="badge bg-warning" style="color: #000;">
                                                            <i class="fas fa-shield-alt me-1"></i><?php echo htmlspecialchars($user["IDA_NIVEL"]); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-user me-1"></i>Usuário
                                                        </span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($user["eh_admin"] == 1) { ?>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#removeModal<?php echo $user["IDF_ID"]; ?>">
                                                            <i class="fas fa-times me-1"></i>Remover Admin
                                                        </button>
                                                    <?php } else { ?>
                                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#promoteModal<?php echo $user["IDF_ID"]; ?>">
                                                            <i class="fas fa-arrow-up me-1"></i>Promover
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>

                                            <!-- Modal Promover -->
                                            <div class="modal fade" id="promoteModal<?php echo $user["IDF_ID"]; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                                        <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                            <h5 class="modal-title">Promover a Administrador</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <p>Deseja promover <strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong> a administrador?</p>
                                                                <p class="text-muted small">O usuário terá acesso completo ao painel administrativo e poderá gerenciar conteúdos, usuários e permissões.</p>
                                                            </div>
                                                            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-success" name="acao" value="promover">
                                                                    <input type="hidden" name="usuario_id" value="<?php echo $user["IDF_ID"]; ?>">
                                                                    Confirmar Promoção
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Remover -->
                                            <div class="modal fade" id="removeModal<?php echo $user["IDF_ID"]; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                                        <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                            <h5 class="modal-title">Remover Permissão de Admin</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST">
                                                            <div class="modal-body">
                                                                <p>Deseja remover as permissões de administrador de <strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong>?</p>
                                                                <p class="text-muted small">O usuário continuará tendo acesso à plataforma como usuário regular, mas perderá acesso ao painel administrativo.</p>
                                                            </div>
                                                            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-danger" name="acao" value="remover">
                                                                    <input type="hidden" name="usuario_id" value="<?php echo $user["IDF_ID"]; ?>">
                                                                    Remover Permissão
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p class="mt-3 text-muted">Nenhum usuário aprovado disponível</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- INFORMAÇÕES -->
                <div class="alert alert-info mt-4" style="background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.2);">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Níveis de Permissão:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Usuário:</strong> Acesso à plataforma de conteúdo (vídeos, cursos)</li>
                        <li><strong>EDITOR:</strong> Acesso ao painel admin, gerenciamento de conteúdos</li>
                        <li><strong>SUPER:</strong> Acesso total (apenas atribuir manualmente no banco)</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
