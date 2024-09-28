<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylelogin.css">
    <title>Login</title>
</head>
<body>
    <div class="mainLogin">
        <div class="loginEsquerda">
            <h1>Acesse nosso site para aprender<br>mais sobre microbiologia</h1>
            <img src="img/estudante.svg" class="loginEsquerdaImg" alt="estudante">
        </div>
        <div class="loginDireita">
            <div class="card">
                <h1>LOGIN</h1>
                <form action="teste.php" method="post">
                    <div class="textField">
                        <label for="email">E-mail</label>
                        <input type="text" name="email" placeholder="Email" required>
                    </div>
                    <div class="textField">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" placeholder="Senha" required>
                    </div>
                    <div class="inscrever">
                        <a href="cadastro.php">Ainda não é inscrito?</a>
                    </div>
                    <div class="btn-container">
                        <button class="btnLogin" type="submit" name="submit" value="enviar">Acessar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
