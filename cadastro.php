<?php 
include_once('config.php');

if (isset($_POST['submit'])) {
    $nome = $_POST['firstname'];
    $sobrenome = $_POST['lastname'];
    $email = $_POST['email'];
    $senha = $_POST['password'];
    
    $stmt = $conexao->prepare("INSERT INTO usuarios (primeironome, sobrenome, email, senha) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Erro na preparação: " . $conexao->error);
    }

    $stmt->bind_param("ssss", $nome, $sobrenome, $email, $senha);

    // Executa a declaração
    if ($stmt->execute()) {
        // Se a inserção for bem-sucedida, redireciona com um alerta
        echo "<script>
                alert('Login efetuado com sucesso!');
                window.location.href = 'login.php';
              </script>";
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    // Fecha a declaração
    $stmt->close();
}

// Fecha a conexão
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylecadastro.css">
    <title>Cadastro</title>
</head>

<body>
    <div class="container">
        <div class="form-image">
            <img src="img/estudante2.png" alt="Imagem de estudante">
        </div>

        <div class="form">
            <form action="cadastro.php" method="POST">
                <div class="form-header">
                    <div class="title">
                        <h1>CADASTRE-SE</h1>
                    </div>
                    <div class="login-button">
                        <button type="button" onclick="window.location.href='login.php'">Entrar</button>
                    </div>
                </div>

                <div class="input-wrapper">
                    <div class="input-group-left">
                        <div class="input-box">
                            <label for="firstname">Primeiro nome</label>
                            <input id="firstname" type="text" name="firstname" placeholder="Digite o primeiro nome" required>
                        </div>

                        <div class="input-box">
                            <label for="email">E-mail</label>
                            <input id="email" type="email" name="email" placeholder="Digite o e-mail" required>
                        </div>
                    </div>

                    <div class="input-group-right">
                        <div class="input-box">
                            <label for="lastname">Sobrenome</label>
                            <input id="lastname" type="text" name="lastname" placeholder="Digite o sobrenome" required>
                        </div>

                        <div class="input-box">
                            <label for="password">Senha</label>
                            <input id="password" type="password" name="password" placeholder="Escolha uma senha" required>
                        </div>
                    </div>
                </div>

                <div class="continue-button">
                    <button type="submit" name="submit" id="submit">Continuar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
