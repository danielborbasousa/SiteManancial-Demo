<?php
session_start();
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}

if(!isset($_SESSION["Usuario_tipo"]) || $_SESSION["Usuario_tipo"] !== "admin") {
    header("location:dashboardusuario.php");
    exit;
}

include("conexao.php");

$admin_email = $_SESSION["Usuario_logado"];
$mensagem = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = isset($_POST["acao_admin"]) ? $_POST["acao_admin"] : "";

    // Gestão de usuários (somente fiéis) com escala: FIEL -> EDITOR -> ADMIN(SUPER)
    if($acao == "alterar_perfil_fiel") {
        $idf_id = intval($_POST["idf_id"]);
        $destino = mysqli_real_escape_string($conn, $_POST["destino_perfil"]);

        $res_fiel = mysqli_query($conn, "SELECT * FROM ID_FIEL WHERE IDF_ID = $idf_id LIMIT 1");
        if($res_fiel && mysqli_num_rows($res_fiel) == 1) {
            $fiel = mysqli_fetch_assoc($res_fiel);
            $email = mysqli_real_escape_string($conn, $fiel["IDF_EMAIL"]);
            $nome = mysqli_real_escape_string($conn, $fiel["IDF_NOME"]);
            $filial = mysqli_real_escape_string($conn, $fiel["IDF_FILIAL"]);
            $senha = mysqli_real_escape_string($conn, $fiel["IDF_SENHA"]);

            if($destino == "FIEL") {
                if(mysqli_query($conn, "DELETE FROM ID_ADMIN WHERE IDA_EMAIL = '$email'")) {
                    $mensagem = "Perfil atualizado para Fiel com sucesso.";
                }
            } elseif($destino == "EDITOR" || $destino == "SUPER") {
                $res_admin = mysqli_query($conn, "SELECT * FROM ID_ADMIN WHERE IDA_EMAIL = '$email'");

                if($res_admin && mysqli_num_rows($res_admin) > 0) {
                    if(mysqli_query($conn, "UPDATE ID_ADMIN SET IDA_NIVEL = '$destino' WHERE IDA_EMAIL = '$email'")) {
                        if($destino == "SUPER") {
                            $mensagem = "Perfil atualizado para Admin com sucesso.";
                        } else {
                            $mensagem = "Perfil atualizado para Editor com sucesso.";
                        }
                    }
                } else {
                    $sql = "INSERT INTO ID_ADMIN (IDA_NOME, IDA_EMAIL, IDA_FILIAL, IDA_SENHA, IDA_NIVEL) VALUES ('$nome', '$email', '$filial', '$senha', '$destino')";
                    if(mysqli_query($conn, $sql)) {
                        if($destino == "SUPER") {
                            $mensagem = "Fiel promovido para Admin com sucesso.";
                        } else {
                            $mensagem = "Fiel promovido para Editor com sucesso.";
                        }
                    } else {
                        $mensagem = "Erro ao alterar perfil: " . mysqli_error($conn);
                    }
                }
            } else {
                $mensagem = "Destino de perfil inválido.";
            }
        }
    }

}

// 8) Relatorios simples
$total_usuarios = 0;
$total_admins = 0;
$total_cursos = 0;
$total_matriculas = 0;
$total_conteudos = 0;

$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ID_FIEL");
if($r) { $row = mysqli_fetch_assoc($r); $total_usuarios = $row["total"]; }
$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ID_ADMIN");
if($r) { $row = mysqli_fetch_assoc($r); $total_admins = $row["total"]; }
$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ID_CURSO");
if($r) { $row = mysqli_fetch_assoc($r); $total_cursos = $row["total"]; }
$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ID_MATRICULA");
if($r) { $row = mysqli_fetch_assoc($r); $total_matriculas = $row["total"]; }
$r = mysqli_query($conn, "SELECT COUNT(*) AS total FROM ID_CONTENT");
if($r) { $row = mysqli_fetch_assoc($r); $total_conteudos = $row["total"]; }

