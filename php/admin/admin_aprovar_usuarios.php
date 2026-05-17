<?php
include("../conexao.php");

auth_require(array('admin'));

$mensagem = "";
$erro = "";

// Processar aprovação/rejeição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = isset($_POST["usuario_id"]) ? (int) $_POST["usuario_id"] : 0;
    $acao = isset($_POST["acao"]) ? trim($_POST["acao"]) : "";
    $motivo = isset($_POST["motivo"]) ? trim($_POST["motivo"]) : "";

    if ($usuario_id <= 0 || !in_array($acao, array('aprovar', 'rejeitar', 'remover_permissao', 'dar_permissao'))) {
        $erro = "Requisição inválida.";
    } else {
        // Buscar dados do usuário
        $sql_user = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL FROM ID_FIEL WHERE IDF_ID = $usuario_id LIMIT 1";
        $res_user = mysqli_query($conn, $sql_user);
        
        if ($res_user && mysqli_num_rows($res_user) > 0) {
            $user = mysqli_fetch_assoc($res_user);
            
            // Mapear ação para novo status
            $novo_status = '';
            if ($acao === 'aprovar' || $acao === 'dar_permissao') {
                $novo_status = 'aprovado';
            } elseif ($acao === 'rejeitar' || $acao === 'remover_permissao') {
                $novo_status = 'negado';
            }
            
            // Atualizar status do usuário
            $sql_update = "UPDATE ID_FIEL SET IDF_STATUS = '$novo_status' WHERE IDF_ID = $usuario_id LIMIT 1";
            if (mysqli_query($conn, $sql_update)) {
                // Atualizar solicitação de acesso
                $admin_id = isset($_SESSION["Usuario_id"]) ? (int) $_SESSION["Usuario_id"] : 0;
                $motivo_esc = mysqli_real_escape_string($conn, $motivo);
                $sql_sol = "UPDATE ID_SOLICITACAO_ACESSO SET IDSA_STATUS = '$novo_status', IDSA_ADMIN_ID = $admin_id, IDSA_MOTIVO_NEGACAO = '$motivo_esc', IDSA_RESPONDIDO_EM = NOW() WHERE IDF_ID = $usuario_id LIMIT 1";
                @mysqli_query($conn, $sql_sol);
                
                // Criar notificação para o usuário
                if ($novo_status === 'aprovado') {
                    $notif_titulo = "Acesso Aprovado!";
                    $notif_msg = "Sua solicitação de acesso foi aprovada! Acesse a plataforma com suas credenciais.";
                } else {
                    $notif_titulo = "Acesso Negado";
                    $notif_msg = "Sua solicitação de acesso foi negada." . (!empty($motivo) ? " Motivo: $motivo_esc" : "");
                }
                $sql_notif = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO) VALUES ($usuario_id, '$notif_titulo', '$notif_msg', 'resposta_acesso')";
                @mysqli_query($conn, $sql_notif);
                
                // Mensagem de sucesso
                if ($acao === 'aprovar' || $acao === 'dar_permissao') {
                    $mensagem = "Usuário aprovado com sucesso!";
                } else {
                    $mensagem = "Usuário rejeitado com sucesso!";
                }
            } else {
                $erro = "Erro ao processar solicitação.";
            }
        } else {
            $erro = "Usuário não encontrado.";
        }
    }
}

// Buscar usuários pendentes
$sql_pendentes = "
    SELECT 
        f.IDF_ID, f.IDF_NOME, f.IDF_EMAIL, f.IDF_TELEFONE, f.IDF_CPF, 
        f.IDF_FILIAL_ID, f.IDF_FUNCAO, f.IDF_ENDERECO, 
        f.IDF_CRIADO_EM, f.IDF_STATUS,
        fil.IDL_NOME
    FROM ID_FIEL f
    LEFT JOIN ID_FILIAL fil ON f.IDF_FILIAL_ID = fil.IDL_ID
    WHERE f.IDF_STATUS = 'pendente'
    ORDER BY f.IDF_CRIADO_EM DESC
