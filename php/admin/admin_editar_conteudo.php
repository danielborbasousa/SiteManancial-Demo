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
$conteudo = null;
// Caminho absoluto para a pasta de videos
$base_root = realpath(__DIR__ . '/../../');
if ($base_root !== false) {
    $upload_dir = $base_root . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR;
} else {
    $upload_dir = __DIR__ . '/../../videos/';
}
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    header("location:admin_conteudos.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST["IDCT_TITULO"]);
    $descricao = trim($_POST["IDCT_DESCRICAO"]);
    $ordem = (int) $_POST["IDCT_ORDEM"];
    $curso_id = (int) $_POST["IDC_ID"];
    $modulo_id = isset($_POST["IDM_ID"]) && $_POST["IDM_ID"] !== "" ? (int) $_POST["IDM_ID"] : null;

    if ($titulo === "") {
        $erro = "Informe o titulo do video.";
    } else {
        $sql_busca = "SELECT IDCT_URL FROM ID_CONTENT WHERE IDCT_ID = $id LIMIT 1";
        $res_busca = mysqli_query($conn, $sql_busca);
        $conteudo_atual = $res_busca ? mysqli_fetch_assoc($res_busca) : null;
        $url_final = $conteudo_atual && isset($conteudo_atual["IDCT_URL"]) ? $conteudo_atual["IDCT_URL"] : "";

        if (isset($_FILES["arquivo"]) && $_FILES["arquivo"]["error"] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES["arquivo"]["error"] !== UPLOAD_ERR_OK) {
                $code = $_FILES["arquivo"]["error"];
                switch ($code) {
                    case UPLOAD_ERR_INI_SIZE:
                        $msg = "O arquivo excede upload_max_filesize no servidor.";
                        break;
                    case UPLOAD_ERR_FORM_SIZE:
                        $msg = "O arquivo excede o tamanho permitido pelo formulário.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $msg = "O upload foi parcial.";
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

                    $nome_arquivo = time() . "_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($nome_original, PATHINFO_FILENAME)) . "." . $extensao;
                    $destino = $upload_dir . $nome_arquivo;

                    if (!is_uploaded_file($_FILES["arquivo"]["tmp_name"])) {
                        $erro = "Arquivo temporário não encontrado: " . ($_FILES["arquivo"]["tmp_name"] ?? 'n/a');
                    } elseif (move_uploaded_file($_FILES["arquivo"]["tmp_name"], $destino)) {
                        if ($url_final !== "" && strpos($url_final, "videos/") === 0) {
                            $arquivo_antigo = "../../" . $url_final;
                            if (file_exists($arquivo_antigo)) {
                                @unlink($arquivo_antigo);
                            }
                        }

                        $url_final = "videos/" . $nome_arquivo;
                    } else {
                        $erro = "Nao foi possivel salvar o novo video.";
                    }
                }
            }
        }

        if ($erro === "") {
            $titulo_db = mysqli_real_escape_string($conn, $titulo);
            $descricao_db = mysqli_real_escape_string($conn, $descricao);
            $url_db = mysqli_real_escape_string($conn, $url_final);
            $curso_id = $curso_id > 0 ? $curso_id : 1;
            $modulo_sql = $modulo_id === null ? "NULL" : (string) $modulo_id;

            $sql_update = "UPDATE ID_CONTENT SET IDC_ID = $curso_id, IDM_ID = $modulo_sql, IDCT_TITULO = '$titulo_db', IDCT_DESCRICAO = '$descricao_db', IDCT_URL = '$url_db', IDCT_ORDEM = $ordem WHERE IDCT_ID = $id LIMIT 1";

            if (mysqli_query($conn, $sql_update)) {
                $mensagem = "Video atualizado com sucesso.";
            } else {
                $erro = "Erro ao atualizar no banco.";
            }
        }
    }
}

$sql = "SELECT * FROM ID_CONTENT WHERE IDCT_ID = $id LIMIT 1";
$resultado = mysqli_query($conn, $sql);
if ($resultado && mysqli_num_rows($resultado) == 1) {
    $conteudo = mysqli_fetch_assoc($resultado);
} else {
    header("location:admin_conteudos.php");
    exit;
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
    <title>Editar Conteudo - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
    <style>
        :root[data-theme="dark"] .video-edit-success {
            background: #14532d !important;
            color: #ffffff !important;
            border: 1px solid #166534 !important;
        }
    </style>
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h1 class="mb-1">Editar Video</h1>
                    <p class="mb-0 text-muted">Ajuste titulo, descricao, ordem e arquivo do conteudo</p>
                </div>
            </div>

            <?php if ($mensagem !== "") { ?>
                <div class="alert alert-success video-edit-success"><?php echo htmlspecialchars($mensagem); ?></div>
            <?php } ?>

            <?php if ($erro !== "") { ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
            <?php } ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Titulo</label>
                        <input type="text" name="IDCT_TITULO" class="form-control custom-input" value="<?php echo htmlspecialchars($conteudo["IDCT_TITULO"]); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Curso</label>
                        <select name="IDC_ID" class="form-control custom-input" required>
                            <option value="">Selecione</option>
                            <?php foreach ($cursos as $curso) { ?>
                                <option value="<?php echo (int) $curso["IDC_ID"]; ?>" <?php echo ((int) $conteudo["IDC_ID"] === (int) $curso["IDC_ID"]) ? "selected" : ""; ?>><?php echo htmlspecialchars($curso["IDC_TITULO"]); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Modulo (opcional)</label>
                        <input type="text" name="IDM_ID" class="form-control custom-input" value="<?php echo htmlspecialchars($conteudo["IDM_ID"]); ?>" placeholder="ID do modulo" inputmode="numeric" pattern="[0-9]*" title="Use apenas numeros" oninput="this.value = this.value.replace(/\D/g, '')">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ordem</label>
                        <input type="number" name="IDCT_ORDEM" class="form-control custom-input" value="<?php echo (int) $conteudo["IDCT_ORDEM"]; ?>" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descricao</label>
                        <textarea name="IDCT_DESCRICAO" class="form-control custom-input" rows="3"><?php echo htmlspecialchars($conteudo["IDCT_DESCRICAO"]); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Trocar video (opcional)</label>
                        <input type="file" name="arquivo" class="form-control custom-input" accept="video/*">
                        <small class="text-muted d-block mt-1">Atual: <?php echo htmlspecialchars($conteudo["IDCT_URL"]); ?></small>
                        <small class="text-muted d-block">Se nenhum arquivo for selecionado, o vídeo atual será mantido.</small>
                        <?php if (!empty($conteudo["IDCT_URL"])) { ?>
                            <div class="mt-3">
                                <video controls style="max-width: 100%; border-radius: 10px; border: 1px solid var(--border-color);">
                                    <source src="<?php echo htmlspecialchars(strpos($conteudo["IDCT_URL"], 'videos/') === 0 ? '../../' . $conteudo["IDCT_URL"] : $conteudo["IDCT_URL"]); ?>">
                                    Seu navegador não suporta a reprodução de vídeo.
                                </video>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-light fw-bold">Salvar alteracoes</button>
                        <a href="dashboard.php" class="btn btn-outline-light">Voltar ao Painel</a>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>