$lista_fieis = mysqli_query($conn, "
    SELECT f.IDF_ID, f.IDF_NOME, f.IDF_EMAIL, a.IDA_ID, a.IDA_NIVEL
    FROM ID_FIEL f
    LEFT JOIN ID_ADMIN a ON a.IDA_EMAIL = f.IDF_EMAIL
    ORDER BY f.IDF_ID DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - Missão Evangélica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .admin-acao-box {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 0.5rem;
            align-items: center;
            min-width: 360px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboardadmin.php">
                <img src="../assets/logo.png" alt="Logotipo" class="logo me-2" /> Painel Administrativo
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardadmin.php">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php">Contato</a></li>
                </ul>
                <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <?php if($mensagem != ""): ?>
            <div id="adminAlert" class="alert alert-info d-flex justify-content-between align-items-center">
                <span><?php echo htmlspecialchars($mensagem); ?></span>
                <button type="button" id="fecharAdminAlert" class="btn btn-sm btn-outline-dark">X</button>
            </div>
        <?php endif; ?>

        <div class="auth-container mb-4" style="max-width: 1200px; margin: 0 auto;">
            <h1 class="mb-3">Painel Admin</h1>
            <p class="mb-1"><strong>Login:</strong> <?php echo htmlspecialchars($admin_email); ?></p>
            <p class="text-info mb-0">Gestão básica de fiéis/admins e relatórios.</p>
        </div>

        <div class="row g-3 mb-4" style="max-width: 1200px; margin: 0 auto;">
            <div class="col-md-2"><div class="auth-container p-3"><strong>Usuários</strong><div><?php echo $total_usuarios; ?></div></div></div>
            <div class="col-md-2"><div class="auth-container p-3"><strong>Admins</strong><div><?php echo $total_admins; ?></div></div></div>
            <div class="col-md-2"><div class="auth-container p-3"><strong>Cursos</strong><div><?php echo $total_cursos; ?></div></div></div>
            <div class="col-md-3"><div class="auth-container p-3"><strong>Matrículas</strong><div><?php echo $total_matriculas; ?></div></div></div>
            <div class="col-md-3"><div class="auth-container p-3"><strong>Conteúdos</strong><div><?php echo $total_conteudos; ?></div></div></div>
        </div>

        <div class="auth-container mb-4" style="max-width: 1200px; margin: 0 auto;">
            <h4>Gestão de Fiéis</h4>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead><tr><th>ID</th><th>Nome</th><th>Email</th><th>Perfil</th><th>Promover/Rebaixar</th></tr></thead>
                    <tbody>
                    <?php if($lista_fieis): while($f = mysqli_fetch_assoc($lista_fieis)): ?>
                        <tr>
                            <td><?php echo $f["IDF_ID"]; ?></td>
                            <td><?php echo htmlspecialchars($f["IDF_NOME"]); ?></td>
                            <td><?php echo htmlspecialchars($f["IDF_EMAIL"]); ?></td>
                            <td>
                                <?php if($f["IDA_ID"]): ?>
                                    <?php if($f["IDA_NIVEL"] === "SUPER"): ?>
                                        Admin
                                    <?php else: ?>
                                        Editor
                                    <?php endif; ?>
                                <?php else: ?>
                                    Fiel
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $perfil_atual = "FIEL";
                                    if($f["IDA_ID"]) {
                                        $perfil_atual = $f["IDA_NIVEL"]; // EDITOR ou SUPER
                                    }
                                ?>
                                <div class="admin-acao-box">
                                    <select id="acao_<?php echo $f['IDF_ID']; ?>" class="form-select form-select-sm" onchange="atualizarOpcoes(<?php echo $f['IDF_ID']; ?>)" data-perfil="<?php echo $perfil_atual; ?>">
                                        <?php if($perfil_atual === "FIEL"): ?>
                                            <option value="PROMOVER">Promover</option>
                                        <?php elseif($perfil_atual === "EDITOR"): ?>
                                            <option value="PROMOVER">Promover</option>
                                            <option value="REBAIXAR">Rebaixar</option>
                                        <?php else: ?>
                                            <option value="REBAIXAR">Rebaixar</option>
                                        <?php endif; ?>
                                    </select>

                                    <select id="destino_<?php echo $f['IDF_ID']; ?>" class="form-select form-select-sm" data-perfil="<?php echo $perfil_atual; ?>"></select>

                                    <button type="button" class="btn btn-light btn-sm" onclick="confirmarFluxo(<?php echo $f['IDF_ID']; ?>, '<?php echo htmlspecialchars($f['IDF_EMAIL'], ENT_QUOTES); ?>')">Confirmar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="confirmacaoBox" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999;">
            <div style="max-width:520px; margin:12% auto; background:#1e293b; color:#fff; border-radius:10px; padding:20px; border:1px solid #334155;">
                <h5 class="mb-3">Confirmação</h5>
                <p id="confirmacaoTexto" class="mb-3"></p>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="fecharConfirmacao()">Não</button>
                    <button type="button" class="btn btn-light btn-sm" onclick="enviarConfirmacao()">Sim</button>
                </div>
            </div>
        </div>

        <form id="formAcaoAdmin" method="POST" style="display:none;">
            <input type="hidden" name="acao_admin" value="alterar_perfil_fiel">
            <input type="hidden" name="idf_id" id="form_idf_id" value="">
            <input type="hidden" name="destino_perfil" id="form_destino_perfil" value="">
        </form>

    </main>
    <script>
        function nomePerfil(valor) {
            if (valor === 'SUPER') return 'Admin';
            if (valor === 'EDITOR') return 'Editor';
            return 'Fiel';
        }

        function atualizarOpcoes(idFiel) {
            const selectAcao = document.getElementById('acao_' + idFiel);
            const selectDestino = document.getElementById('destino_' + idFiel);
            const perfilAtual = selectAcao.getAttribute('data-perfil');
            const acao = selectAcao.value;

            let opcoes = [];

            if (perfilAtual === 'FIEL') {
                opcoes = [
                    { valor: 'EDITOR', texto: 'Editor' },
                    { valor: 'SUPER', texto: 'Admin' }
                ];
            } else if (perfilAtual === 'EDITOR') {
                if (acao === 'PROMOVER') {
                    opcoes = [{ valor: 'SUPER', texto: 'Admin' }];
                } else {
                    opcoes = [{ valor: 'FIEL', texto: 'Fiel' }];
                }
            } else {
                opcoes = [
                    { valor: 'EDITOR', texto: 'Editor' },
                    { valor: 'FIEL', texto: 'Fiel' }
                ];
            }

            selectDestino.innerHTML = '';
            opcoes.forEach(function(op) {
                const option = document.createElement('option');
                option.value = op.valor;
                option.textContent = op.texto;
                selectDestino.appendChild(option);
            });
        }

        function confirmarFluxo(idFiel, email) {
            const acao = document.getElementById('acao_' + idFiel).value;
            const destino = document.getElementById('destino_' + idFiel).value;
            const tipoAcao = acao === 'PROMOVER' ? 'promover' : 'rebaixar';
            confirmarAcao(idFiel, email, destino, tipoAcao);
        }

        function confirmarAcao(idFiel, email, destino, tipoAcao) {
            const texto = 'Confirmar que irá ' + tipoAcao + ' o fiel "' + email + '" para "' + nomePerfil(destino) + '"?';
            document.getElementById('confirmacaoTexto').textContent = texto;
            document.getElementById('form_idf_id').value = idFiel;
            document.getElementById('form_destino_perfil').value = destino;
            document.getElementById('confirmacaoBox').style.display = 'block';
        }

        function fecharConfirmacao() {
            document.getElementById('confirmacaoBox').style.display = 'none';
        }

        function enviarConfirmacao() {
            document.getElementById('formAcaoAdmin').submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('[id^="acao_"]');
            selects.forEach(function(select) {
                const idFiel = select.id.replace('acao_', '');
                atualizarOpcoes(idFiel);
            });

            const btnFechar = document.getElementById('fecharAdminAlert');
            const boxAlerta = document.getElementById('adminAlert');
            if (btnFechar && boxAlerta) {
                btnFechar.addEventListener('click', function() {
                    boxAlerta.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>
