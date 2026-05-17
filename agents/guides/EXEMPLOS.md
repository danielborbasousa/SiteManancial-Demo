# 📚 Exemplos Práticos

## 1️⃣ Exemplo: Adicionar Validação em Formulário

### HTML
```html
<input 
    type="email" 
    name="IDF_EMAIL" 
    class="form-control custom-input"
    placeholder="Seu e-mail"
    autocomplete="email"
    maxlength="100"
    required
>
```

### PHP (Servidor)
```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["IDF_EMAIL"] ?? "");
    
    // Validação
    if (empty($email)) {
        $erro = "E-mail é obrigatório";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido";
    } else {
        // Escapar para banco
        $email = mysqli_real_escape_string($conn, $email);
        
        // Verificar duplicidade
        $sql_check = "SELECT IDF_ID FROM ID_FIEL WHERE IDF_EMAIL = '$email' LIMIT 1";
        $res_check = mysqli_query($conn, $sql_check);
        
        if ($res_check && mysqli_num_rows($res_check) > 0) {
            $erro = "E-mail já registrado";
        } else {
            // Prosseguir com inserção
        }
    }
}
```

---

// (conteúdo resumido — o arquivo original contém mais exemplos práticos e trechos prontos)
