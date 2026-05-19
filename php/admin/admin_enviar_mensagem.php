<?php
include("../conexao.php");

auth_require(array('admin'));

$mensagem = "";
$erro = "";
$admin_id_logado = isset($_SESSION["Usuario_id"]) ? (int) $_SESSION["Usuario_id"] : 0;

$usuarios = array();
$sql_usuarios = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL FROM ID_FIEL WHERE IDF_STATUS = 'aprovado' ORDER BY IDF_NOME";
$res_usuarios = mysqli_query($conn, $sql_usuarios);
if ($res_usuarios && mysqli_num_rows($res_usuarios) > 0) {
    while ($row = mysqli_fetch_assoc($res_usuarios)) {
        $usuarios[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["acao"]) && $_POST["acao"] === "enviar_mensagem") {
    $destino = isset($_POST["destino"]) ? trim($_POST["destino"]) : "";
    $titulo = trim($_POST["titulo"] ?? "");
    $mensagem_texto = trim($_POST["mensagem"] ?? "");
    $tipo = trim($_POST["tipo"] ?? "info");

    if ($titulo === "" || $mensagem_texto === "") {
        $erro = "Preencha o título e a mensagem.";
    } elseif (!in_array($tipo, array('info', 'sucesso', 'aviso', 'erro'), true)) {
        $erro = "Tipo de mensagem inválido.";
    } elseif ($destino === "") {
        $erro = "Selecione um fiel ou Todos os fiéis.";
    } else {
        $titulo_db = mysqli_real_escape_string($conn, $titulo);
        $mensagem_db = mysqli_real_escape_string($conn, $mensagem_texto);
        $tipo_db = mysqli_real_escape_string($conn, $tipo);

        if ($destino === 'todos') {
            $sql = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO)\n                    SELECT IDF_ID, '$titulo_db', '$mensagem_db', '$tipo_db'\n                    FROM ID_FIEL\n                    WHERE IDF_STATUS = 'aprovado' AND IDF_ATIVO = 1";
            if (mysqli_query($conn, $sql)) {
                $qtde = mysqli_affected_rows($conn);
                $mensagem = "Mensagem enviada com sucesso para " . ($qtde > 0 ? $qtde : 0) . " fiel(is).";
            } else {
                $erro = "Erro ao enviar mensagem: " . mysqli_error($conn);
            }
        } else {
            $usuario_id = (int) $destino;
            if ($usuario_id <= 0) {
                $erro = "Fiel inválido.";
            } else {
                $sql_verifica = "SELECT IDF_ID FROM ID_FIEL WHERE IDF_ID = $usuario_id AND IDF_STATUS = 'aprovado' LIMIT 1";
                $res_verifica = mysqli_query($conn, $sql_verifica);
                if ($res_verifica && mysqli_num_rows($res_verifica) > 0) {
                    $sql = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO) VALUES ($usuario_id, '$titulo_db', '$mensagem_db', '$tipo_db')";
                    if (mysqli_query($conn, $sql)) {
                        $mensagem = "Mensagem enviada com sucesso para o fiel selecionado.";
                    } else {
                        $erro = "Erro ao enviar mensagem: " . mysqli_error($conn);
                    }
                } else {
                    $erro = "Somente fiéis aprovados podem receber mensagens.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="mb-1">Enviar Mensagem</h1>
                    <p class="mb-0 text-muted">Envie uma notificação para um fiel específico ou para todos os aprovados.</p>
                </div>
            </div>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: #14532d; color: #000; border-color: #166534;">
                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($mensagem); ?>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <?php if ($erro !== "") { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($erro); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <form method="POST">
                <input type="hidden" name="acao" value="enviar_mensagem">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Destino</label>
                        <select name="destino" class="form-select custom-input" required>
                            <option value="">Selecione</option>
                            <option value="todos">Todos os fiéis</option>
                            <?php foreach ($usuarios as $usuario) { ?>
                                <option value="<?php echo (int) $usuario['IDF_ID']; ?>">
                                    <?php echo htmlspecialchars($usuario['IDF_NOME']); ?> (<?php echo htmlspecialchars($usuario['IDF_EMAIL']); ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Título</label>
                        <input type="text" name="titulo" class="form-control custom-input" maxlength="180" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mensagem</label>
                        <textarea name="mensagem" class="form-control custom-input" rows="5" required maxlength="5000"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select custom-input">
                            <option value="info">Informação</option>
                            <option value="sucesso">Sucesso</option>
                            <option value="aviso">Aviso</option>
                            <option value="erro">Erro</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                        </button>
                        <a href="dashboard.php" class="btn btn-outline-light">Voltar ao Painel</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
