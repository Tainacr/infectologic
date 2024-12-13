<?php
session_start();
include('navbar.php');
if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

if (isset($_POST['questionario_id']) && !empty($_POST['questionario_id'])) {
    $questionario_id = $_POST['questionario_id'];

    $sql_questionario = "SELECT id, nome FROM questionario WHERE id = ?";
    $stmt = $conexao->prepare($sql_questionario);
    $stmt->bind_param("i", $questionario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $questionario = $result->fetch_assoc();

    if (!$questionario) {
        echo "Questionário não encontrado.";
        exit();
    }

    $sql_perguntas = "SELECT id, resposta_correta FROM perguntas WHERE questionario_id = ?";
    $stmt_perguntas = $conexao->prepare($sql_perguntas);
    $stmt_perguntas->bind_param("i", $questionario_id);
    $stmt_perguntas->execute();
    $result_perguntas = $stmt_perguntas->get_result();
    $perguntas = $result_perguntas->fetch_all(MYSQLI_ASSOC);

    $acertos = 0;

    foreach ($perguntas as $pergunta) {
        $resposta_usuario = $_POST['pergunta_' . $pergunta['id']] ?? null;
        if ($resposta_usuario && $resposta_usuario === $pergunta['resposta_correta']) {
            $acertos++;
        }
    }

    $stmt->close();
    $stmt_perguntas->close();
} else {
    echo "Dados inválidos.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Questionário</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=HK+Grotesk:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styleresultado.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="card-title">Resultado do Questionário</h1>
            <p class="card-description">Aqui estão seus resultados para "<?php echo htmlspecialchars($questionario['nome']); ?>"</p>

            <div class="result-message <?php echo ($acertos === count($perguntas)) ? 'success' : ($acertos > 0 ? 'good' : 'try-again'); ?>">
                Você acertou <?php echo $acertos; ?> de <?php echo count($perguntas); ?> perguntas.
            </div>

            <?php if ($acertos === count($perguntas)): ?>
                <p class="success">Parabéns! Você acertou todas as perguntas!</p>
            <?php elseif ($acertos > 0): ?>
                <p class="good">Bom trabalho! Continue assim!</p>
            <?php else: ?>
                <p class="try-again">Tente novamente para melhorar sua pontuação!</p>
            <?php endif; ?>

            <div class="card-footer">
                <a href="homepage.php">Voltar para a página inicial</a>
            </div>
        </div>
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
