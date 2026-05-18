<?php
include("../conexao.php");

auth_require(array('admin'));

$mensagem = "";
$erro = "";
$modo = isset($_GET["modo"]) ? $_GET["modo"] : "listar";
$usuario_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$usuario_edicao = null;
$admin_id = isset($_SESSION["Usuario_id"]) ? (int) $_SESSION["Usuario_id"] : 0;

// Processar atualização de usuário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["acao"])) {
    $acao = $_POST["acao"];

    if ($acao === "excluir_usuario") {
        $id = (int) $_POST["usuario_id"];

        if ($id <= 0) {
            $erro = "Usuário inválido.";
        } else {
            if ($id === $admin_id) {
                $erro = "Você não pode excluir o seu próprio usuário.";
            } else {
                $sql_delete = "DELETE FROM ID_FIEL WHERE IDF_ID = $id LIMIT 1";
                if (mysqli_query($conn, $sql_delete)) {
                    header("location:admin_gerenciar_usuarios.php");
                    exit;
                } else {
                    $erro = "Erro ao excluir usuário: " . mysqli_error($conn);
                }
            }
        }
    
    } elseif ($acao === "editar_usuario") {
        $id = (int) $_POST["usuario_id"];
        $nome = mysqli_real_escape_string($conn, trim($_POST["nome"]));
        $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
        $telefone = mysqli_real_escape_string($conn, trim($_POST["telefone"]));
        $funcao = mysqli_real_escape_string($conn, trim($_POST["funcao"]));
        $status = mysqli_real_escape_string($conn, trim($_POST["status"]));
        
        if (empty($nome) || empty($email)) {
            $erro = "Nome e email são obrigatórios.";
        } else {
            $sql = "UPDATE ID_FIEL SET IDF_NOME = '$nome', IDF_EMAIL = '$email', IDF_TELEFONE = '$telefone', IDF_FUNCAO = '$funcao', IDF_STATUS = '$status' WHERE IDF_ID = $id LIMIT 1";
            if (mysqli_query($conn, $sql)) {
                $mensagem = "Usuário atualizado com sucesso!";
                $modo = "listar";
            } else {
                $erro = "Erro ao atualizar usuário: " . mysqli_error($conn);
            }
        }
    } elseif ($acao === "editar_perfil") {
        $id = (int) $_POST["usuario_id"];
        $nome = mysqli_real_escape_string($conn, trim($_POST["nome"]));
        $telefone = mysqli_real_escape_string($conn, trim($_POST["telefone"]));
        
        if (empty($nome)) {
            $erro = "Nome é obrigatório.";
        } else {
            $sql = "UPDATE ID_FIEL SET IDF_NOME = '$nome', IDF_TELEFONE = '$telefone' WHERE IDF_ID = $id LIMIT 1";
            if (mysqli_query($conn, $sql)) {
                $_SESSION["Usuario_nome"] = $nome;
                $mensagem = "Perfil atualizado com sucesso!";
                $modo = "listar";
            } else {
                $erro = "Erro ao atualizar perfil: " . mysqli_error($conn);
            }
        }
    }
}

// Buscar dados do usuário para edição
if ($modo === "editar" && $usuario_id > 0) {
    $sql = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FUNCAO, IDF_STATUS FROM ID_FIEL WHERE IDF_ID = $usuario_id LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $usuario_edicao = mysqli_fetch_assoc($res);
    } else {
        $modo = "listar";
        $erro = "Usuário não encontrado.";
    }
}

// Buscar informações do admin logado
$sql_admin = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FUNCAO, IDF_STATUS FROM ID_FIEL WHERE IDF_ID = $admin_id LIMIT 1";
$res_admin = mysqli_query($conn, $sql_admin);
$admin_info = $res_admin && mysqli_num_rows($res_admin) > 0 ? mysqli_fetch_assoc($res_admin) : null;

// Buscar todos os usuários
$sql_usuarios = "
    SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_TELEFONE, IDF_CPF, IDF_FUNCAO, IDF_STATUS, IDF_CRIADO_EM 
    FROM ID_FIEL 
    ORDER BY IDF_CRIADO_EM DESC
