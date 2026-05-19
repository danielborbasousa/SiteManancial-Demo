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

$mensagem = "";
$erro = "";
// Caminho absoluto para a pasta de videos, evita problemas com paths relativos
$base_root = realpath(__DIR__ . '/../../');
if ($base_root !== false) {
    $upload_dir = $base_root . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR;
} else {
    $upload_dir = __DIR__ . '/../../videos/';
}

// Diagnóstico básico de upload
$upload_tmp_dir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"]) && $_POST["acao"] === "salvar") {
    $titulo = trim($_POST["IDCT_TITULO"]);
    $descricao = trim($_POST["IDCT_DESCRICAO"]);
    $ordem = (int) $_POST["IDCT_ORDEM"];
    $curso_id = (int) $_POST["IDC_ID"];
    $modulo_id = isset($_POST["IDM_ID"]) && $_POST["IDM_ID"] !== "" ? (int) $_POST["IDM_ID"] : null;
    $tipo = "video";
    $url_salva = "";

    if ($titulo === "") {
        $erro = "Informe o titulo do video.";
    } elseif (!isset($_FILES["arquivo"])) {
        $erro = "Nenhum arquivo foi enviado.";
    } elseif ($_FILES["arquivo"]["error"] !== UPLOAD_ERR_OK) {
        $code = $_FILES["arquivo"]["error"];
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $msg = "O arquivo não pôde ser enviado.";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $msg = "O arquivo não pôde ser enviado.";
                break;
            case UPLOAD_ERR_PARTIAL:
                $msg = "O upload foi parcial.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $msg = "Nenhum arquivo enviado.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $msg = "Pasta temporária ausente no servidor.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $msg = "Falha ao gravar o arquivo no disco.";
                break;
            case UPLOAD_ERR_EXTENSION:
                $msg = "Upload interrompido por extensão PHP.";
                break;
            default:
                $msg = "Erro no upload (código $code).";
        }
        $erro = $msg;
    } else {
        $nome_original = basename($_FILES["arquivo"]["name"]);
        $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));
        $permitidas = array("mp4", "webm", "ogg", "mov");

        if (!in_array($extensao, $permitidas)) {
            $erro = "Formato de video nao permitido.";
        } else {
            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0777, true);
            }

            // Verificar permissões antes de mover
            if (!is_writable(dirname($upload_dir))) {
                $erro = "Pasta pai de destino não é gravável: " . dirname($upload_dir);
            } elseif (!is_dir($upload_dir) || !is_writable($upload_dir)) {
                // Tentar criar e ajustar permissões
                @chmod($upload_dir, 0777);
                if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
                    $erro = "A pasta de vídeos não é gravável: " . $upload_dir;
                }
            }

            $nome_arquivo = time() . "_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($nome_original, PATHINFO_FILENAME)) . "." . $extensao;
            $destino = $upload_dir . $nome_arquivo;

            if (!is_uploaded_file($_FILES["arquivo"]["tmp_name"])) {
                $tmp = $_FILES["arquivo"]["tmp_name"] ?? 'n/a';
                $erro = "Arquivo temporário não encontrado: " . $tmp . "; upload_tmp_dir: " . $upload_tmp_dir . " (existe: " . (is_dir($upload_tmp_dir) ? 'sim' : 'nao') . ")";
            } elseif (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $destino)) {
                $url_salva = "videos/" . $nome_arquivo;

                $titulo_db = mysqli_real_escape_string($conn, $titulo);
                $descricao_db = mysqli_real_escape_string($conn, $descricao);
                $url_db = mysqli_real_escape_string($conn, $url_salva);
                $curso_id = $curso_id > 0 ? $curso_id : 1;
                $modulo_sql = $modulo_id === null ? "NULL" : (string) $modulo_id;

                $sql = "INSERT INTO ID_CONTENT (IDC_ID, IDM_ID, IDCT_TIPO, IDCT_TITULO, IDCT_DESCRICAO, IDCT_URL, IDCT_ORDEM) VALUES ($curso_id, $modulo_sql, 'VIDEO', '$titulo_db', '$descricao_db', '$url_db', $ordem)";

                if (mysqli_query($conn, $sql)) {
                    $mensagem = "Video enviado com sucesso.";
                } else {
                    @unlink($destino);
                    $erro = "Erro ao salvar no banco.";
                }
            } else {
                $erro = "Nao foi possivel salvar o arquivo. Tmp: " . ($_FILES["arquivo"]["tmp_name"] ?? 'n/a') . " Dest: " . $destino;
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"]) && $_POST["acao"] === "excluir") {
    $id = (int) $_POST["IDCT_ID"];
    $sql_busca = "SELECT IDCT_URL FROM ID_CONTENT WHERE IDCT_ID = $id LIMIT 1";
    $res_busca = mysqli_query($conn, $sql_busca);

    if ($res_busca && mysqli_num_rows($res_busca) == 1) {
        $video = mysqli_fetch_assoc($res_busca);
        $url = isset($video["IDCT_URL"]) ? $video["IDCT_URL"] : "";

        if (strpos($url, "videos/") === 0) {
            $arquivo_local = "../../" . $url;
            if (file_exists($arquivo_local)) {
                @unlink($arquivo_local);
            }
        }

        mysqli_query($conn, "DELETE FROM ID_CONTENT WHERE IDCT_ID = $id LIMIT 1");
        $mensagem = "Video excluido com sucesso.";
    } else {
        $erro = "Video nao encontrado.";
    }
}

