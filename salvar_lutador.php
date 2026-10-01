<?php

session_start();

include("conexao.php");

// Somente ADMIN pode cadastrar lutadores
if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: painel.php");
    exit;
}


// Recebe os dados do formulário

$nome = $_POST["nome"];
$nacionalidade = $_POST["nacionalidade"];
$idade = $_POST["idade"];
$categoria = $_POST["categoria"];
$vitorias = $_POST["vitorias"];
$derrotas = $_POST["derrotas"];
$empates = $_POST["empates"];


// FOTO

$nomeFoto = "";

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
                window.location.href = 'cadastrar_lutador.php';
              </script>";

        exit;
    }


    // Cria um nome único para a imagem
    $nomeFoto = uniqid() . "." . $extensao;

    $caminhoFoto = $pasta . $nomeFoto;


    // Move a imagem para a pasta fotos
    if (!move_uploaded_file(
        $_FILES["foto"]["tmp_name"],
        $caminhoFoto
    )) {

        echo "<script>
                alert('Erro ao enviar a foto!');
                window.location.href = 'cadastrar_lutador.php';
              </script>";

        exit;
    }

}


// CADASTRA NO BANCO

$sql = "INSERT INTO lutadores
        (nome, nacionalidade, idade, categoria, vitorias, derrotas, empates, foto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "ssisiiis",
    $nome,
    $nacionalidade,
    $idade,
    $categoria,
    $vitorias,
    $derrotas,
    $empates,
    $nomeFoto
);


if ($stmt->execute()) {

    echo "<script>
            alert('Lutador cadastrado com sucesso!');
            window.location.href = 'lutadores.php';
          </script>";

} else {

    echo "<script>
            alert('Erro ao cadastrar o lutador!');
            window.location.href = 'cadastrar_lutador.php';
          </script>";

}


$stmt->close();
$conexao->close();

?>

