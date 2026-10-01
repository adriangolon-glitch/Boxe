<?php

session_start();

include("conexao.php");

// ========================================
// SOMENTE ADMIN PODE EXCLUIR LUTAS
// ========================================

if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {

    header("Location: painel.php");
    exit;
}

// ========================================
// VERIFICA SE O ID FOI INFORMADO
// ========================================

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    echo "<script>
            alert('Luta não informada!');
            window.location.href = 'lutas.php';
          </script>";

    exit;
}

$id = $_GET["id"];

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

$resultado = $stmtVerificar->get_result();

if ($resultado->num_rows == 0) {

    echo "<script>
            alert('Luta não encontrada!');
            window.location.href = 'lutas.php';
          </script>";

    exit;
}

// ========================================
// EXCLUI A LUTA
// ========================================

$sql = "DELETE FROM lutas WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

// ========================================
// EXECUTA A EXCLUSÃO
// ========================================

if ($stmt->execute()) {

    echo "<script>
            alert('Luta excluída com sucesso!');
            window.location.href = 'lutas.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao excluir a luta!');
            window.location.href = 'lutas.php';
          </script>";

}

// ========================================
// FECHA AS CONEXÕES
// ========================================

$stmt->close();

$stmtVerificar->close();

$conexao->close();

?>
