<?php

session_start();


// Verificar se o usuário está logado

if (!isset($_SESSION["id"])) {

    echo "<script>
            alert('Você precisa fazer login!');
            window.location.href = 'login.php';
          </script>";

    exit;
}


// Verificar se o ID foi informado

if (!isset($_GET["id"]) || empty($_GET["id"])) {

    echo "<script>
            alert('Produto não informado!');
            window.location.href = 'carrinho.php';
          </script>";

    exit;
}


$id = intval($_GET["id"]);


// Verificar se o carrinho existe

if (!isset($_SESSION["carrinho"])) {

    $_SESSION["carrinho"] = [];

}


// Remover produto

if (isset($_SESSION["carrinho"][$id])) {

    unset($_SESSION["carrinho"][$id]);

}


// Voltar para o carrinho

header("Location: carrinho.php");

exit;

?>
