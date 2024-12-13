<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

$email = $_SESSION['email'];
$sql = "SELECT u.id_grupo 
        FROM usuarios u 
        WHERE u.email = ?";
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

// Buscar todos os usuários
$sql_usuarios = "SELECT idusuario, primeironome, sobrenome FROM usuarios"; 
$result_usuarios = $conexao->query($sql_usuarios);

if ($result_usuarios->num_rows > 0) {
    $usuarios = $result_usuarios->fetch_all(MYSQLI_ASSOC);
} else {
    $usuarios = [];
}

$usuario_atual = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['usuario_id']) && !empty($_POST['usuario_id'])) {
        $usuario_id = $_POST['usuario_id'];

        $sql_usuario = "SELECT idusuario, primeironome, sobrenome, email FROM usuarios WHERE idusuario = ?";
        $stmt = $conexao->prepare($sql_usuario);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result_usuario = $stmt->get_result();
        if ($result_usuario->num_rows > 0) {
            $usuario_atual = $result_usuario->fetch_assoc();
        }
        $stmt->close();
    }

    if (isset($_POST['primeironome']) && isset($_POST['sobrenome']) && isset($_POST['email']) && isset($_POST['usuario_id'])) {
        $primeironome = $_POST['primeironome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $usuario_id = $_POST['usuario_id'];

        if (empty($primeironome) || empty($sobrenome) || empty($email)) {
            $error_message = "Por favor, preencha todos os campos.";
        } else {
            $sql_update = "UPDATE usuarios SET primeironome = ?, sobrenome = ?, email = ? WHERE idusuario = ?";
            $stmt = $conexao->prepare($sql_update);
            $stmt->bind_param("sssi", $primeironome, $sobrenome, $email, $usuario_id);

            if ($stmt->execute()) {
                $success_message = "Usuário atualizado com sucesso!";
            } else {
                $error_message = "Erro ao atualizar usuário: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddusuario.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Editar Usuário</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="usuario">Selecione o Usuário</label>
            <select name="usuario_id" id="usuario" required onchange="this.form.submit()">
                <option value="">Escolha um usuário</option> 
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?php echo $usuario['idusuario']; ?>" <?php echo isset($_POST['usuario_id']) && $_POST['usuario_id'] == $usuario['idusuario'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usuario['primeironome'] . ' ' . $usuario['sobrenome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($_POST['usuario_id']) && !empty($_POST['usuario_id'])): ?>
                <?php if ($usuario_atual): ?>
                    <label for="primeironome">Primeiro Nome:</label>
                    <input type="text" id="primeironome" name="primeironome" value="<?php echo htmlspecialchars($usuario_atual['primeironome']); ?>" required>

                    <label for="sobrenome">Sobrenome:</label>
                    <input type="text" id="sobrenome" name="sobrenome" value="<?php echo htmlspecialchars($usuario_atual['sobrenome']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario_atual['email']); ?>" required>

                    <input type="hidden" name="usuario_id" value="<?php echo $usuario_atual['idusuario']; ?>">
                <?php endif; ?>
            <?php endif; ?>
            
            <button type="submit">Salvar Alterações</button>
        </form>
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
