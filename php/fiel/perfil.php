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
    <title>Missão Manancial | Meu Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../css/styles.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php $usuario_nome = $_SESSION['Usuario_nome'] ?? 'Usuário'; ?>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <img src="../../assets/logo.png" alt="Logo" style="height:50px; margin-right:1rem;" />
                <span>Missão Manancial</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home me-2"></i>Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php"><i class="fas fa-info-circle me-2"></i>Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php"><i class="fas fa-envelope me-2"></i>Contato</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <a href="dashboard.php" class="btn btn-sm btn-outline-light" title="Voltar para o painel"><i class="fas fa-arrow-left"></i></a>
                    <a href="busca.php" class="text-decoration-none" style="font-size: 1.2rem; color: var(--primary-light);"><i class="fas fa-search"></i></a>
                    <a href="notificacoes.php" class="text-decoration-none" style="font-size: 1.2rem; color: var(--primary-light);"><i class="fas fa-bell"></i></a>
                    <div class="theme-toggle-container">
                        <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
                        <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
                        <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars(substr($usuario_nome, 0, 12)); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-circle me-2"></i>Meu Perfil</a></li>
                            <li><a class="dropdown-item" href="notificacoes.php"><i class="fas fa-bell me-2"></i>Notificações</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="sair.php"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-4">Meu perfil</h1>
            
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

            <!-- back button removed (now in navbar) -->
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/theme.js"></script>
</body>
</html>
