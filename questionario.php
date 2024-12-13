<?php
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

if (isset($_GET['curso_id'])) {
    $curso_id = $_GET['curso_id'];
} else {
    header('Location: homepage.php');
    exit();
}

$sql_questionario = "SELECT id, nome, descricao FROM questionario WHERE curso_id = ?";
$stmt = $conexao->prepare($sql_questionario);
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$result = $stmt->get_result();
$questionario = $result->fetch_assoc();

if (!$questionario) {
    echo "Questionário não encontrado para este curso.";
    exit();
}

$sql_perguntas = "SELECT id, pergunta, alternativa_a, alternativa_b, alternativa_c, alternativa_d, resposta_correta FROM perguntas WHERE questionario_id = ?";
$stmt_perguntas = $conexao->prepare($sql_perguntas);
$stmt_perguntas->bind_param("i", $questionario['id']);
$stmt_perguntas->execute();
$result_perguntas = $stmt_perguntas->get_result();
$perguntas = $result_perguntas->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$stmt_perguntas->close();

$acertos = 0;
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($perguntas as $pergunta) {
        $resposta_usuario = strtoupper($_POST['pergunta_' . $pergunta['id']] ?? null);
        if ($resposta_usuario && $resposta_usuario == strtoupper($pergunta['resposta_correta'])) {
            $acertos++;
        }
    }

    $mensagem = "Você acertou $acertos pergunta(s)!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($questionario['nome']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stylequest.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title"><?php echo htmlspecialchars($questionario['nome']); ?></h1>
        <p class="description"><?php echo htmlspecialchars($questionario['descricao']); ?></p>

        <?php if ($mensagem): ?>
            <div class="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="questionario_id" value="<?php echo $questionario['id']; ?>">

            <?php foreach ($perguntas as $index => $pergunta): ?>
                <div class="question">
                    <p><strong><?php echo $index + 1; ?>. <?php echo htmlspecialchars($pergunta['pergunta']); ?></strong></p>

                    <label>
                        <input type="radio" name="pergunta_<?php echo $pergunta['id']; ?>" value="a" required>
                        <?php echo htmlspecialchars($pergunta['alternativa_a']); ?>
                    </label><br>

                    <label>
                        <input type="radio" name="pergunta_<?php echo $pergunta['id']; ?>" value="b" required>
                        <?php echo htmlspecialchars($pergunta['alternativa_b']); ?>
                    </label><br>

                    <label>
                        <input type="radio" name="pergunta_<?php echo $pergunta['id']; ?>" value="c" required>
                        <?php echo htmlspecialchars($pergunta['alternativa_c']); ?>
                    </label><br>

                    <label>
                        <input type="radio" name="pergunta_<?php echo $pergunta['id']; ?>" value="d" required>
                        <?php echo htmlspecialchars($pergunta['alternativa_d']); ?>
                    </label><br><br>
                </div>
            <?php endforeach; ?>

            <button type="submit">Enviar Respostas</button>
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
