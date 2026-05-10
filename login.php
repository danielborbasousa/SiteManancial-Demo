<?php
session_start();
include("php/conexao.php");

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["IDF_EMAIL"]);
    $senha = $_POST["IDF_SENHA"];

    $email_limpo = mysqli_real_escape_string($conn, $email);

    if (banco_eh_robusto()) {
        $sql_fiel = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_SENHA_HASH FROM ID_FIEL WHERE IDF_EMAIL = '$email_limpo' LIMIT 1";
        $resultado_fiel = mysqli_query($conn, $sql_fiel);

        if ($resultado_fiel && mysqli_num_rows($resultado_fiel) == 1) {
            $usuario = mysqli_fetch_assoc($resultado_fiel);
            $senha_hash = hash('sha256', $senha);
            if (isset($usuario["IDF_SENHA_HASH"]) && (
                $usuario["IDF_SENHA_HASH"] === $senha_hash || password_verify($senha, $usuario["IDF_SENHA_HASH"])
            )) {
                session_regenerate_id(true);
                $_SESSION["Usuario_logado"] = $usuario["IDF_EMAIL"];
                $_SESSION["Usuario_nome"] = $usuario["IDF_NOME"];
                $_SESSION["Usuario_tipo"] = "usuario";

                $id_fiel = (int) $usuario["IDF_ID"];
                $sql_admin = "SELECT IDA_ID FROM ID_ADMIN WHERE IDA_FIEL_ID = $id_fiel LIMIT 1";
                $resultado_admin = mysqli_query($conn, $sql_admin);
                if ($resultado_admin && mysqli_num_rows($resultado_admin) == 1) {
                    $_SESSION["Usuario_tipo"] = "admin";
                }

                header("location:php/fiel/dashboardusuario.php");
                exit;
            }
        }
    } else {
        $sql_fiel = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_SENHA FROM ID_FIEL WHERE IDF_EMAIL = '$email_limpo' LIMIT 1";
        $resultado_fiel = mysqli_query($conn, $sql_fiel);

        if ($resultado_fiel && mysqli_num_rows($resultado_fiel) == 1) {
            $usuario = mysqli_fetch_assoc($resultado_fiel);
            if ($usuario["IDF_SENHA"] === $senha) {
                session_regenerate_id(true);
                $_SESSION["Usuario_logado"] = $usuario["IDF_EMAIL"];
                $_SESSION["Usuario_nome"] = $usuario["IDF_NOME"];
                $_SESSION["Usuario_tipo"] = "usuario";
                header("location:php/fiel/dashboardusuario.php");
                exit;
            }
        }

        $sql_admin = "SELECT IDA_ID, IDA_NOME, IDA_EMAIL, IDA_SENHA FROM ID_ADMIN WHERE IDA_EMAIL = '$email_limpo' LIMIT 1";
        $resultado_admin = mysqli_query($conn, $sql_admin);

        if ($resultado_admin && mysqli_num_rows($resultado_admin) == 1) {
            $admin = mysqli_fetch_assoc($resultado_admin);
            if ($admin["IDA_SENHA"] === $senha) {
                session_regenerate_id(true);
                $_SESSION["Usuario_logado"] = $admin["IDA_EMAIL"];
                $_SESSION["Usuario_nome"] = $admin["IDA_NOME"];
                $_SESSION["Usuario_tipo"] = "admin";
                header("location:php/fiel/dashboardusuario.php");
                exit;
            }
        }
    }

    $erro = "E-mail ou senha incorretos";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Missão Evangélica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="auth-container text-center">
        <img src="assets/logo.png" alt="Logotipo" class="logo mb-3" />
        <h2 class="mb-4 fw-bold">Entrar</h2>
        <?php if ($erro != "") { echo "<p style='color:red;'>" . htmlspecialchars($erro) . "</p>"; } ?>
        <form action="" method="POST">
            <div class="mb-3">
                <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Endereço de e-mail" required>
            </div>

            <div class="mb-4">
                <input type="password" name="IDF_SENHA" class="form-control custom-input" placeholder="Senha" required>
            </div>

            <button type="submit" class="btn btn-light w-100 py-2 fw-bold mb-4 d-block mx-auto">Entrar</button>

            <p class="text-center text-light mb-0">
                Novo aqui? <a href="php/fiel/register.php" class="auth-link">Criar conta.</a>
            </p>
        </form>
    </div>

</body>
</html>