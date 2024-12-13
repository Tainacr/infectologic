<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

$email = $_SESSION['email'];
$sql = "SELECT u.id_grupo FROM usuarios u WHERE u.email = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id_grupo);
$stmt->fetch();
$stmt->close();

if ($id_grupo !== 1) { 
    header('Location: erro_permissao.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $primeiro_nome = $_POST['primeiro_nome'];
    $sobrenome = $_POST['sobrenome'];
    $email_usuario = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $id_grupo = $_POST['id_grupo'];

    if (empty($primeiro_nome) || empty($sobrenome) || empty($email_usuario) || empty($senha) || empty($id_grupo)) {
        $error_message = "Por favor, preencha todos os campos.";
    } else {
        $sql = "INSERT INTO usuarios (primeironome, sobrenome, email, senha, id_grupo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssssi", $primeiro_nome, $sobrenome, $email_usuario, $senha, $id_grupo);

        if ($stmt->execute()) {
            $success_message = "Usuário adicionado com sucesso!";
        } else {
            $error_message = "Erro ao adicionar usuário: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuário</title>
    <link rel="stylesheet" href="css/styleaddusuario.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Gerenciar Usuário</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="primeiro_nome">Primeiro Nome:</label>
            <input type="text" id="primeiro_nome" name="primeiro_nome" required>

            <label for="sobrenome">Sobrenome:</label>
            <input type="text" id="sobrenome" name="sobrenome" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>

            <label for="id_grupo">Grupo de Usuário:</label>
            <select name="id_grupo" id="id_grupo" required>
                <option value="2">Usuário</option>
                <option value="1">Administrador</option>
            </select>

            <button type="submit">Adicionar Usuário</button>
        </form>
    </div>

    <div class= "btn">
            <a href="editar_usuario.php" class="btn-editar">Editar</a>
            <a href="excluir_usuario.php" class="btn-excluir">Excluir</a>
            </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dropdown = document.querySelector('.dropdown');
            const dropbtn = document.querySelector('.dropbtn');

            dropbtn.addEventListener('click', () => {
                dropdown.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>
