<?php
include("../conexao.php");

auth_require(array('admin'));

$usuario_id = isset($_GET["user_id"]) ? (int) $_GET["user_id"] : 0;
$usuario = null;
$cursos = array();
$videos = array();
$erro = "";

// Buscar lista de usuários para selecionar
$usuarios_list = array();
$sql_users = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_STATUS FROM ID_FIEL WHERE IDF_STATUS = 'aprovado' ORDER BY IDF_NOME LIMIT 100";
$res_users = mysqli_query($conn, $sql_users);
if ($res_users && mysqli_num_rows($res_users) > 0) {
    while ($user = mysqli_fetch_assoc($res_users)) {
        $usuarios_list[] = $user;
    }
}

// Se um usuário foi selecionado, buscar seus dados e conteúdo
if ($usuario_id > 0) {
    $sql_user = "SELECT IDF_ID, IDF_NOME, IDF_EMAIL, IDF_FUNCAO, IDF_FILIAL_ID, IDF_CPF, IDF_TELEFONE, IDF_ENDERECO, IDF_CRIADO_EM FROM ID_FIEL WHERE IDF_ID = $usuario_id LIMIT 1";
    $res_user = mysqli_query($conn, $sql_user);
    if ($res_user && mysqli_num_rows($res_user) > 0) {
        $usuario = mysqli_fetch_assoc($res_user);
        
        // Buscar cursos/matrículas do usuário
        $sql_matriculas = "
            SELECT m.IDMATR_ID, c.IDC_ID, c.IDC_TITULO, m.IDMATR_STATUS, m.IDMATR_PERCENTUAL
            FROM ID_MATRICULA m
            LEFT JOIN ID_CURSO c ON m.IDC_ID = c.IDC_ID
            WHERE m.IDF_ID = $usuario_id
            ORDER BY c.IDC_TITULO
        ";
        $res_matriculas = mysqli_query($conn, $sql_matriculas);
        if ($res_matriculas && mysqli_num_rows($res_matriculas) > 0) {
            while ($curso = mysqli_fetch_assoc($res_matriculas)) {
                $cursos[] = $curso;
            }
        }
        
        // Buscar vídeos disponíveis
        $sql_videos = "SELECT IDCT_ID, IDCT_TITULO, IDC_ID, IDCT_DESCRICAO, IDCT_URL FROM ID_CONTENT WHERE LOWER(IDCT_TIPO) = 'video' ORDER BY IDCT_ORDEM LIMIT 10";
        $res_videos = mysqli_query($conn, $sql_videos);
        if ($res_videos && mysqli_num_rows($res_videos) > 0) {
            while ($video = mysqli_fetch_assoc($res_videos)) {
                $videos[] = $video;
            }
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visão de Usuário - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="../../js/theme.js"></script>
</head>
<body style="transition: background 0.3s ease;">
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo site_url('php/admin/admin_conteudos.php'); ?>">
                <img src="../../assets/logo.png" alt="Logotipo" class="logo me-2" /> Admin Panel
            </a>
            <div class="d-flex gap-2 align-items-center ms-auto">
                <div class="theme-toggle-container">
                    <i class="fas fa-moon theme-icon" style="font-size: 1rem;"></i>
                    <input type="checkbox" id="theme-toggle" class="theme-toggle" aria-label="Alternar tema" style="width: 40px; height: 22px;">
                    <i class="fas fa-sun theme-icon" style="font-size: 1rem;"></i>
                </div>
                <a href="dashboard.php" class="btn btn-sm btn-outline-light">Voltar ao Painel</a>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-5 px-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4"><i class="fas fa-user-eye me-2"></i>Visualizar Como Usuário</h1>
                <p class="text-muted mb-4">Selecione um usuário para ver como ele visualiza a plataforma</p>

                <?php if ($erro !== "") { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($erro); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <!-- SELETOR DE USUÁRIO -->
                <div class="card mb-4" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                    <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Selecionar Usuário</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="d-flex gap-2">
                            <select name="user_id" class="form-select custom-input" required onchange="this.form.submit();">
                                <option value="">-- Escolha um usuário --</option>
                                <?php foreach ($usuarios_list as $u) { ?>
                                    <option value="<?php echo $u["IDF_ID"]; ?>" <?php echo ($usuario_id == $u["IDF_ID"]) ? "selected" : ""; ?>>
                                        <?php echo htmlspecialchars($u["IDF_NOME"]); ?> (<?php echo htmlspecialchars($u["IDF_EMAIL"]); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if ($usuario !== null) { ?>
                    <!-- INFORMAÇÕES DO USUÁRIO -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Perfil do Usuário</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td><strong>Nome:</strong></td>
                                            <td><?php echo htmlspecialchars($usuario["IDF_NOME"]); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>E-mail:</strong></td>
                                            <td><?php echo htmlspecialchars($usuario["IDF_EMAIL"]); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>CPF:</strong></td>
                                            <td><?php echo htmlspecialchars($usuario["IDF_CPF"]); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Telefone:</strong></td>
                                            <td><?php echo htmlspecialchars($usuario["IDF_TELEFONE"]); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Função:</strong></td>
                                            <td><?php echo htmlspecialchars($usuario["IDF_FUNCAO"]); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cadastrado em:</strong></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($usuario["IDF_CRIADO_EM"])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                                <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Cursos Inscritos (<?php echo count($cursos); ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (count($cursos) > 0) { ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($cursos as $curso) { ?>
                                                <div class="list-group-item" style="background: transparent; border-color: rgba(96,165,250,0.1);">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($curso["IDC_TITULO"]); ?></h6>
                                                            <small class="text-muted">Status: <?php echo htmlspecialchars($curso["IDMATR_STATUS"]); ?></small>
                                                        </div>
                                                        <span class="badge bg-info"><?php echo round($curso["IDMATR_PERCENTUAL"]); ?>%</span>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 4px;">
                                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $curso["IDMATR_PERCENTUAL"]; ?>%; background: linear-gradient(90deg, #60a5fa, #a78bfa);"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <p class="text-muted mb-0">Nenhum curso ainda.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VÍDEOS DISPONÍVEIS -->
                    <div class="card" style="background: var(--bg-light); border: 1px solid var(--border-color);">
                        <div class="card-header" style="background: var(--bg-dark); border-bottom: 1px solid var(--border-color);">
                            <h5 class="mb-0"><i class="fas fa-video me-2"></i>Vídeos Disponíveis (Visualização do Usuário)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($videos) > 0) { ?>
                                <div class="row g-3">
                                    <?php foreach ($videos as $video) { ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div style="background: rgba(96,165,250,0.05); border: 1px solid rgba(96,165,250,0.2); border-radius: 12px; overflow: hidden;">
                                                <div style="height: 160px; background: #000; display: flex; align-items: center; justify-content: center; position: relative;">
                                                    <i class="fas fa-play-circle" style="font-size: 3rem; color: #60a5fa; opacity: 0.5;"></i>
                                                    <div style="position: absolute; top: 0; right: 0; background: #60a5fa; color: white; padding: 0.5rem 0.8rem; font-size: 0.8rem; font-weight: 600;">VIDEO</div>
                                                </div>
                                                <div style="padding: 1rem;">
                                                    <h6 class="mb-2"><?php echo htmlspecialchars($video["IDCT_TITULO"]); ?></h6>
                                                    <p class="text-muted mb-3" style="font-size: 0.9rem; max-height: 60px; overflow: hidden;">
                                                        <?php echo htmlspecialchars($video["IDCT_DESCRICAO"]); ?>
                                                    </p>
                                                    <button class="btn btn-sm btn-light w-100" disabled>
                                                        <i class="fas fa-play me-1"></i>Ver Vídeo
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-video" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-3 text-muted">Nenhum vídeo disponível</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- INSTRUÇÕES -->
                    <div class="alert alert-info mt-4" style="background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.2);">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Como funciona esta visualização:</strong> Você está vendo a plataforma conforme o usuário selecionado a enxerga. Isso inclui seus cursos inscritos, progresso e conteúdo disponível. Use isto para entender a experiência do usuário e auxiliá-lo se necessário.
                    </div>
                <?php } else if ($usuario_id > 0) { ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-warning me-2"></i>Usuário não encontrado ou não aprovado.
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
