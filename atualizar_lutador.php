<?php

session_start();

include("conexao.php");

// SOMENTE ADMIN PODE ATUALIZAR LUTADORES
if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: painel.php");
    exit;
}


// RECEBE OS DADOS

$id = $_POST["id"];
$nome = $_POST["nome"];
$nacionalidade = $_POST["nacionalidade"];
$idade = $_POST["idade"];
$categoria = $_POST["categoria"];
$vitorias = $_POST["vitorias"];
$derrotas = $_POST["derrotas"];
$empates = $_POST["empates"];


// BUSCA A FOTO ATUAL

$sqlFoto = "SELECT foto FROM lutadores WHERE id = ?";

$stmtFoto = $conexao->prepare($sqlFoto);

$stmtFoto->bind_param(
    "i",
    $id
);

$stmtFoto->execute();

$resultadoFoto = $stmtFoto->get_result();

if ($resultadoFoto->num_rows == 0) {

    echo "<script>
            alert('Lutador não encontrado!');
            window.location.href = 'lutadores.php';
          </script>";

    exit;
}

$dadosFoto = $resultadoFoto->fetch_assoc();

$nomeFoto = $dadosFoto["foto"];


// VERIFICA SE FOI ENVIADA UMA NOVA FOTO

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {

    $pasta = "fotos/";

    // Cria a pasta caso ela não exista
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $nomeOriginal = $_FILES["foto"]["name"];

    $extensao = strtolower(
        pathinfo($nomeOriginal, PATHINFO_EXTENSION)
    );

    // Extensões permitidas
    $extensoesPermitidas = [
        "jpg",
        "jpeg",
        "png",
        "webp"
    ];

    if (!in_array($extensao, $extensoesPermitidas)) {

        echo "<script>
                alert('Formato de imagem não permitido!');
                window.location.href = 'editar_lutador.php?id=$id';
              </script>";

        exit;
    }


    // Cria novo nome para a foto
    $novaFoto = uniqid() . "." . $extensao;

    $caminhoFoto = $pasta . $novaFoto;


    // Move a nova foto
    if (!move_uploaded_file(
        $_FILES["foto"]["tmp_name"],
        $caminhoFoto
    )) {

        echo "<script>
                alert('Erro ao enviar a nova foto!');
                window.location.href = 'editar_lutador.php?id=$id';
              </script>";

        exit;
    }


    // Apaga a foto antiga
    if (!empty($nomeFoto)) {

        $fotoAntiga = $pasta . $nomeFoto;

        if (file_exists($fotoAntiga)) {
            unlink($fotoAntiga);
        }

    }


    // Atualiza o nome da foto
    $nomeFoto = $novaFoto;

}


// ATUALIZA O LUTADOR NO BANCO

$sql = "UPDATE lutadores SET
        nome = ?,
        nacionalidade = ?,
        idade = ?,
        categoria = ?,
        vitorias = ?,
        derrotas = ?,
        empates = ?,
        foto = ?
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssisiiisi",
    $nome,
    $nacionalidade,
    $idade,
    $categoria,
    $vitorias,
    $derrotas,
    $empates,
    $nomeFoto,
    $id
);


// EXECUTA A ATUALIZAÇÃO

if ($stmt->execute()) {

    echo "<script>
            alert('Lutador atualizado com sucesso!');
            window.location.href = 'lutadores.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao atualizar o lutador!');
            window.location.href = 'editar_lutador.php?id=$id';
          </script>";

}


// FECHA CONEXÕES

$stmt->close();
$stmtFoto->close();
$conexao->close();

?>
