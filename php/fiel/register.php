<?php
session_start();
include("../conexao.php");

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
    $senha = $_POST["IDF_SENHA"];
    $senha_confirma = $_POST["IDF_SENHA_CONFIRMA"];

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
    } elseif (!ctype_digit($telefone_limpo) || strlen($telefone_limpo) !== 11) {
        $mensagem = "Telefone invalido. Use o formato (11) 9 9999-9999.";
    } elseif (!ctype_digit($cpf_limpo) || strlen($cpf_limpo) != 11) {
        $mensagem = "CPF invalido. Use apenas numeros (11 digitos).";
    } elseif (strlen($filial) < 2 || strlen($filial) > $lim_filial) {
        $mensagem = "Filial invalida. Use entre 2 e " . $lim_filial . " caracteres.";
    } elseif (strlen($funcao) < 2 || strlen($funcao) > $lim_funcao) {
        $mensagem = "Funcao invalida. Use entre 2 e " . $lim_funcao . " caracteres.";
    } elseif (strlen($senha) < 6 || strlen($senha) > $lim_senha) {
        $mensagem = "Senha invalida. Use entre 6 e " . $lim_senha . " caracteres.";
    } elseif ($senha !== $senha_confirma) {
        $mensagem = "As senhas nao conferem. Tente novamente.";
    } else {
        $nome = mysqli_real_escape_string($conn, $nome);
        $email = mysqli_real_escape_string($conn, $email);
        $telefone_limpo = mysqli_real_escape_string($conn, $telefone_limpo);
        $cpf_limpo = mysqli_real_escape_string($conn, $cpf_limpo);
        $filial = mysqli_real_escape_string($conn, $filial);
        $funcao = mysqli_real_escape_string($conn, $funcao);

        if (banco_eh_robusto()) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $senha_hash = mysqli_real_escape_string($conn, $senha_hash);

            $filial_id = "NULL";
            $sql_filial = "SELECT IDL_ID FROM ID_FILIAL WHERE IDL_NOME = '$filial' LIMIT 1";
            $resultado_filial = mysqli_query($conn, $sql_filial);
            if ($resultado_filial && mysqli_num_rows($resultado_filial) == 1) {
                $linha_filial = mysqli_fetch_assoc($resultado_filial);
                $filial_id = (int) $linha_filial["IDL_ID"];
            }

            $sql = "INSERT INTO ID_FIEL (IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FILIAL_ID, IDF_FUNCAO, IDF_ENDERECO, IDF_SENHA_HASH, IDF_STATUS, IDF_ATIVO) VALUES ('$nome', '$email', '$telefone_limpo', '$cpf_limpo', $filial_id, '$funcao', '', '$senha_hash', 'pendente', 1)";
        } else {
            $senha = mysqli_real_escape_string($conn, $senha);
            $sql = "INSERT INTO ID_FIEL (IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FILIAL, IDF_FUNCAO, IDF_ENDERECO, IDF_SENHA, IDF_STATUS) VALUES ('$nome', '$email', '$telefone_limpo', '$cpf_limpo', '$filial', '$funcao', '', '$senha', 'pendente')";
        }

        if (mysqli_query($conn, $sql)) {
            $_SESSION['cadastro_sucesso'] = 'Sua solicitação foi recebida e está aguardando aprovação.';
            header("location:../../login.php");
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
    <title>Missão Manancial | Criar conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../../js/theme.js"></script>
</head>
<body class="auth-page">

    <div class="position-absolute top-0 end-0 p-3" style="z-index: 50; display:flex; gap:8px; align-items:center;">
        <div class="theme-toggle-container">
            <i class="fas fa-moon theme-icon"></i>
            <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema">
            <i class="fas fa-sun theme-icon"></i>
        </div>
    </div>

    <a href="../../index.html" class="auth-back-link">
        <i class="fas fa-arrow-left"></i>
        Voltar para a página inicial
    </a>

    <section class="auth-panel auth-panel--wide auth-panel--heroless">
        <div class="auth-brand-row">
            <img src="../../assets/logo.png" alt="Logotipo" class="logo" />
            <div class="auth-brand-copy">
                <h2>Criar conta</h2>
                <p>Preencha os dados abaixo para acessar a plataforma.</p>
            </div>
        </div>

        <div class="auth-compact-divider"></div>

        <?php if ($mensagem != "") {
            $cor_mensagem = "#38bdf8";
            if (strpos($mensagem, "invalido") !== false || strpos($mensagem, "nao pode") !== false || strpos($mensagem, "Erro") !== false) {
                $cor_mensagem = "#ff6b6b";
            }
            echo "<div class='mb-3' style='color:" . $cor_mensagem . "; font-weight:600;'>" . htmlspecialchars($mensagem) . "</div>";
        } ?>

        <form action="" method="POST" class="auth-form-grid">
            <div class="auth-form-grid auth-form-grid--two">
                <div>
                    <input type="text" name="IDF_NOME" class="form-control custom-input" placeholder="Nome completo" pattern="[A-Za-zÀ-ÿ\s]+" title="Use apenas letras e espacos" minlength="3" maxlength="100" required>
                </div>

                <div>
                    <input type="email" name="IDF_EMAIL" class="form-control custom-input" placeholder="Endereco de e-mail" maxlength="100" required>
                </div>
            </div>

            <div class="auth-form-grid auth-form-grid--two">
                <div>
                    <input type="text" id="IDF_TELEFONE" name="IDF_TELEFONE" class="form-control custom-input" placeholder="(11) 9 9999-9999" pattern="\([0-9]{2}\) 9 [0-9]{4}-[0-9]{4}" title="Use o formato (11) 9 9999-9999" inputmode="numeric" minlength="16" maxlength="16" required>
                </div>

                <div>
                    <input type="text" id="IDF_CPF" name="IDF_CPF" class="form-control custom-input" placeholder="000.000.000-00" pattern="[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}" title="Use o formato 000.000.000-00" inputmode="numeric" minlength="14" maxlength="14" required>
                </div>
            </div>

            <div class="auth-form-grid auth-form-grid--two">
                <div>
                    <input type="text" name="IDF_FILIAL" class="form-control custom-input" placeholder="Filial" minlength="2" maxlength="100" required>
                </div>

                <div>
                    <input type="text" name="IDF_FUNCAO" class="form-control custom-input" placeholder="Função" minlength="2" maxlength="50" required>
                </div>
            </div>

            <div class="auth-form-grid">
                <div>
                    <input type="password" name="IDF_SENHA" class="form-control custom-input" placeholder="Criar senha" minlength="6" maxlength="100" required>
                </div>

                <div>
                    <input type="password" name="IDF_SENHA_CONFIRMA" class="form-control custom-input" placeholder="Confirmar senha" minlength="6" maxlength="100" required>
                </div>
            </div>

            <div class="auth-form-actions">
                <button type="submit" class="btn auth-submit w-100 py-2">Cadastrar</button>

                <p class="text-center mb-0" style="color: var(--text-muted);">
                    Já tem conta? <a href="../../login.php" class="auth-link">Entrar.</a>
                </p>
            </div>
        </form>
    </section>

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
            valor = valor.replace(/\D/g, '').substring(0, 11);

            if (valor.length <= 2) {
                return '(' + valor;
            }

            if (valor.length === 3) {
                return '(' + valor.substring(0, 2) + ') ' + valor.substring(2);
            }

            var ddd = valor.substring(0, 2);
            var nove = valor.substring(2, 3);
            var bloco1 = valor.substring(3, 7);
            var bloco2 = valor.substring(7, 11);
            var formatado = '(' + ddd + ') ' + nove;

            if (bloco1.length > 0) {
                formatado += ' ' + bloco1;
            }

            if (bloco2.length > 0) {
                formatado += '-' + bloco2;
            }

            return formatado;
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
