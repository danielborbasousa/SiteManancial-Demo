<?php
session_start();
include("conexao.php");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["IDF_NOME"];
    $email = $_POST["IDF_EMAIL"];
    $senha = $_POST["IDF_SENHA"];

    $sql = "INSERT INTO ID_FIEL (IDF_NOME, IDF_EMAIL, IDF_SENHA) VALUES ('$nome', '$email', '$senha')";

    if (mysqli_query($conn, $sql)) {
        $mensagem = "Cadastro realizado com sucesso. Faça login.";
    } else {
        $mensagem = "Erro ao cadastrar: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="auth-container text-center">
        <img src="logo.png" alt="Logotipo" class="logo mb-3" />
        <h2 class="mb-4 fw-bold">Criar Conta</h2>

        <?php if ($mensagem != "") { echo "<p style='color:#38bdf8;'>$mensagem</p>"; } ?>

        <form action="" method="POST">
            <div class="mb-3">
                <input type="text" name="IDF_NOME" class="form-control custom-input" placeholder="Nome completo" required>
            </div>

            <div class="mb-3">
                <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Endereco de e-mail" required>
            </div>

            <div class="mb-4">
                <input type="password" name="IDF_SENHA" class="form-control custom-input" placeholder="Criar senha" required>
            </div>

            <button type="submit" class="btn btn-light w-100 py-2 fw-bold mb-4">Cadastrar</button>

            <p class="text-center text-muted mb-0">
                Ja tem conta? <a href="index.php" class="auth-link">Entrar.</a>
            </p>
        </form>
    </div>

</body>
</html>
