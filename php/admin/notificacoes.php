<?php
include("../conexao.php");

auth_require(array('admin'));

$usuario_id = isset($_SESSION['Usuario_id']) ? (int) $_SESSION['Usuario_id'] : 0;
$notificacoes = array();
$total_notificacoes = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = trim($_POST['acao']);

    if ($acao === 'excluir_notificacao') {
        $notificacao_id = isset($_POST['notificacao_id']) ? (int) $_POST['notificacao_id'] : 0;
        if ($notificacao_id > 0 && $usuario_id > 0) {
            $sql_delete = "DELETE FROM ID_NOTIFICACAO WHERE IDN_ID = $notificacao_id AND IDF_ID = $usuario_id LIMIT 1";
            if (mysqli_query($conn, $sql_delete)) {
                header('Location: notificacoes.php');
                exit;
            }
        }
    } elseif ($acao === 'excluir_todas_notificacoes') {
        if ($usuario_id > 0) {
            $sql_delete_all = "DELETE FROM ID_NOTIFICACAO WHERE IDF_ID = $usuario_id";
            if (mysqli_query($conn, $sql_delete_all)) {
                header('Location: notificacoes.php');
                exit;
            }
        }
    }
}

if ($usuario_id > 0) {
    $sql_notificacoes = "SELECT IDN_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO, IDN_LIDA, IDN_CRIADO_EM FROM ID_NOTIFICACAO WHERE IDF_ID = $usuario_id ORDER BY IDN_CRIADO_EM DESC";
    $res_notificacoes = mysqli_query($conn, $sql_notificacoes);
    if ($res_notificacoes && mysqli_num_rows($res_notificacoes) > 0) {
        while ($row = mysqli_fetch_assoc($res_notificacoes)) {
            $notificacoes[] = $row;
        }
    }
    $total_notificacoes = count($notificacoes);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificações - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="container py-5">
        <div class="auth-container admin-notifications" style="max-width: 1000px; margin: 0 auto;">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
                <h1 class="mb-0">Notificações (<?php echo $total_notificacoes; ?>)</h1>
                <?php if ($total_notificacoes > 0) { ?>
                    <form method="POST" onsubmit="return confirm('Deseja apagar todas as notificações?');">
                        <input type="hidden" name="acao" value="excluir_todas_notificacoes">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash-alt me-2"></i>Apagar todas
                        </button>
                    </form>
                <?php } ?>
            </div>

            <?php if ($total_notificacoes > 0) { ?>
                <div class="list-group">
                    <?php foreach ($notificacoes as $notificacao) { ?>
                        <div class="list-group-item mb-3" style="background: var(--bg-light); border: 1px solid var(--border-color); color: var(--text-main);">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($notificacao['IDN_TITULO']); ?></h5>
                                    <p class="mb-2"><?php echo nl2br(htmlspecialchars($notificacao['IDN_MENSAGEM'] ?? '')); ?></p>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($notificacao['IDN_CRIADO_EM'])); ?></small>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($notificacao['IDN_TIPO']); ?></span>
                                    <form method="POST" onsubmit="return confirm('Deseja apagar esta notificação?');">
                                        <input type="hidden" name="acao" value="excluir_notificacao">
                                        <input type="hidden" name="notificacao_id" value="<?php echo (int) $notificacao['IDN_ID']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i>Apagar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="alert alert-info" role="alert" style="background:#ffffff !important; color:#000000 !important; border:1px solid rgba(0,0,0,0.08) !important;">
                    Você não tem notificações no momento.
                </div>
            <?php } ?>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/theme.js"></script>
</body>
</html>
