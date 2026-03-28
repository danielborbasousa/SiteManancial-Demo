<?php
    session_start(); // Inicia a sessão
    include("conexao.php"); // Inclui a conexão com o banco
    // Módulo de processamento de login
    if($_SERVER["REQUEST_METHOD"] == "POST"){ // Verifica se o formulário foi enviado via POST
        $email = $_POST["IDF_EMAIL"]; // Obtém o e-mail do formulário
        $senha = $_POST["IDF_SENHA"]; // Obtém a senha do formulário
        $sql = "SELECT * FROM ID_FIEL WHERE IDF_EMAIL = '$email' AND IDF_SENHA = '$senha'"; // Consulta SQL para verificar credenciais
        $resultado = mysqli_query($conn, $sql); // Executa a consulta
 
        if($resultado && mysqli_num_rows($resultado) == 1){ // Verifica se encontrou um usuário
            $_SESSION["Usuario_logado"] = $email; // Armazena o e-mail na sessão
            header("location:dashboardusuario.php"); // Redireciona para o dashboard
            exit; // Para a execução
        }
        else{
            $erro = "E-mail ou senha incorretos"; // Define mensagem de erro
        }
    }
?> 

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Missão Evangélica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="auth-container text-center">
        <img src="logo.png" alt="Logotipo" class="logo mb-3" />
        <h2 class="mb-4 fw-bold">Entrar</h2>
        <?php if(isset($erro)) { echo "<p style='color:red;'>$erro</p>"; } ?>
        <form action="" method="POST">
            
            <div class="mb-3">
                <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Endereço de e‑mail" required>
            </div>
            
            <div class="mb-4">
                <input type="password" name="IDF_SENHA" class="form-control custom-input" placeholder="Senha" required>
            </div>
            
            <button type="submit" class="btn btn-light w-100 py-2 fw-bold mb-4 d-block mx-auto">Entrar</button>
            
            <p class="text-center text-light mb-0">
                Novo aqui? <a href="register.php" class="auth-link">Criar conta.</a>
            </p>
        </form>
    </div>

</body>
</html>