";
$res_usuarios = mysqli_query($conn, $sql_usuarios);
$usuarios = array();
if ($res_usuarios && mysqli_num_rows($res_usuarios) > 0) {
    while ($user = mysqli_fetch_assoc($res_usuarios)) {
        $usuarios[] = $user;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
    <style>
        .user-card { background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; transition: all 0.3s; }
        .user-card:hover { border-color: var(--primary-light); transform: translateY(-4px); }
        .status-badge { display: inline-block; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-aprovado { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-pendente { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-negado { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .meu-perfil-card { background: linear-gradient(135deg, rgba(59,130,246,0.15) 0%, rgba(16,185,129,0.1) 100%); border: 2px solid var(--primary-light); border-radius: 12px; padding: 2rem; }
        .table-responsive { background: var(--bg-light); border-radius: 10px; overflow: hidden; }
    </style>
</head>
<body class="admin-layout" style="transition: background 0.3s ease;">
    <?php include __DIR__ . '/header_admin.php'; ?>

    <main class="container-fluid py-5 px-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="mb-2"><i class="fas fa-users me-2"></i>Gerenciamento de Usuários</h1>
                <p class="text-muted">Edite informações de usuários e configure seus perfis</p>
            </div>
        </div>

        <?php if ($mensagem !== "") { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($mensagem); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <?php if ($erro !== "") { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($erro); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <!-- MEU PERFIL -->
        <?php if ($admin_info && $modo === "listar") { ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="meu-perfil-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div style="width: 80px; height: 80px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--bg-dark); font-size: 2rem; font-weight: bold; margin-right: 1.5rem;">
                                    <?php echo strtoupper(substr($admin_info["IDF_NOME"], 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="mb-1"><?php echo htmlspecialchars($admin_info["IDF_NOME"]); ?></h3>
                                    <p class="mb-1" style="color: var(--text-muted);">
                                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($admin_info["IDF_EMAIL"]); ?>
                                    </p>
                                    <p style="color: var(--text-muted);">
                                        <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($admin_info["IDF_TELEFONE"] ?? "N/A"); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editarPerfilModal">
                                <i class="fas fa-edit me-2"></i>Editar Meu Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- MODO EDITAR USUÁRIO -->
        <?php if ($modo === "editar" && $usuario_edicao) { ?>
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="user-card">
                    <h3 class="mb-4"><i class="fas fa-edit me-2"></i>Editar Usuário</h3>
                    <form method="POST">
                        <input type="hidden" name="acao" value="editar_usuario">
                        <input type="hidden" name="usuario_id" value="<?php echo $usuario_edicao['IDF_ID']; ?>">

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario_edicao['IDF_NOME']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario_edicao['IDF_EMAIL']); ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($usuario_edicao['IDF_TELEFONE']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="funcao" class="form-label">Função</label>
                                <input type="text" class="form-control" id="funcao" name="funcao" value="<?php echo htmlspecialchars($usuario_edicao['IDF_FUNCAO']); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pendente" <?php echo $usuario_edicao['IDF_STATUS'] === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                <option value="aprovado" <?php echo $usuario_edicao['IDF_STATUS'] === 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                <option value="negado" <?php echo $usuario_edicao['IDF_STATUS'] === 'negado' ? 'selected' : ''; ?>>Negado</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario_edicao['IDF_CPF']); ?>" disabled>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <a href="admin_gerenciar_usuarios.php" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- MODO LISTAR USUÁRIOS -->
        <?php if ($modo === "listar") { ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="color: var(--text-main);">
                        <thead style="background: var(--bg-dark);">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th>Função</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $user) { 
                                $status_class = "status-" . $user['IDF_STATUS'];
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user["IDF_NOME"]); ?></strong></td>
                                <td><?php echo htmlspecialchars($user["IDF_EMAIL"]); ?></td>
                                <td><?php echo htmlspecialchars($user["IDF_TELEFONE"] ?? "-"); ?></td>
                                <td><?php echo htmlspecialchars($user["IDF_FUNCAO"] ?? "-"); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($user["IDF_STATUS"]); ?>
                                    </span>
                                </td>
                                <td><small><?php echo date('d/m/Y', strtotime($user["IDF_CRIADO_EM"])); ?></small></td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="admin_gerenciar_usuarios.php?modo=editar&id=<?php echo $user['IDF_ID']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ((int) $user['IDF_ID'] !== $admin_id) { ?>
                                            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');" style="display:inline;">
                                                <input type="hidden" name="acao" value="excluir_usuario">
                                                <input type="hidden" name="usuario_id" value="<?php echo $user['IDF_ID']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
    </main>

    <!-- MODAL EDITAR PERFIL -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar Meu Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="editar_perfil">
                        <input type="hidden" name="usuario_id" value="<?php echo $admin_info['IDF_ID']; ?>">

                        <div class="mb-3">
                            <label for="nome_perfil" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome_perfil" name="nome" value="<?php echo htmlspecialchars($admin_info['IDF_NOME']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email_perfil" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_perfil" value="<?php echo htmlspecialchars($admin_info['IDF_EMAIL']); ?>" disabled>
                            <small class="text-muted">Email não pode ser alterado</small>
                        </div>

                        <div class="mb-3">
                            <label for="telefone_perfil" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="telefone_perfil" name="telefone" value="<?php echo htmlspecialchars($admin_info['IDF_TELEFONE']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin_info['IDF_CPF']); ?>" disabled>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
