<?php

session_start();

include("conexao.php");


// Verificar se o usuário está logado

if (!isset($_SESSION["id"])) {

    echo "<script>
            alert('Você precisa fazer login para comprar!');
            window.location.href = 'login.php';
          </script>";

    exit;
}


// Verificar se o produto foi informado

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    echo "<script>
            alert('Produto não informado!');
            window.location.href = 'loja.php';
          </script>";

    exit;
}


$id = intval($_GET["id"]);


// Verificar quantidade

$quantidade = 1;

if (isset($_GET["quantidade"]) && !empty($_GET["quantidade"])) {

    $quantidade = intval($_GET["quantidade"]);

}

if ($quantidade < 1) {

    $quantidade = 1;

}


// Buscar produto

$sql = "SELECT * FROM produtos WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();


// Produto não encontrado

if ($resultado->num_rows == 0) {

    echo "<script>
            alert('Produto não encontrado!');
            window.location.href = 'loja.php';
          </script>";

    exit;
}


$produto = $resultado->fetch_assoc();


// Verificar estoque

if ($produto["estoque"] <= 0) {

    echo "<script>
            alert('Esse produto está esgotado!');
            window.location.href = 'loja.php';
          </script>";

    exit;
}


// Verificar se a quantidade ultrapassa o estoque

if ($quantidade > $produto["estoque"]) {

    echo "<script>
            alert('Quantidade maior que o estoque disponível!');
            window.location.href = 'produto.php?id=$id';
          </script>";

    exit;
}


// Criar carrinho caso ainda não exista

if (!isset($_SESSION["carrinho"])) {

    $_SESSION["carrinho"] = [];

}


// Verificar se o produto já está no carrinho

if (isset($_SESSION["carrinho"][$id])) {

    $novaQuantidade =
        $_SESSION["carrinho"][$id]["quantidade"] + $quantidade;


    // Não permitir passar do estoque

    if ($novaQuantidade > $produto["estoque"]) {

        $novaQuantidade = $produto["estoque"];

    }


    $_SESSION["carrinho"][$id]["quantidade"] =
        $novaQuantidade;

} else {


    // Adicionar produto novo

    $_SESSION["carrinho"][$id] = [

        "id" => $produto["id"],

        "nome" => $produto["nome"],

        "preco" => $produto["preco"],

        "foto" => $produto["foto"],

        "quantidade" => $quantidade

    ];

}


// Fechar consulta

$stmt->close();

$conexao->close();


// Ir para o carrinho

header("Location: carrinho.php");

exit;

?>
