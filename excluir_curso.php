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

$cursos = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])) {
        $curso_id = $_POST['curso_id'];

        // Excluir o curso selecionado
        $sql_delete = "DELETE FROM cursos WHERE id = ?";
        $stmt = $conexao->prepare($sql_delete);
        $stmt->bind_param("i", $curso_id);

        if ($stmt->execute()) {
            $success_message = "Curso excluído com sucesso!";
        } else {
            $error_message = "Erro ao excluir o curso: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Buscar os cursos para exibir na lista
$sql_cursos = "SELECT id, nome FROM cursos"; 
$result_cursos = $conexao->query($sql_cursos);
if ($result_cursos->num_rows > 0) {
    $cursos = $result_cursos->fetch_all(MYSQLI_ASSOC);
} else {
    $cursos = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Curso</title>
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Excluir Curso</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="curso">Selecione o Curso para Excluir</label>
            <select name="curso_id" id="curso" required>
                <option value="">Escolha um curso</option>
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>">
                        <?php echo htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Excluir Curso</button>
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
