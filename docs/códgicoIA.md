Passo 1: Criando a conta e pegando a API Key (No site do Brevo)
Acesse o site brevo.com e crie uma conta gratuita (você pode usar sua conta do Google para ir mais rápido).
Durante o cadastro, ele vai pedir para você validar o seu e-mail e o seu número de celular. Faça essa validação, pois é ela que garante que você é um humano e libera os envios gratuitos.
Com a conta criada e ativa, olhe para o canto superior direito da tela. Clique no seu nome (ou no nome da sua empresa) para abrir o menu.
Clique na opção SMTP & API.
Vá na aba API keys e clique no botão Create a new API key.
Dê um nome para ela (ex: projeto-faculdade) e clique em Generate.
Importante: Copie a chave gigante que vai aparecer na tela na mesma hora e guarde em um bloco de notas. Ela só aparece uma vez por motivos de segurança!
Passo 2: Protegendo sua chave no config.php
Como conversamos sobre segurança, crie o arquivo config.php na raiz do seu projeto no htdocs para salvar essa chave:
PHP
<?php
// config.php

// Substitua pelo código gigante que você copiou do Brevo
define('BREVO_API_KEY', 'xkeysib-sua-chave-gigante-aqui');

// Substitua pelo e-mail exato que você usou para criar sua conta no Brevo
define('REMETENTE_EMAIL', 'seu_email_da_conta_brevo@gmail.com');
Passo 3: O Código no processar_status.php
Agora, no arquivo PHP que o botão de aprovar ou recusar chama, nós vamos usar o cURL nativo do PHP. Ele vai ler o banco de dados, ver o e-mail do usuário, montar um JSON e mandar para o Brevo entregar.
Veja como fica o arquivo completo:
PHP
<?php
// 1. Importa a chave de segurança
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recebe os dados ocultos dos botões da tela do admin
    $idUsuario = $_POST['usuario_id'];
    $acao = $_POST['acao']; // Espera receber 'aprovar' ou 'recusar'

    // =============================================================
    // BANCO DE DADOS: Aqui você faz o seu SELECT
    // =============================================================
    // Exemplo: $query = "SELECT nome, email FROM usuarios WHERE id = $idUsuario";
    // Pegue os resultados e jogue nessas variáveis abaixo:
    
    $nomeUsuario = "Nome do Aluno"; // Substitua pela variável do seu banco
    $emailUsuario = "email_real_do_aluno@gmail.com"; // O e-mail que o usuário digitou no cadastro

    // =============================================================
    // DEFINIÇÃO DA MENSAGEM (DINÂMICA)
    // =============================================================
    if ($acao === 'aprovar') {
        $assunto = 'Boas notícias! Seu cadastro foi aprovado 🎉';
        $mensagemHtml = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2>Olá, {$nomeUsuario}!</h2>
                <p>Seu cadastro foi revisado pelo nosso administrador e foi <strong>APROVADO</strong> com sucesso.</p>
                <p>Você já pode acessar a nossa plataforma utilizando as suas credenciais.</p>
                <br>
                <p>Atenciosamente,<br>Equipe do Sistema.</p>
            </div>
        ";
    } else {
        $assunto = 'Atualização sobre o seu cadastro';
        $mensagemHtml = "
            <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                <h2>Olá, {$nomeUsuario}.</h2>
                <p>Agradecemos o seu interesse, mas infelizmente o seu cadastro foi <strong>RECUSADO</strong> após a análise dos dados.</p>
                <p>Se tiver dúvidas sobre o motivo da recusa, entre em contato com o suporte do campus.</p>
                <br>
                <p>Atenciosamente,<br>Equipe do Sistema.</p>
            </div>
        ";
    }

    // =============================================================
    // PREPARAÇÃO DO ENVIO PARA A API DO BREVO (cURL Nativo)
    // =============================================================
    
    // Montando o desenho dos dados que o Brevo exige (Padrão JSON deles)
    $dadosEmail = [
        "sender" => [
            "name" => "Painel Administrativo", 
            "email" => REMETENTE_EMAIL // O seu e-mail do config.php
        ],
        "to" => [
            [
                "email" => $emailUsuario, // O e-mail do usuário que estava no banco
                "name" => $nomeUsuario
            ]
        ],
        "subject" => $assunto,
        "htmlContent" => $mensagemHtml
    ];

    // Configurando o cURL para conversar com a API do Brevo
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.api-brevo.com/v3/smtp/email", // Link da API do Brevo
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dadosEmail), // Transforma os dados em texto JSON
        CURLOPT_HTTPHEADER => [
            "api-key: " . BREVO_API_KEY, // Passa a sua chave de autorização de forma limpa
            "Content-Type: application/json",
            "Accept: application/json"
        ],
    ]);

    // Envia a requisição de fato
    $resposta = curl_exec($ch);
    $erroHttp = curl_errno($ch);
    curl_close($ch);

    // Se o cURL não deu erro de conexão, consideramos sucesso e redirecionamos
    if (!$erroHttp) {
        // Redireciona o admin de volta para a tela de listagem com um aviso de sucesso
        header("Location: tela_admin.php?status=sucesso");
        exit();
    } else {
        echo "Erro técnico na comunicação com o servidor de e-mail.";
    }
}