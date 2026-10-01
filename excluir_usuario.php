<?php

session_start();

include("conexao.php");

// Somente administrador pode excluir usuários
if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: painel.php");
    exit;
}

// Verifica se foi informado um ID
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: usuarios.php");
    exit;
}

$id = $_GET["id"];

// Impede o administrador de excluir a própria conta
if ($id == $_SESSION["id"]) {

    echo "<script>
            alert('Você não pode excluir sua própria conta!');
            window.location.href = 'usuarios.php';
          </script>";

    exit;
}

// Verifica se o usuário existe
$sqlVerificar = "SELECT id FROM usuarios WHERE id = ?";

$stmtVerificar = $conexao->prepare($sqlVerificar);

$stmtVerificar->bind_param(
    "i",
    $id
);

$stmtVerificar->execute();

$resultado = $stmtVerificar->get_result();

if ($resultado->num_rows == 0) {

    echo "<script>
            alert('Usuário não encontrado!');
            window.location.href = 'usuarios.php';
          </script>";

    exit;
}

// Exclui o usuário
$sql = "DELETE FROM usuarios WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

if ($stmt->execute()) {

    echo "<script>
            alert('Usuário excluído com sucesso!');
            window.location.href = 'usuarios.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao excluir o usuário!');
            window.location.href = 'usuarios.php';
          </script>";

}

$stmt->close();
$stmtVerificar->close();
$conexao->close();

?>

