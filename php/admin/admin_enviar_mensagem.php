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
    $destino_tipo = isset($_POST["destino_tipo"]) ? trim($_POST["destino_tipo"]) : "";
    $destinos = isset($_POST["destinos"]) && is_array($_POST["destinos"]) ? $_POST["destinos"] : array();
    $titulo = trim($_POST["titulo"] ?? "");
    $mensagem_texto = trim($_POST["mensagem"] ?? "");
    $tipo = trim($_POST["tipo"] ?? "info");

    if ($titulo === "" || $mensagem_texto === "") {
        $erro = "Preencha o título e a mensagem.";
    } elseif (!in_array($tipo, array('info', 'sucesso', 'aviso', 'erro'), true)) {
        $erro = "Tipo de mensagem inválido.";
    } elseif ($destino_tipo === "") {
        $erro = "Selecione um destino (ex: Todos os fiéis, Admins, Usuários ou Selecionados).";
    } else {
        $titulo_db = mysqli_real_escape_string($conn, $titulo);
        $mensagem_db = mysqli_real_escape_string($conn, $mensagem_texto);
        $tipo_db = mysqli_real_escape_string($conn, $tipo);

        // todos os fiéis / todos os usuários (aprovados e ativos)
        if ($destino_tipo === 'todos' || $destino_tipo === 'todos_fieis' || $destino_tipo === 'todos_usuarios') {
            $sql = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO)\n                    SELECT IDF_ID, '$titulo_db', '$mensagem_db', '$tipo_db'\n                    FROM ID_FIEL\n                    WHERE IDF_STATUS = 'aprovado' AND IDF_ATIVO = 1";
            if (mysqli_query($conn, $sql)) {
                $qtde = mysqli_affected_rows($conn);
                $mensagem = "Mensagem enviada com sucesso para " . ($qtde > 0 ? $qtde : 0) . " usuário(s).";
            } else {
                $erro = "Erro ao enviar mensagem: " . mysqli_error($conn);
            }

        } elseif ($destino_tipo === 'todos_admins') {
            $sql = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO)\n                    SELECT f.IDF_ID, '$titulo_db', '$mensagem_db', '$tipo_db'\n                    FROM ID_ADMIN a JOIN ID_FIEL f ON f.IDF_ID = a.IDA_FIEL_ID\n                    WHERE a.IDA_ATIVO = 1 AND f.IDF_STATUS = 'aprovado' AND f.IDF_ATIVO = 1";
            if (mysqli_query($conn, $sql)) {
                $qtde = mysqli_affected_rows($conn);
                $mensagem = "Mensagem enviada com sucesso para " . ($qtde > 0 ? $qtde : 0) . " admin(s).";
            } else {
                $erro = "Erro ao enviar mensagem: " . mysqli_error($conn);
            }

        } elseif ($destino_tipo === 'selecionados') {
            if (empty($destinos)) {
                $erro = "Selecione pelo menos um usuário.";
            } else {
                $enviadas = 0;
                foreach ($destinos as $d) {
                    $uid = (int) $d;
                    if ($uid <= 0) continue;
                    $sql_verifica = "SELECT IDF_ID FROM ID_FIEL WHERE IDF_ID = $uid AND IDF_STATUS = 'aprovado' AND IDF_ATIVO = 1 LIMIT 1";
                    $res_verifica = mysqli_query($conn, $sql_verifica);
                    if ($res_verifica && mysqli_num_rows($res_verifica) > 0) {
                        $sql_ins = "INSERT INTO ID_NOTIFICACAO (IDF_ID, IDN_TITULO, IDN_MENSAGEM, IDN_TIPO) VALUES ($uid, '$titulo_db', '$mensagem_db', '$tipo_db')";
                        if (mysqli_query($conn, $sql_ins)) {
                            $enviadas++;
                        }
                    }
                }
                $mensagem = "Mensagem enviada com sucesso para " . $enviadas . " usuário(s) selecionados.";
            }
        } else {
            $erro = "Destino inválido.";
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
                        <div class="mb-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="destino_tipo" id="dest_todos_fieis" value="todos_fieis" checked>
                                <label class="form-check-label" for="dest_todos_fieis">Todos os fiéis</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="destino_tipo" id="dest_todos_admins" value="todos_admins">
                                <label class="form-check-label" for="dest_todos_admins">Todos os admins</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="destino_tipo" id="dest_todos_usuarios" value="todos_usuarios">
                                <label class="form-check-label" for="dest_todos_usuarios">Todos os usuários</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="destino_tipo" id="dest_selecionados" value="selecionados">
                                <label class="form-check-label" for="dest_selecionados">Selecionar indivíduos</label>
                            </div>
                        </div>

                        <div id="lista-selecionados" class="border rounded p-2 bg-dark" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-white-50">Clique nos nomes para selecionar (lista rolável)</small>
                                <div>
                                    <button type="button" id="btn-marcar-todos" class="btn btn-sm btn-outline-light">Marcar todos</button>
                                </div>
                            </div>
                            <div class="scroll-list" style="max-height:260px; overflow:auto;">
                                <?php foreach ($usuarios as $usuario) { ?>
                                    <label class="d-flex align-items-center justify-content-between p-2 mb-1 border rounded user-item" data-uid="<?php echo (int) $usuario['IDF_ID']; ?>" style="cursor:pointer;">
                                        <div class="text-white">
                                            <?php echo htmlspecialchars($usuario['IDF_NOME']); ?>
                                            <div class="text-white-50 small"><?php echo htmlspecialchars($usuario['IDF_EMAIL']); ?></div>
                                        </div>
                                        <input type="checkbox" name="destinos[]" value="<?php echo (int) $usuario['IDF_ID']; ?>" class="form-check-input ms-2 d-none">
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
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

            <style>
                #lista-selecionados .user-item.is-selected { background: rgba(99,102,241,0.12); border-color: rgba(99,102,241,0.25); }
                #lista-selecionados .user-item .form-check-input { width:18px; height:18px; }
            </style>

            <script>
                (function(){
                    const rdSelecionados = document.getElementById('dest_selecionados');
                    const rdTodosFieis = document.getElementById('dest_todos_fieis');
                    const rdTodosAdmins = document.getElementById('dest_todos_admins');
                    const rdTodosUsuarios = document.getElementById('dest_todos_usuarios');
                    const lista = document.getElementById('lista-selecionados');
                    const btnMarcarTodos = document.getElementById('btn-marcar-todos');

                    function updateListaVisibility(){
                        if (rdSelecionados.checked) {
                            lista.style.display = '';
                        } else {
                            lista.style.display = 'none';
                            // clear selections when hiding
                            document.querySelectorAll('#lista-selecionados .user-item').forEach(function(it){ it.classList.remove('is-selected'); var cb = it.querySelector('input[type=checkbox]'); if(cb) cb.checked = false; });
                        }
                    }

                    [rdSelecionados, rdTodosFieis, rdTodosAdmins, rdTodosUsuarios].forEach(function(r){ r.addEventListener('change', updateListaVisibility); });

                    // clicking a label toggles selection and checkbox
                    document.querySelectorAll('#lista-selecionados .user-item').forEach(function(item){
                        item.addEventListener('click', function(e){
                            // ignore clicks on inner checkbox (if visible)
                            const cb = item.querySelector('input[type=checkbox]');
                            if (cb) {
                                cb.checked = !cb.checked;
                                item.classList.toggle('is-selected', cb.checked);
                            }
                        });
                    });

                    btnMarcarTodos.addEventListener('click', function(){
                        const items = document.querySelectorAll('#lista-selecionados .user-item');
                        let allSelected = true;
                        items.forEach(function(it){ const cb = it.querySelector('input[type=checkbox]'); if(cb && !cb.checked) allSelected = false; });
                        items.forEach(function(it){ const cb = it.querySelector('input[type=checkbox]'); if(cb){ cb.checked = !allSelected; it.classList.toggle('is-selected', !allSelected); } });
                    });

                    // initialize visibility
                    updateListaVisibility();
                })();
            </script>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
