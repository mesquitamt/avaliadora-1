<?php
session_start();
require 'perguntas.php';

// inicializa sessão se ainda não começou
if (!isset($_SESSION['iniciado'])) {
    $_SESSION['iniciado'] = false;
    $_SESSION['indice']   = 0;
    $_SESSION['pontuacao'] = 0;
}

// reiniciar quiz
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$feedback = "";

// iniciar quiz
if (isset($_POST['iniciar'])) {
    $_SESSION['iniciado']  = true;
    $_SESSION['indice']    = 0;
    $_SESSION['pontuacao'] = 0;
}

// verifica envio da resposta
if ($_SESSION['iniciado'] && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['iniciar'])) {
    $indice   = $_SESSION['indice'];
    $resposta = $_POST['resposta'] ?? null;

    if ($resposta !== null) {
        if ($resposta == $perguntas[$indice]['correta']) {
            $_SESSION['pontuacao']++;
            $feedback = "🔴⚫ Resposta correta!";
        } else {
            $correta  = $perguntas[$indice]['opcoes'][$perguntas[$indice]['correta']];
            $feedback = "❌ Resposta errada, por acaso tu é vascaino? A correta era: $correta";
        }
        $_SESSION['indice']++;
    }
}

// Verifica se o quiz acabou 
$quizFinalizado = $_SESSION['iniciado'] && $_SESSION['indice'] >= count($perguntas);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Mini Quiz Backend</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    
    <h1>Mini Quiz do Flamenguista</h1>
<img src="img/jovem.png" width="220px" right="220px" alt="">
    <?php if (!$_SESSION['iniciado']): ?>
        
        <form method="post">
            <button type="submit" name="iniciar" class="btn">🎮 Iniciar Quiz</button>
        </form>

    <?php elseif ($quizFinalizado): ?>
        
        <h2> 🦅 Quiz Finalizado! </h2>
        <p>Sua pontuação final: <strong><?= $_SESSION['pontuacao'] ?></strong> de <?= count($perguntas) ?></p>
        <a href="index.php?reset=1" class="btn">Reiniciar Quiz</a>

        <!-- Esse código mostra uma pergunta do quiz, exibe as opções de resposta em formato de múltipla escolha-->
    <?php else: ?>
        
        <?php if ($feedback): ?>
            <p class="feedback"><?= $feedback ?></p>
        <?php endif; ?>

        <?php
        $indice = $_SESSION['indice'];
        $perguntaAtual = $perguntas[$indice];
        ?>

        <h2><?= $perguntaAtual['pergunta'] ?></h2>
        <form method="post">
            <?php foreach ($perguntaAtual['opcoes'] as $i => $opcao): ?>
                <label>
                    <input type="radio" name="resposta" value="<?= $i ?>" required>
                    <?= $opcao ?>
                </label><br>
            <?php endforeach; ?>
            <button type="submit" class="btn">Responder</button>
        </form>
    <?php endif; ?>

</div>
</body>
</html>
