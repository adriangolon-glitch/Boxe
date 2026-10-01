<?php

session_start();

include("conexao.php");

// ========================================
// SOMENTE ADMIN PODE CADASTRAR LUTAS
// ========================================

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {

    header("Location: painel.php");
    exit;
}

// ========================================
// RECEBE OS DADOS DO FORMULÁRIO
// ========================================

$lutador1 = $_POST["lutador1"];
$lutador2 = $_POST["lutador2"];
$data_luta = $_POST["data_luta"];
$resultado = $_POST["resultado"];
$metodo = $_POST["metodo"];

// ========================================
// VERIFICA SE OS LUTADORES SÃO DIFERENTES
// ========================================

if ($lutador1 == $lutador2) {

    echo "<script>
            alert('Os dois lutadores não podem ser iguais!');
            window.location.href = 'cadastrar_luta.php';
          </script>";

    exit;
}

// ========================================
// CADASTRA A LUTA
// ========================================

$sql = "INSERT INTO lutas
        (lutador1, lutador2, data_luta, resultado, metodo)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssss",
    $lutador1,
    $lutador2,
    $data_luta,
    $resultado,
    $metodo
);

// ========================================
// EXECUTA O CADASTRO
// ========================================

if ($stmt->execute()) {

    echo "<script>
            alert('Luta cadastrada com sucesso!');
            window.location.href = 'lutas.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao cadastrar a luta!');
            window.location.href = 'cadastrar_luta.php';
          </script>";

}

// ========================================
// FECHA A CONEXÃO
// ========================================

$stmt->close();

$conexao->close();

?>
