<?php

session_start();

include("conexao.php");


// Receber os dados

$usuario = $_POST["usuario"];
$senha = $_POST["senha"];
$confirmar_senha = $_POST["confirmar_senha"];


// Verificar se as senhas são iguais

if ($senha != $confirmar_senha) {

    echo "<script>
            alert('As senhas não são iguais!');
            window.location.href = 'cadastro.php';
          </script>";

    exit;
}


// Verificar se o usuário já existe

$sqlVerificar = "SELECT id FROM usuarios WHERE usuario = ?";

$stmtVerificar = $conexao->prepare($sqlVerificar);

$stmtVerificar->bind_param("s", $usuario);

$stmtVerificar->execute();

$resultado = $stmtVerificar->get_result();


if ($resultado->num_rows > 0) {

    echo "<script>
            alert('Esse usuário já existe!');
            window.location.href = 'cadastro.php';
          </script>";

    exit;
}


// Cadastrar como usuário normal

$tipo = "usuario";


$sql = "INSERT INTO usuarios
        (usuario, senha, tipo)
        VALUES (?, ?, ?)";


$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sss",
    $usuario,
    $senha,
    $tipo
);


// Salvar

if ($stmt->execute()) {

    echo "<script>
            alert('Conta criada com sucesso! Agora faça login.');
            window.location.href = 'login.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao criar a conta!');
            window.location.href = 'cadastro.php';
          </script>";

}


$stmt->close();

$stmtVerificar->close();

$conexao->close();

?>