";
$res_pendentes = mysqli_query($conn, $sql_pendentes);
$usuarios_pendentes = array();
if ($res_pendentes && mysqli_num_rows($res_pendentes) > 0) {
    while ($user = mysqli_fetch_assoc($res_pendentes)) {
        $usuarios_pendentes[] = $user;
    }
}

// Buscar histórico de aprovações/rejeições
$sql_historico = "
    SELECT 
        f.IDF_ID, f.IDF_NOME, f.IDF_EMAIL, f.IDF_STATUS,
        sa.IDSA_RESPONDIDO_EM, sa.IDSA_MOTIVO_NEGACAO,
        a.IDF_NOME as ADMIN_NOME
    FROM ID_FIEL f
    LEFT JOIN ID_SOLICITACAO_ACESSO sa ON f.IDF_ID = sa.IDF_ID
    LEFT JOIN ID_ADMIN ad ON sa.IDSA_ADMIN_ID = ad.IDA_ID
    LEFT JOIN ID_FIEL a ON ad.IDA_FIEL_ID = a.IDF_ID
    WHERE f.IDF_STATUS IN ('aprovado', 'negado')
    ORDER BY sa.IDSA_RESPONDIDO_EM DESC
    LIMIT 50
";
$res_historico = mysqli_query($conn, $sql_historico);
$usuarios_historico = array();
if ($res_historico && mysqli_num_rows($res_historico) > 0) {
    while ($user = mysqli_fetch_assoc($res_historico)) {
        $usuarios_historico[] = $user;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovar Usuários - Admin</title>
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
                <h1 class="mb-4"><i class="fas fa-user-check me-2"></i>Aprovação de Usuários</h1>

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

                <!-- USUARIOS PENDENTES -->
                <div class="card mb-5" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                        <h5 class="mb-0"><i class="fas fa-hourglass-half me-2 text-warning"></i>Solicitações Pendentes (<?php echo count($usuarios_pendentes); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($usuarios_pendentes) > 0) { ?>
                            <div class="row g-3">
                                <?php foreach ($usuarios_pendentes as $user) { ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="border rounded p-3" style="border-color: var(--border-color);">
                                            <div class="mb-3">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($user["IDF_NOME"]); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($user["IDF_EMAIL"]); ?></small>
                                            </div>
                                            <div class="mb-3">
                                                <table class="table table-sm mb-0" style="font-size: 0.85rem;">
                                                    <tr>
                                                        <td><strong>CPF:</strong></td>
                                                        <td><?php echo htmlspecialchars($user["IDF_CPF"]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Telefone:</strong></td>
                                                        <td><?php echo htmlspecialchars($user["IDF_TELEFONE"]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Função:</strong></td>
                                                        <td><?php echo htmlspecialchars($user["IDF_FUNCAO"]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Filial:</strong></td>
                                                        <td><?php echo htmlspecialchars($user["IDL_NOME"] ?? "N/A"); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Solicitado em:</strong></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($user["IDF_CRIADO_EM"])); ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted"><strong>Endereço:</strong></small>
                                                <small class="d-block text-muted"><?php echo htmlspecialchars($user["IDF_ENDERECO"]); ?></small>
                                            </div>
                                            <div class="d-flex gap-2 mt-3">
                                                <button type="button" class="btn btn-sm btn-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $user["IDF_ID"]; ?>">
                                                    <i class="fas fa-check me-1"></i>Aprovar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger flex-grow-1" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo $user["IDF_ID"]; ?>">
                                                    <i class="fas fa-times me-1"></i>Rejeitar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Aprovar -->
                                    <div class="modal fade" id="approveModal<?php echo $user["IDF_ID"]; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                    <h5 class="modal-title">Aprovar Usuário</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <p>Deseja aprovar o acesso de <strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong>?</p>
                                                        <p class="text-muted small">O usuário receberá uma notificação e poderá acessar a plataforma.</p>
                                                    </div>
                                                    <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-success" name="acao" value="aprovar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $user["IDF_ID"]; ?>">
                                                            Aprovar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Rejeitar -->
                                    <div class="modal fade" id="rejectModal<?php echo $user["IDF_ID"]; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                    <h5 class="modal-title">Rejeitar Acesso</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <p>Rejeitar o acesso de <strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong>?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Motivo (opcional):</label>
                                                            <textarea class="form-control custom-input" name="motivo" rows="3" placeholder="Informe o motivo da rejeição..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-danger" name="acao" value="rejeitar">
                                                            <input type="hidden" name="usuario_id" value="<?php echo $user["IDF_ID"]; ?>">
                                                            Rejeitar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else { ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle text-success" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p class="mt-3 text-muted">Nenhuma solicitação pendente!</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- HISTÓRICO -->
                <div class="card" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Histórico de Decisões</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($usuarios_historico) > 0) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr style="border-bottom: 1px solid var(--border-color);">
                                            <th>Usuário</th>
                                            <th>E-mail</th>
                                            <th>Status</th>
                                            <th>Respondido em</th>
                                            <th>Por</th>
                                            <th>Motivo</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios_historico as $user) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user["IDF_NOME"]); ?></td>
                                                <td><small><?php echo htmlspecialchars($user["IDF_EMAIL"]); ?></small></td>
                                                <td>
                                                    <?php if ($user["IDF_STATUS"] === "aprovado") { ?>
                                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aprovado</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Rejeitado</span>
                                                    <?php } ?>
                                                </td>
                                                <td><small><?php echo $user["IDSA_RESPONDIDO_EM"] ? date('d/m/Y H:i', strtotime($user["IDSA_RESPONDIDO_EM"])) : "N/A"; ?></small></td>
                                                <td><small><?php echo htmlspecialchars($user["ADMIN_NOME"] ?? "Sistema"); ?></small></td>
                                                <td><small><?php echo htmlspecialchars($user["IDSA_MOTIVO_NEGACAO"] ?? "-"); ?></small></td>
                                                <td>
                                                    <?php if ($user["IDF_STATUS"] === "aprovado") { ?>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#removerPermissaoModal<?php echo $user["IDF_ID"]; ?>" title="Remover Permissão">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    <?php } else { ?>
                                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#darPermissaoModal<?php echo $user["IDF_ID"]; ?>" title="Dar Permissão">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- MODAIS DE AÇÃO DO HISTÓRICO -->
                            <?php foreach ($usuarios_historico as $user) { ?>
                                <!-- Modal Remover Permissão -->
                                <div class="modal fade" id="removerPermissaoModal<?php echo $user["IDF_ID"]; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                <h5 class="modal-title">Remover Permissão</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <p>Deseja remover a permissão de <strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong>?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Motivo (opcional):</label>
                                                        <textarea class="form-control custom-input" name="motivo" rows="3" placeholder="Informe o motivo da revogação..."></textarea>
                                                    </div>
                                                    <div class="alert alert-warning" role="alert">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <small>O usuário será notificado sobre a remoção de permissão.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger" name="acao" value="remover_permissao">
                                                        <input type="hidden" name="usuario_id" value="<?php echo $user["IDF_ID"]; ?>">
                                                        <i class="fas fa-ban me-1"></i>Remover
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Dar Permissão -->
                                <div class="modal fade" id="darPermissaoModal<?php echo $user["IDF_ID"]; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                                                <h5 class="modal-title">Dar Permissão</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <p>Deseja dar permissão para <strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong> acessar a plataforma?</p>
                                                    <div class="alert alert-info" role="alert">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <small>O usuário receberá uma notificação sobre a aprovação.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success" name="acao" value="dar_permissao">
                                                        <input type="hidden" name="usuario_id" value="<?php echo $user["IDF_ID"]; ?>">
                                                        <i class="fas fa-check me-1"></i>Aprovar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center py-5">
                                <p class="text-muted">Nenhum histórico disponível</p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

