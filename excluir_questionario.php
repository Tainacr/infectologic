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

$sql_cursos = "SELECT id, nome FROM cursos"; 
$result_cursos = $conexao->query($sql_cursos);

if ($result_cursos->num_rows > 0) {
    $cursos = $result_cursos->fetch_all(MYSQLI_ASSOC);
} else {
    $cursos = [];
}

$questionarios = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['questionario_id']) && !empty($_POST['questionario_id'])) {
        $questionario_id = $_POST['questionario_id'];

        // Excluir o questionário selecionado
        $sql_delete = "DELETE FROM questionario WHERE id = ?";
        $stmt = $conexao->prepare($sql_delete);
        $stmt->bind_param("i", $questionario_id);

        if ($stmt->execute()) {
            $success_message = "Questionário excluído com sucesso!";
        } else {
            $error_message = "Erro ao excluir questionário: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])) {
        $sql_questionarios = "SELECT id, nome FROM questionario WHERE curso_id = ?";
        $stmt = $conexao->prepare($sql_questionarios);
        $stmt->bind_param("i", $_POST['curso_id']);
        $stmt->execute();
        $result_questionarios = $stmt->get_result();
        if ($result_questionarios->num_rows > 0) {
            $questionarios = $result_questionarios->fetch_all(MYSQLI_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Questionário</title>
    <link rel="stylesheet" href="css/styleaddquest.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title">Excluir Questionário</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="curso">Selecione o Curso</label>
            <select name="curso_id" id="curso" required onchange="this.form.submit()">
                <option value="">Escolha um curso</option> 
                <?php foreach ($cursos as $curso): ?>
                    <option value="<?php echo $curso['id']; ?>" <?php echo isset($_POST['curso_id']) && $_POST['curso_id'] == $curso['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($curso['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($_POST['curso_id']) && !empty($_POST['curso_id'])): ?>
                <label for="questionario">Selecione o Questionário para Excluir:</label>
                <select name="questionario_id" id="questionario" required>
                    <option value="">Escolha um questionário</option>
                    <?php foreach ($questionarios as $questionario): ?>
                        <option value="<?php echo $questionario['id']; ?>"><?php echo htmlspecialchars($questionario['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button type="submit">Excluir Questionário</button>
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
