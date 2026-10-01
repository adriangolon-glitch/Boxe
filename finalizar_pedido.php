<?php

session_start();
include("conexao.php");


// Verifica se o usuário está logado
if (!isset($_SESSION["id"])) {
    echo "<script>
            alert('Você precisa fazer login para finalizar o pedido!');
            window.location.href = 'login.php';
          </script>";
    exit;
}


// Verifica se existe carrinho
if (!isset($_SESSION["carrinho"]) || empty($_SESSION["carrinho"])) {
    echo "<script>
            alert('Seu carrinho está vazio!');
            window.location.href = 'loja.php';
          </script>";
    exit;
}


$usuario_id = $_SESSION["id"];
$carrinho = $_SESSION["carrinho"];


// Inicia o total
$total = 0;

// Lista que vai guardar os produtos atualizados do banco
$produtos_pedido = [];


// Começa a transação
$conexao->begin_transaction();

try {

    /*
    =====================================================
    1. CONFERIR OS PRODUTOS NO BANCO
    =====================================================
    */

    foreach ($carrinho as $item) {

        $produto_id = intval($item["id"]);
        $quantidade = intval($item["quantidade"]);

        if ($quantidade <= 0) {
            throw new Exception("Quantidade inválida.");
        }


        // Busca o produto atualizado no banco
        $sql = "SELECT id, nome, preco, estoque
                FROM produtos
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 0) {
            throw new Exception("Um dos produtos não existe mais.");
        }

        $produto = $resultado->fetch_assoc();

        $stmt->close();


        // Confere o estoque
        if ($produto["estoque"] < $quantidade) {

            throw new Exception(
                "O produto '" . $produto["nome"] .
                "' não possui estoque suficiente."
            );
        }


        // Usa o preço atual do banco
        $preco = floatval($produto["preco"]);

        // Calcula subtotal
        $subtotal = $preco * $quantidade;

        // Soma ao total
        $total += $subtotal;


        // Guarda os dados atualizados
        $produtos_pedido[] = [
            "id" => $produto["id"],
            "nome" => $produto["nome"],
            "preco" => $preco,
            "quantidade" => $quantidade
        ];
    }


    /*
    =====================================================
    2. CRIAR O PEDIDO
    =====================================================
    */

    $sql = "INSERT INTO pedidos (usuario_id, total)
            VALUES (?, ?)";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("id", $usuario_id, $total);
    $stmt->execute();

    // Pega o ID do pedido criado
    $pedido_id = $conexao->insert_id;

    $stmt->close();


    /*
    =====================================================
    3. INSERIR OS PRODUTOS DO PEDIDO
    =====================================================
    */

    foreach ($produtos_pedido as $produto) {

        $produto_id = $produto["id"];
        $quantidade = $produto["quantidade"];
        $preco = $produto["preco"];


        $sql = "INSERT INTO itens_pedido
                (pedido_id, produto_id, quantidade, preco)
                VALUES (?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "iiid",
            $pedido_id,
            $produto_id,
            $quantidade,
            $preco
        );

        $stmt->execute();

        $stmt->close();


        /*
        =================================================
        4. DIMINUIR O ESTOQUE
        =================================================
        */

        $sql = "UPDATE produtos
                SET estoque = estoque - ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ii",
            $quantidade,
            $produto_id
        );

        $stmt->execute();

        $stmt->close();
    }


    /*
    =====================================================
    5. CONFIRMA TODAS AS ALTERAÇÕES
    =====================================================
    */

    $conexao->commit();


    // Limpa o carrinho
    $_SESSION["carrinho"] = [];


    /*
    =====================================================
    6. REDIRECIONA PARA OS PEDIDOS
    =====================================================
    */

    echo "<script>
            alert('Pedido realizado com sucesso! Número do pedido: #$pedido_id');
            window.location.href = 'meus_pedidos.php';
          </script>";

    exit;


} catch (Exception $erro) {

    /*
    =====================================================
    SE DER ERRO, DESFAZ TUDO
    =====================================================
    */

    $conexao->rollback();

    $mensagem = addslashes($erro->getMessage());

    echo "<script>
            alert('Não foi possível finalizar o pedido: $mensagem');
            window.location.href = 'carrinho.php';
          </script>";

    exit;
}

?>