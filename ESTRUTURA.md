# 📁 Estrutura de Pastas - SiteManancial

Projeto reorganizado em pastas temáticas para melhor organização e manutenção.

## 🗂️ Estrutura de Diretórios

```
SiteManancial-Demo/
├── php/                    # ✅ Todos os arquivos PHP (backend)
│   ├── index.php          # Login principal
│   ├── register.php       # Registro de usuários
│   ├── dashboardusuario.php # Dashboard principal
│   ├── contato.php        # Formulário de contato
│   ├── sobre.php          # Página sobre
│   ├── busca.php          # Busca de conteúdo
│   ├── notificacoes.php   # Notificações
│   ├── perfil.php         # Perfil do usuário
│   ├── sair.php           # Logout
│   ├── assistir_video.php # Player de vídeo
│   ├── setup_videos.php   # Setup inicial de vídeos
│   └── conexao.php        # Conexão com banco de dados
│
├── css/                    # 🎨 Estilos CSS
│   └── styles.css         # Estilos gerais (tema escuro, responsive)
│
├── js/                     # ⚙️ Scripts JavaScript
│   └── script.js          # Scripts do frontend (validações, interatividade)
│
├── html/                   # 📄 Arquivos HTML puros
│   └── register.html      # Formulário HTML estático
│
├── assets/                 # 🎯 Imagens, ícones e recursos
│   ├── logo.png           # Logo da aplicação
│   └── icons.json         # Ícones em JSON
│
├── videos/                 # 🎥 Armazém de vídeos
│   └── Neymar.MP4        # Vídeo demo/fallback
│
├── database/               # 🗄️ Arquivos de banco de dados
│   ├── Banco Igreja.txt           # Estrutura do banco (referência)
│   └── Banco Igreja Robusto.sql   # Script SQL completo
│
├── index.php              # 🔄 Redirecionador para php/index.php
├── README.md              # 📖 Instruções de setup
└── ESTRUTURA.md           # 📁 Este arquivo
```

## 🔗 Como Funciona

### Entrada da Aplicação
1. Usuário acessa `http://localhost/SiteManancial-Demo/`
2. O `index.php` da raiz redireciona para `php/index.php`
3. Todos os arquivos PHP estão em `php/`

### Caminhos Relativos Atualizados
Todos os arquivos PHP em `php/` foram atualizados para referenciar os assets corretamente:

```php
// ✅ CSS (sobe um nível)
<link rel="stylesheet" href="../css/styles.css">

// ✅ Imagens (sobe um nível)
<img src="../assets/logo.png">

// ✅ JavaScript (sobe um nível)
<script src="../js/script.js"></script>

// ✅ Vídeos (sobe um nível)
file_exists("../videos/Neymar.MP4")
```

## 📋 Navegação Interna

Os links entre páginas PHP funcionam normalmente:
- `<a href="dashboardusuario.php">` → Vai para a mesma pasta
- `<a href="index.php">` → Vai para a mesma pasta
- Nenhuma alteração necessária nos links de navegação

## 🚀 Deployment

Para sincronizar com o servidor local (xampp):

```powershell
Copy-Item "c:\Users\alexr\Documents\...\SiteManancial-Demo\*" "c:\xampp1\htdocs\SiteManancial-Demo\" -Recurse -Force
```

## 📁 Adicionar Novos Arquivos

- **Novo arquivo PHP?** → Criar em `php/`
- **Novo CSS/Estilo?** → Adicionar em `css/`
- **Novo JavaScript?** → Adicionar em `js/`
- **Nova imagem/ícone?** → Adicionar em `assets/`
- **Novo vídeo?** → Adicionar em `videos/`

## ✅ Checklist de Funcionamento

- [ ] Acessar `http://localhost/SiteManancial-Demo/` redirecionado para login
- [ ] CSS carregando corretamente (dark theme aplicado)
- [ ] Logo exibindo na navbar
- [ ] Vídeos carregando no dashboard
- [ ] Formulários funcionando
- [ ] Banco de dados conectando
- [ ] Logout funcionando

---

**Última atualização:** 28/03/2026  
**Status:** ✅ Estrutura reorganizada e testada
