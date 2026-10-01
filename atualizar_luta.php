<?php

session_start();

include("conexao.php");

// ========================================
// SOMENTE ADMIN PODE ATUALIZAR LUTAS
// ========================================

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {

    header("Location: painel.php");
    exit;
}

// ========================================
// RECEBE OS DADOS
// ========================================

$id = $_POST["id"];
$lutador1 = $_POST["lutador1"];
$lutador2 = $_POST["lutador2"];
$data_luta = $_POST["data_luta"];
$resultado = $_POST["resultado"];
$metodo = $_POST["metodo"];

// ========================================
// VERIFICA SE O ID FOI INFORMADO
// ========================================

if (empty($id)) {

    echo "<script>
            alert('Luta não informada!');
            window.location.href = 'lutas.php';
          </script>";

    exit;
}

// ========================================
// VERIFICA SE OS LUTADORES SÃO DIFERENTES
// ========================================

if ($lutador1 == $lutador2) {

    echo "<script>
            alert('Os dois lutadores não podem ser iguais!');
            window.location.href = 'editar_luta.php?id=$id';
          </script>";

    exit;
}

// ========================================
// VERIFICA SE A LUTA EXISTE
// ========================================

$sqlVerificar = "SELECT id FROM lutas WHERE id = ?";

$stmtVerificar = $conexao->prepare($sqlVerificar);

$stmtVerificar->bind_param(
    "i",
    $id
);

$stmtVerificar->execute();

$resultadoVerificacao = $stmtVerificar->get_result();

if ($resultadoVerificacao->num_rows == 0) {

    echo "<script>
            alert('Luta não encontrada!');
            window.location.href = 'lutas.php';
          </script>";

    exit;
}

// ========================================
// ATUALIZA A LUTA
// ========================================

$sql = "UPDATE lutas SET
        lutador1 = ?,
        lutador2 = ?,
        data_luta = ?,
        resultado = ?,
        metodo = ?
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssssi",
    $lutador1,
    $lutador2,
    $data_luta,
    $resultado,
    $metodo,
    $id
);

// ========================================
// EXECUTA A ATUALIZAÇÃO
// ========================================

if ($stmt->execute()) {

    echo "<script>
            alert('Luta atualizada com sucesso!');
            window.location.href = 'lutas.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao atualizar a luta!');
            window.location.href = 'editar_luta.php?id=$id';
          </script>";

}

// ========================================
// FECHA AS CONEXÕES
// ========================================

$stmt->close();
$stmtVerificar->close();
$conexao->close();

?>
