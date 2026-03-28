<?php
session_start();
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}

$url = isset($_GET["url"]) ? $_GET["url"] : "";
$titulo = isset($_GET["titulo"]) ? $_GET["titulo"] : "Video";
$descricao = isset($_GET["descricao"]) ? $_GET["descricao"] : "";

// So permite videos locais dentro da pasta videos/
if(strpos($url, "videos/") !== 0) {
    $url = "";
}

if($url != "" && !file_exists($url)) {
    $url = "";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?> - Player</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><?php echo htmlspecialchars($titulo); ?></h4>
            <a href="dashboardusuario.php" class="btn btn-outline-light btn-sm">Voltar</a>
        </div>

        <?php if($url != ""): ?>
            <div style="background:#000; border-radius: 10px; overflow: hidden;">
                <video controls autoplay style="width:100%; max-height:70vh;" src="<?php echo htmlspecialchars($url); ?>">
                    Seu navegador não suporta vídeo HTML5.
                </video>
            </div>
            <p class="mt-3 text-info"><?php echo htmlspecialchars($descricao); ?></p>
        <?php else: ?>
            <div class="alert alert-warning">Video nao encontrado ou URL invalida.</div>
        <?php endif; ?>
    </main>
</body>
</html>
