<?php
    session_start(); // Inicia a sessão
    include("conexao.php"); // Inclui a conexão com o banco
    // Módulo de processamento de login
    if($_SERVER["REQUEST_METHOD"] == "POST"){ // Verifica se o formulário foi enviado via POST
        $email = mysqli_real_escape_string($conn, trim($_POST["IDF_EMAIL"])); // Obtém o e-mail do formulário
        $senha = mysqli_real_escape_string($conn, $_POST["IDF_SENHA"]); // Obtém a senha do formulário

        // 1) Tenta autenticar como usuário comum (ID_FIEL)
        $sql_fiel = "SELECT * FROM ID_FIEL WHERE IDF_EMAIL = '$email' AND IDF_SENHA = '$senha'";
        $resultado_fiel = mysqli_query($conn, $sql_fiel);

        if($resultado_fiel && mysqli_num_rows($resultado_fiel) == 1){
            $usuario_fiel = mysqli_fetch_assoc($resultado_fiel);
            $_SESSION["Usuario_logado"] = $usuario_fiel["IDF_EMAIL"];
            $_SESSION["Usuario_tipo"] = "user";
            $_SESSION["Usuario_id"] = $usuario_fiel["IDF_ID"];
            header("location:dashboardusuario.php");
            exit;
        }

        // 2) Se não for usuário, tenta autenticar como administrador (ID_ADMIN)
        $sql_admin = "SELECT * FROM ID_ADMIN WHERE IDA_EMAIL = '$email' AND IDA_SENHA = '$senha'";
        $resultado_admin = mysqli_query($conn, $sql_admin);

        if($resultado_admin && mysqli_num_rows($resultado_admin) == 1){
            $usuario_admin = mysqli_fetch_assoc($resultado_admin);
            $_SESSION["Usuario_logado"] = $usuario_admin["IDA_EMAIL"];
            $_SESSION["Usuario_tipo"] = "admin";
            $_SESSION["Usuario_id"] = $usuario_admin["IDA_ID"];
            header("location:dashboardadmin.php");
            exit;
        }

        $erro = "E-mail ou senha incorretos"; // Define mensagem de erro
    }
?> 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fazer Login - Missão Evangélica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">

    <a href="index.html" class="btn btn-outline-light btn-sm" style="position:fixed; top:16px; left:16px; z-index:9999;">Voltar para o site</a>

    <div class="auth-container text-center">
        <img src="../assets/logo.png" alt="Logotipo" class="logo mb-3" />
        <h2 class="mb-4 fw-bold">Fazer Login</h2>
        <?php if(isset($erro)) { echo "<p style='color:red;'>$erro</p>"; } ?>
        <form action="" method="POST">
            
            <div class="mb-3">
                <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Endereço de e‑mail" required>
            </div>
            
            <div class="mb-4">
                <input type="password" name="IDF_SENHA" class="form-control custom-input" placeholder="Senha" required>
            </div>
            
            <button type="submit" class="btn btn-light w-100 py-2 fw-bold mb-4 d-block mx-auto">Fazer Login</button>

            <p class="text-center text-light mb-0">
                Novo aqui? <a href="register.php" class="auth-link">Criar conta.</a>
            </p>
        </form>
    </div>

</body>
</html>