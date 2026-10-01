<?php

session_start();

include("conexao.php");

$usuario = $_POST["usuario"];
$senha = $_POST["senha"];

// Procura o usuário no banco
$sql = "SELECT * FROM usuarios WHERE usuario = ? AND senha = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ss",
    $usuario,
    $senha
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $dados = $resultado->fetch_assoc();

    // Gera um novo ID de sessão depois do login
    session_regenerate_id();

    // Salva os dados do usuário na sessão
    $_SESSION["id"] = $dados["id"];
    $_SESSION["usuario"] = $dados["usuario"];
    $_SESSION["tipo"] = $dados["tipo"];

    // Vai para o painel
    header("Location: painel.php");
    exit;

} else {

    echo "<script>
            alert('Usuário ou senha incorretos!');
            window.location.href = 'login.php';
          </script>";

    exit;
}

$stmt->close();
$conexao->close();

?>
