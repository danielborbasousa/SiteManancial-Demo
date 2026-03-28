<?php
session_start();
include("conexao.php");

if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}

$busca = "";
$encontrou = false;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $busca = $_POST["busca"];
    $sql = "SELECT * FROM ID_CONTENT WHERE IDCT_TITULO LIKE '%$busca%' OR IDCT_DESCRICAO LIKE '%$busca%'";
    $resultado = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboardusuario.php">
                <img src="logo.png" alt="Logotipo" class="logo me-2" /> Missao Evangelica Manancial da Esperança
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardusuario.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-4">Buscar Conteudo</h1>
            
            <form method="POST" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="busca" class="form-control custom-input" placeholder="Digite o titulo ou descricao..." value="<?php echo $busca; ?>">
                    <button class="btn btn-light" type="submit">Buscar</button>
                </div>
            </form>

            <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $resultado = mysqli_query($conn, $sql);
                if($resultado && mysqli_num_rows($resultado) > 0) {
            ?>
                <div>
                    <h5>Resultados encontrados:</h5>
                    <div class="list-group mt-3">
                        <?php 
                        while($conteudo = mysqli_fetch_assoc($resultado)) {
                        ?>
                            <div class="list-group-item list-group-item-action">
                                <h6 class="mb-1"><?php echo $conteudo['IDCT_TITULO']; ?></h6>
                                <p class="mb-1"><?php echo $conteudo['IDCT_DESCRICAO']; ?></p>
                                <small>Tipo: <?php echo $conteudo['IDCT_TIPO']; ?></small>
                            </div>
                        <?php 
                        }
                        ?>
                    </div>
                </div>
            <?php 
                } else {
            ?>
                <div class="alert alert-warning">
                    Nenhum conteudo encontrado para "<?php echo $busca; ?>".
                </div>
            <?php 
                }
            }
            ?>

            <a href="dashboardusuario.php" class="btn btn-light mt-4">Voltar para o painel</a>
        </div>
    </main>
</body>
</html>
