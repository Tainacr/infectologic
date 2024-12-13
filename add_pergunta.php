<?php 
session_start();

if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

include_once('config.php');

$sql = "SELECT id, nome FROM questionario"; 
$stmt = $conexao->prepare($sql);

if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $questionarios = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("Erro na consulta de questionários.");
}

$message = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $questionario_id = intval($_POST['questionario_id']);
    $pergunta = trim($_POST['pergunta']);
    $alternativa_a = trim($_POST['alternativa_a']);
    $alternativa_b = trim($_POST['alternativa_b']);
    $alternativa_c = trim($_POST['alternativa_c']);
    $alternativa_d = trim($_POST['alternativa_d']);
    $resposta_correta = strtoupper(trim($_POST['resposta_correta'])); 

    if (!in_array($resposta_correta, ['A', 'B', 'C', 'D'])) {
        $message = "Resposta correta inválida! Use apenas A, B, C ou D.";
    } else {
        $sql = "INSERT INTO perguntas (questionario_id, pergunta, alternativa_a, alternativa_b, alternativa_c, alternativa_d, resposta_correta) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conexao->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("issssss", $questionario_id, $pergunta, $alternativa_a, $alternativa_b, $alternativa_c, $alternativa_d, $resposta_correta);

            if ($stmt->execute()) {
                $message = "Pergunta adicionada com sucesso!";
            } else {
                $message = "Erro ao adicionar pergunta.";
            }

            $stmt->close();
        } else {
            $message = "Erro ao preparar a consulta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Perguntas</title>
    <link rel="stylesheet" href="css/styleaddpergunta.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="content">
        <h1 class="main-title">Gerenciar Perguntas</h1>
        
        <?php if ($message): ?>
            <div class="alert <?php echo $message == 'Pergunta adicionada com sucesso!' ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="add_pergunta.php" method="POST">
            <label for="questionario_id">Questionário</label>
            <select name="questionario_id" id="questionario_id" required>
                <?php if (!empty($questionarios)): ?>
                    <?php foreach ($questionarios as $questionario): ?>
                        <option value="<?php echo $questionario['id']; ?>">
                            <?php echo htmlspecialchars($questionario['nome']); ?>  <!-- Apenas o nome -->
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">Nenhum questionário disponível</option>
                <?php endif; ?>
            </select>

            <label for="pergunta">Pergunta</label>
            <input type="text" name="pergunta" id="pergunta" required>

            <label for="alternativa_a">Alternativa A</label>
            <input type="text" name="alternativa_a" id="alternativa_a" required>

            <label for="alternativa_b">Alternativa B</label>
            <input type="text" name="alternativa_b" id="alternativa_b" required>

            <label for="alternativa_c">Alternativa C</label>
            <input type="text" name="alternativa_c" id="alternativa_c" required>

            <label for="alternativa_d">Alternativa D</label>
            <input type="text" name="alternativa_d" id="alternativa_d" required>

            <label for="resposta_correta">Resposta Correta</label>
            <select name="resposta_correta" id="resposta_correta" required>
                <option value="A">Alternativa A</option>
                <option value="B">Alternativa B</option>
                <option value="C">Alternativa C</option>
                <option value="D">Alternativa D</option>
            </select>

            <button type="submit">Adicionar Pergunta</button>
        </form>
    </div>

    <div class="btn">
        <a href="editar_pergunta.php" class="btn-editar">Editar</a>
        <a href="excluir_pergunta.php" class="btn-excluir">Excluir</a>
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
