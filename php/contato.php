<?php
session_start();
if(!isset($_SESSION["Usuario_logado"])) {
    header("location:index.php");
    exit;
}

// Endpoint real do Formspree
$formspreeEndpoint = "https://formspree.io/f/xblaplaq";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Missao Evangelica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark w-100 p-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboardusuario.php">
                <img src="../assets/logo.png" alt="Logotipo" class="logo me-2" /> Missao Evangelica Manancial da Esperança
            </a>
            <div class="collapse navbar-collapse show">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboardusuario.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre.php">Sobre</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contato.php">Contato</a></li>
                </ul>
                <a href="sair.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="auth-container" style="max-width: 900px; margin: 0 auto;">
            <h1 class="mb-3">Contato</h1>
            <p class="mb-1"><strong>Email:</strong> contato@igreja.com</p>
            <p class="mb-1"><strong>Telefone:</strong> (11) 99999-9999</p>
            <p class="mb-3"><strong>Endereco:</strong> Rua Exemplo, 100 - Centro</p>

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
            <a href="dashboardusuario.php" class="btn btn-secondary mt-3">Voltar para o painel</a>
        </div>
    </main>

        <script>
        const form = document.getElementById("contactForm");
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
            const email = emailInput.value.trim().toLowerCase();
            const message = messageInput.value.toLowerCase();
            const action = form.getAttribute("action");

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
            const veioDoFormspree = document.referrer.indexOf("formspree.io") !== -1;
            const deveIrParaInicio = sessionStorage.getItem("formspreeRetorno") === "1";

            if (veioDoFormspree && deveIrParaInicio) {
                sessionStorage.removeItem("formspreeRetorno");
                form.reset();
                window.location.replace("dashboardusuario.php");
                return;
            }

            if (event.persisted) {
                form.reset();
            }
        });
        </script>
</body>
</html>
