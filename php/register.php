<?php
session_start();
include("conexao.php");

$mensagem = "";

// Limites alinhados com a estrutura da tabela ID_FIEL
$lim_nome = 100;
$lim_email = 100;
$lim_telefone = 11;
$lim_cpf = 11;
$lim_filial = 100;
$lim_funcao = 50;
$lim_endereco = 200;
$lim_senha = 100;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["IDF_NOME"]);
    $email = trim($_POST["IDF_EMAIL"]);
    $telefone = trim($_POST["IDF_TELEFONE"]);
    $cpf = trim($_POST["IDF_CPF"]);
    $filial = trim($_POST["IDF_FILIAL"]);
    $funcao = trim($_POST["IDF_FUNCAO"]);
    $endereco = trim($_POST["IDF_ENDERECO"]);
    $senha = $_POST["IDF_SENHA"];

    $telefone_limpo = preg_replace('/\D/', '', $telefone);
    $cpf_limpo = preg_replace('/\D/', '', $cpf);

    if (preg_match('/\d/', $nome)) {
        $mensagem = "O nome nao pode conter numeros.";
    } elseif (strlen($nome) < 3 || strlen($nome) > $lim_nome) {
        $mensagem = "Nome invalido. Use entre 3 e " . $lim_nome . " caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = "Informe um e-mail valido.";
    } elseif (strlen($email) > $lim_email) {
        $mensagem = "E-mail invalido. Maximo de " . $lim_email . " caracteres.";
    } elseif (!ctype_digit($telefone_limpo) || strlen($telefone_limpo) < 10 || strlen($telefone_limpo) > 11) {
        $mensagem = "Telefone invalido. Use apenas numeros (10 ou 11 digitos).";
    } elseif (!ctype_digit($cpf_limpo) || strlen($cpf_limpo) != 11) {
        $mensagem = "CPF invalido. Use apenas numeros (11 digitos).";
    } elseif (strlen($filial) < 2 || strlen($filial) > $lim_filial) {
        $mensagem = "Filial invalida. Use entre 2 e " . $lim_filial . " caracteres.";
    } elseif (strlen($funcao) < 2 || strlen($funcao) > $lim_funcao) {
        $mensagem = "Funcao invalida. Use entre 2 e " . $lim_funcao . " caracteres.";
    } elseif (strlen($endereco) < 5 || strlen($endereco) > $lim_endereco) {
        $mensagem = "Endereco invalido. Use entre 5 e " . $lim_endereco . " caracteres.";
    } elseif (strlen($senha) < 6 || strlen($senha) > $lim_senha) {
        $mensagem = "Senha invalida. Use entre 6 e " . $lim_senha . " caracteres.";
    } else {
        $nome = mysqli_real_escape_string($conn, $nome);
        $email = mysqli_real_escape_string($conn, $email);
        $telefone_limpo = mysqli_real_escape_string($conn, $telefone_limpo);
        $cpf_limpo = mysqli_real_escape_string($conn, $cpf_limpo);
        $filial = mysqli_real_escape_string($conn, $filial);
        $funcao = mysqli_real_escape_string($conn, $funcao);
        $endereco = mysqli_real_escape_string($conn, $endereco);
        $senha = mysqli_real_escape_string($conn, $senha);

        $sql = "INSERT INTO ID_FIEL (IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FILIAL, IDF_FUNCAO, IDF_ENDERECO, IDF_SENHA) VALUES ('$nome', '$email', '$telefone_limpo', '$cpf_limpo', '$filial', '$funcao', '$endereco', '$senha')";

        if (mysqli_query($conn, $sql)) {
            header("location:index.php");
            exit;
        } else {
            $mensagem = "Erro ao cadastrar: " . mysqli_error($conn);
        }
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
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="auth-container text-center">
        <img src="../assets/logo.png" alt="Logotipo" class="logo mb-3" />
        <h2 class="mb-4 fw-bold">Criar Conta</h2>

        <?php if ($mensagem != "") {
            $cor_mensagem = "#38bdf8";
            if (strpos($mensagem, "invalido") !== false || strpos($mensagem, "nao pode") !== false || strpos($mensagem, "Erro") !== false) {
                $cor_mensagem = "#ff6b6b";
            }
            echo "<p style='color:" . $cor_mensagem . ";'>" . $mensagem . "</p>";
        } ?>

        <form action="" method="POST">
            <div class="mb-3">
                <input type="text" name="IDF_NOME" class="form-control custom-input" placeholder="Nome completo" pattern="[A-Za-zÀ-ÿ\s]+" title="Use apenas letras e espacos" minlength="3" maxlength="100" required>
            </div>

            <div class="mb-3">
                <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Endereco de e-mail" maxlength="100" required>
            </div>

            <div class="mb-3">
                <input type="text" id="IDF_TELEFONE" name="IDF_TELEFONE" class="form-control custom-input" placeholder="(11) 99999-9999" pattern="\([0-9]{2}\) [0-9]{4,5}-[0-9]{4}" title="Use o formato (11) 99999-9999" inputmode="numeric" minlength="14" maxlength="15" required>
            </div>

            <div class="mb-3">
                <input type="text" id="IDF_CPF" name="IDF_CPF" class="form-control custom-input" placeholder="000.000.000-00" pattern="[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}" title="Use o formato 000.000.000-00" inputmode="numeric" minlength="14" maxlength="14" required>
            </div>

            <div class="mb-3">
                <input type="text" name="IDF_FILIAL" class="form-control custom-input" placeholder="Filial" minlength="2" maxlength="100" required>
            </div>

            <div class="mb-3">
                <input type="text" name="IDF_FUNCAO" class="form-control custom-input" placeholder="Função" minlength="2" maxlength="50" required>
            </div>

            <div class="mb-3">
                <input type="text" name="IDF_ENDERECO" class="form-control custom-input" placeholder="Endereco" minlength="5" maxlength="200" required>
            </div>

            <div class="mb-4">
                <input type="password" name="IDF_SENHA" class="form-control custom-input" placeholder="Criar senha" minlength="6" maxlength="100" required>
            </div>

            <button type="submit" class="btn btn-light w-100 py-2 fw-bold mb-4 d-block mx-auto">Cadastrar</button>

            <p class="text-center text-light mb-0">
                Ja tem conta? <a href="index.php" class="auth-link">Entrar.</a>
            </p>
        </form>
    </div>

    <script>
        function mascaraCPF(valor) {
            valor = valor.replace(/\D/g, '');
            valor = valor.substring(0, 11);
            valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            return valor;
        }

        function mascaraTelefone(valor) {
            valor = valor.replace(/\D/g, '');
            valor = valor.substring(0, 11);
            if (valor.length <= 10) {
                valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
                valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
                valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
            }
            return valor;
        }

        document.getElementById('IDF_CPF').addEventListener('input', function() {
            this.value = mascaraCPF(this.value);
        });

        document.getElementById('IDF_TELEFONE').addEventListener('input', function() {
            this.value = mascaraTelefone(this.value);
        });
    </script>

</body>
</html>
