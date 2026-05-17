<?php
$painel_link = "dashboard.php";
session_start();
include("../conexao.php");
auth_require();

if(!isset($_SESSION["Usuario_logado"])) {
    header("location:../../login.php");
    exit;
}
$titulo_nav = "Missao Evangelica Manancial da Esperança";
if(isset($_SESSION["Usuario_tipo"]) && $_SESSION["Usuario_tipo"] === "admin") {
    $painel_link = "../admin/admin_conteudos.php";
    $titulo_nav = "Painel Administrativo";
}

// Endpoint real do Formspree
$formspreeEndpoint = "https://formspree.io/f/xreoyrka";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missão Manancial | Contato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
        <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                    <img src="../../assets/logo.png" alt="Logo" style="height:50px; margin-right:1rem;" />
                    <span>Missão Manancial</span>
                </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $painel_link; ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contato.php">Contato</a></li>
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
                            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars(substr($_SESSION['Usuario_nome'] ?? 'Usuário', 0, 12)); ?>
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
            <h1 class="mb-3">Fale conosco</h1>
            <p class="mb-1"><strong>Email:</strong> contato@igreja.com</p>
            <p class="mb-1"><strong>Telefone:</strong> (11) 99999-9999</p>
            <p class="mb-3"><strong>Endereço:</strong> Rua Exemplo, 100 - Centro</p>

                        <h5 class="mt-4">Formulário</h5>
                        <form id="contactForm" method="POST" action="<?php echo $formspreeEndpoint; ?>" class="mt-3" autocomplete="off">
                <div class="mb-3">
                                        <input type="text" id="nome" name="nome" class="form-control custom-input" placeholder="Seu nome" required maxlength="80">
                </div>
                <div class="mb-3">
                                        <input type="email" id="email" name="email" class="form-control custom-input" placeholder="Seu e-mail" required maxlength="120">
                </div>
                <div class="mb-3">
                                        <textarea id="mensagem" name="mensagem" class="form-control custom-input" rows="4" placeholder="Sua mensagem" required maxlength="500"></textarea>
                </div>
                <button type="submit" class="btn btn-light">Enviar</button>
            </form>
            <!-- back button removed (now in navbar) -->
        </div>
    </main>

    <script>
        (function(){
            const form = document.getElementById("contactForm");
            if (!form) return;
            const emailInput = document.getElementById("email");
            const messageInput = document.getElementById("mensagem");

            const emailRegex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
            const fakeEmails = ["test@test.com", "a@a.com", "email@email.com"];
            const forbiddenWords = [
                "idiota", "burro", "lixo", "merda", "porra",
                "script", "<script", "select *", "drop table",
                "http://", "https://", "spam", "hack"
            ];

            form.addEventListener("submit", function (event) {
                const email = (emailInput.value || '').trim().toLowerCase();
                const message = (messageInput.value || '').toLowerCase();
                const action = form.getAttribute("action") || '';

                if (action.indexOf("SEU_ID_AQUI") !== -1) {
                    alert("Configure seu endpoint do Formspree antes de enviar.");
                    event.preventDefault();
                    return;
                }

                if (!emailRegex.test(email)) {
                    alert("Por favor, insira um email valido.");
                    event.preventDefault();
                    return;
                }

                if (fakeEmails.includes(email)) {
                    alert("Email invalido ou generico.");
                    event.preventDefault();
                    return;
                }

                for (const word of forbiddenWords) {
                    if (message.includes(word)) {
                        alert("Sua mensagem contem termos inadequados ou suspeitos.");
                        event.preventDefault();
                        return;
                    }
                }

                // Mantem envio na mesma aba e marca retorno para ir ao inicio
                sessionStorage.setItem("formspreeRetorno", "1");
            });

            // Ao voltar da tela do Formspree, redireciona para a tela inicial
            window.addEventListener("pageshow", function(event) {
                const veioDoFormspree = (document.referrer || '').indexOf("formspree.io") !== -1;
                const deveIrParaInicio = sessionStorage.getItem("formspreeRetorno") === "1";

                if (veioDoFormspree && deveIrParaInicio) {
                    sessionStorage.removeItem("formspreeRetorno");
                    form.reset();
                    window.location.replace("<?php echo $painel_link; ?>");
                    return;
                }

                if (event.persisted) {
                    form.reset();
                }
            });
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/theme.js"></script>
</body>
</html>
