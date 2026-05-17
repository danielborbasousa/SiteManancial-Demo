<?php
session_start();
include("../conexao.php");
auth_require();

if(!isset($_SESSION["Usuario_logado"])) {
    header("location:login.php");
    exit;
}

$email = $_SESSION["Usuario_logado"];
$email_limpo = mysqli_real_escape_string($conn, $email);

if (banco_eh_robusto()) {
    $sql = "SELECT f.IDF_NOME, f.IDF_EMAIL, f.IDF_TELEFONE, f.IDF_CPF, f.IDF_FUNCAO, f.IDF_ENDERECO, COALESCE(fl.IDL_NOME, '') AS IDF_FILIAL_EXIBICAO FROM ID_FIEL f LEFT JOIN ID_FILIAL fl ON fl.IDL_ID = f.IDF_FILIAL_ID WHERE f.IDF_EMAIL = '$email_limpo' LIMIT 1";
} else {
    $sql = "SELECT * FROM ID_FIEL WHERE IDF_EMAIL = '$email_limpo' LIMIT 1";
}

$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="../assets/logo.png" alt="Logotipo" class="logo me-2" /> Missao Evangelica Manancial da Esperança
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-4">Meu Perfil</h1>
            
            <?php if($usuario) { ?>
                <div class="mb-3">
                    <strong>Nome:</strong> <?php echo $usuario['IDF_NOME']; ?>
                </div>
                <div class="mb-3">
                    <strong>E-mail:</strong> <?php echo $usuario['IDF_EMAIL']; ?>
                </div>
                <div class="mb-3">
                    <strong>Telefone:</strong> 
                    <?php 
                    if($usuario['IDF_TELEFONE']) {
                        echo $usuario['IDF_TELEFONE'];
                    } else {
                        echo "Nao informado";
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <strong>CPF:</strong> 
                    <?php 
                    if($usuario['IDF_CPF']) {
                        echo $usuario['IDF_CPF'];
                    } else {
                        echo "Nao informado";
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <strong>Filial:</strong> 
                    <?php 
                    if (banco_eh_robusto()) {
                        if(isset($usuario['IDF_FILIAL_EXIBICAO']) && $usuario['IDF_FILIAL_EXIBICAO'] != '') {
                            echo $usuario['IDF_FILIAL_EXIBICAO'];
                        } else {
                            echo "Nao informada";
                        }
                    } else {
                        if($usuario['IDF_FILIAL']) {
                            echo $usuario['IDF_FILIAL'];
                        } else {
                            echo "Nao informada";
                        }
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <strong>Funcao:</strong> 
                    <?php 
                    if($usuario['IDF_FUNCAO']) {
                        echo $usuario['IDF_FUNCAO'];
                    } else {
                        echo "Nao informada";
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <strong>Endereco:</strong> 
                    <?php 
                    if($usuario['IDF_ENDERECO']) {
                        echo $usuario['IDF_ENDERECO'];
                    } else {
                        echo "Nao informado";
                    }
                    ?>
                </div>
            <?php } ?>

            <a href="dashboard.php" class="btn btn-outline-primary">Voltar para o painel</a>
        </div>
    </main>
</body>
</html>