$conteudos = array();
    $resultado = mysqli_query($conn, "SELECT IDCT_ID, IDC_ID, IDM_ID, IDCT_TIPO, IDCT_TITULO, IDCT_DESCRICAO, IDCT_URL, IDCT_ORDEM FROM ID_CONTENT WHERE LOWER(IDCT_TIPO) = 'video' ORDER BY IDCT_ID DESC");
if ($resultado && mysqli_num_rows($resultado) > 0) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $conteudos[] = $linha;
    }
}

$cursos = array();
$res_cursos = mysqli_query($conn, "SELECT IDC_ID, IDC_TITULO FROM ID_CURSO ORDER BY IDC_TITULO");
if ($res_cursos && mysqli_num_rows($res_cursos) > 0) {
    while ($linha = mysqli_fetch_assoc($res_cursos)) {
        $cursos[] = $linha;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Conteudos - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 1000px; margin: 0 auto;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="mb-1">Administrar Videos</h1>
                    <p class="mb-0 text-muted">Envio, edicao e exclusao simples de conteudos</p>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                        <small class="text-muted d-block">Videos cadastrados</small>
                        <strong class="fs-4"><?php echo count($conteudos); ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                        <small class="text-muted d-block">Modo do banco</small>
                        <strong class="fs-4"><?php echo banco_eh_robusto() ? 'Robusto' : 'Simples'; ?></strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
                        <small class="text-muted d-block">Arquivos enviados</small>
                        <strong class="fs-4">/videos</strong>
                    </div>
                </div>
            </div>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php } ?>

            <?php if ($erro !== "") { ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data" class="mb-5">
                <input type="hidden" name="acao" value="salvar">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Titulo</label>
                        <input type="text" name="IDCT_TITULO" class="form-control custom-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Curso</label>
                        <select name="IDC_ID" class="form-control custom-input" required>
                            <option value="">Selecione</option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?php echo (int) $curso["IDC_ID"]; ?>"><?php echo htmlspecialchars($curso["IDC_TITULO"]); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Modulo (opcional)</label>
                        <input type="number" name="IDM_ID" class="form-control custom-input" placeholder="ID do modulo">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="IDCT_ORDEM" class="form-control custom-input" value="1" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descricao</label>
                        <textarea name="IDCT_DESCRICAO" class="form-control custom-input" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Arquivo de video</label>
                        <input type="file" name="arquivo" class="form-control custom-input" accept="video/*" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-light fw-bold" style="color:#000000 !important; background:#ffffff !important;">Enviar video</button>
                    </div>
                </div>
            </form>

            <h2 class="h4 mb-3">Videos enviados</h2>
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle" style="color:#ffffff !important;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titulo</th>
                            <th>URL</th>
                            <th>Ordem</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($conteudos) > 0) { ?>
                            <?php foreach ($conteudos as $conteudo) { ?>
                                <tr>
                                    <td style="color:#ffffff !important;"><?php echo (int) $conteudo["IDCT_ID"]; ?></td>
                                    <td style="color:#ffffff !important;"><?php echo htmlspecialchars($conteudo["IDCT_TITULO"]); ?></td>
                                    <td style="color:#ffffff !important;"><?php echo htmlspecialchars($conteudo["IDCT_URL"]); ?></td>
                                    <td><?php echo (int) $conteudo["IDCT_ORDEM"]; ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars(strpos($conteudo["IDCT_URL"], 'videos/') === 0 ? '../../' . $conteudo["IDCT_URL"] : $conteudo["IDCT_URL"]); ?>" class="btn btn-sm btn-info" target="_blank">Ver</a>
                                        <a href="admin_editar_conteudo.php?id=<?php echo (int) $conteudo["IDCT_ID"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Deseja excluir este video?');">
                                            <input type="hidden" name="acao" value="excluir">
                                            <input type="hidden" name="IDCT_ID" value="<?php echo (int) $conteudo["IDCT_ID"]; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="5">Nenhum video cadastrado.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>