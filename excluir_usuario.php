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


$sql_usuarios = "SELECT idusuario, primeironome, sobrenome FROM usuarios"; 
$result_usuarios = $conexao->query($sql_usuarios);

if ($result_usuarios->num_rows > 0) {
    $usuarios = $result_usuarios->fetch_all(MYSQLI_ASSOC);
} else {
    $usuarios = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['usuario_id']) && !empty($_POST['usuario_id'])) {
        $usuario_id = $_POST['usuario_id'];

        $sql_delete = "DELETE FROM usuarios WHERE idusuario = ?";
        $stmt = $conexao->prepare($sql_delete);
        $stmt->bind_param("i", $usuario_id);

        if ($stmt->execute()) {
            $success_message = "Usuário excluído com sucesso!";
        } else {
            $error_message = "Erro ao excluir usuário: " . $stmt->error;
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
    <title>Excluir Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddusuario.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Excluir Usuário</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="usuario">Selecione o Usuário</label>
            <select name="usuario_id" id="usuario" required>
                <option value="">Escolha um usuário</option> 
                <?php foreach ($usuarios as $usuario): ?>
                    <option value="<?php echo $usuario['idusuario']; ?>">
                        <?php echo htmlspecialchars($usuario['primeironome'] . ' ' . $usuario['sobrenome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit">Excluir Usuário</button>
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
