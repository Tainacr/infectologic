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

$sql_questionarios = "SELECT id, nome FROM questionario"; 
$result_questionarios = $conexao->query($sql_questionarios);

if ($result_questionarios->num_rows > 0) {
    $questionarios = $result_questionarios->fetch_all(MYSQLI_ASSOC);
} else {
    $questionarios = [];
}

$perguntas = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['pergunta_id']) && !empty($_POST['pergunta_id'])) {
        $pergunta_id = $_POST['pergunta_id'];

        $sql_delete = "DELETE FROM perguntas WHERE id = ?";
        $stmt = $conexao->prepare($sql_delete);
        $stmt->bind_param("i", $pergunta_id);

        if ($stmt->execute()) {
            $success_message = "Pergunta excluída com sucesso!";
        } else {
            $error_message = "Erro ao excluir pergunta: " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_POST['questionario_id']) && !empty($_POST['questionario_id'])) {
        $sql_perguntas = "SELECT id, pergunta FROM perguntas WHERE questionario_id = ?";
        $stmt = $conexao->prepare($sql_perguntas);
        $stmt->bind_param("i", $_POST['questionario_id']);
        $stmt->execute();
        $result_perguntas = $stmt->get_result();
        if ($result_perguntas->num_rows > 0) {
            $perguntas = $result_perguntas->fetch_all(MYSQLI_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Pergunta</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleaddconteudo.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container">
        <h1>Excluir Pergunta</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="questionario">Selecione o Questionário</label>
            <select name="questionario_id" id="questionario" required onchange="this.form.submit()">
                <option value="">Escolha um questionário</option> 
                <?php foreach ($questionarios as $questionario): ?>
                    <option value="<?php echo $questionario['id']; ?>" <?php echo isset($_POST['questionario_id']) && $_POST['questionario_id'] == $questionario['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($questionario['nome']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($_POST['questionario_id']) && !empty($_POST['questionario_id'])): ?>
                <label for="pergunta">Selecione a Pergunta para Excluir:</label>
                <select name="pergunta_id" id="pergunta" required>
                    <option value="">Escolha uma pergunta</option>
                    <?php foreach ($perguntas as $pergunta): ?>
                        <option value="<?php echo $pergunta['id']; ?>"><?php echo htmlspecialchars($pergunta['pergunta']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <button type="submit">Excluir Pergunta</button>
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
