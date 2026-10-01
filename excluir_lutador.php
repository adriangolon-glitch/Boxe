<?php

session_start();

include("conexao.php");

// ========================================
// SOMENTE ADMIN PODE EXCLUIR LUTADORES
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
            alert('Lutador não informado!');
            window.location.href = 'lutadores.php';
          </script>";

    exit;
}

$id = $_GET["id"];


// ========================================
// BUSCA A FOTO DO LUTADOR
// ========================================

$sqlFoto = "SELECT foto FROM lutadores WHERE id = ?";

$stmtFoto = $conexao->prepare($sqlFoto);

$stmtFoto->bind_param(
    "i",
    $id
);

$stmtFoto->execute();

$resultadoFoto = $stmtFoto->get_result();


// Verifica se o lutador existe

if ($resultadoFoto->num_rows == 0) {

    echo "<script>
            alert('Lutador não encontrado!');
            window.location.href = 'lutadores.php';
          </script>";

    exit;
}


$lutador = $resultadoFoto->fetch_assoc();

$nomeFoto = $lutador["foto"];


// ========================================
// EXCLUI O LUTADOR
// ========================================

$sql = "DELETE FROM lutadores WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);


// ========================================
// EXECUTA A EXCLUSÃO
// ========================================

if ($stmt->execute()) {

    // Se existir uma foto, exclui o arquivo também

    if (!empty($nomeFoto)) {

        $caminhoFoto = "fotos/" . $nomeFoto;

        if (file_exists($caminhoFoto)) {

            unlink($caminhoFoto);

        }

    }


    echo "<script>

            alert('Lutador excluído com sucesso!');

            window.location.href = 'lutadores.php';

          </script>";

} else {

    echo "<script>

            alert('Erro ao excluir o lutador!');

            window.location.href = 'lutadores.php';

          </script>";

}


// ========================================
// FECHA AS CONEXÕES
// ========================================

$stmt->close();

$stmtFoto->close();

$conexao->close();

?>
