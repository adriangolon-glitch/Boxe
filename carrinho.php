<?php

session_start();

include("conexao.php");


// Verificar se o usuário está logado

if (!isset($_SESSION["id"])) {

    echo "<script>
            alert('Você precisa fazer login para acessar o carrinho!');
            window.location.href = 'login.php';
          </script>";

    exit;
}


// Criar carrinho caso não exista

if (!isset($_SESSION["carrinho"])) {

    $_SESSION["carrinho"] = [];

}


$carrinho = $_SESSION["carrinho"];

$total = 0;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Meu Carrinho | Boxe Fight</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    >

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #111;
            color: #fff;
        }


        /* NAVBAR */

        nav {
            width: 100%;
            background: #000;
            border-bottom: 2px solid #e10600;
            padding: 18px 6%;

            display: flex;
            justify-content: space-between;
            align-items: center;

            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            color: #fff;
            text-decoration: none;
            font-size: 25px;
            font-weight: bold;
        }

        .logo span {
            color: #e10600;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .menu a {
            color: #ddd;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .menu a:hover {
            color: #e10600;
        }

        .carrinho-menu {
            background: #e10600;
            color: #fff !important;
            padding: 10px 16px;
            border-radius: 6px;
        }

        .carrinho-menu:hover {
            background: #b80500 !important;
        }


        /* CONTEÚDO */

        .pagina {
            max-width: 1200px;
            margin: auto;
            padding: 60px 25px;
        }

        .topo {
            text-align: center;
            margin-bottom: 45px;
        }

        .topo i {
            font-size: 50px;
            color: #e10600;
            margin-bottom: 15px;
        }

        .topo h1 {
            font-size: 42px;
            text-transform: uppercase;
        }

        .topo h1 span {
            color: #e10600;
        }

        .topo p {
            color: #888;
            margin-top: 10px;
        }


        /* CARRINHO */

        .carrinho-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            align-items: start;
        }


        /* PRODUTOS */

        .produtos-carrinho {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .item {
            background: #1c1c1c;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 20px;

            display: grid;
            grid-template-columns: 110px 1fr auto;
            gap: 20px;
            align-items: center;

            transition: 0.3s;
        }

        .item:hover {
            border-color: #555;
        }


        /* FOTO */

        .item-foto {
            width: 110px;
            height: 110px;
            background: #222;
            border-radius: 7px;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .item-foto img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sem-foto {
            font-size: 35px;
            color: #555;
        }


        /* INFORMAÇÕES */

        .item-info h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .item-info p {
            color: #999;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .preco-unitario {
            color: #e10600;
            font-weight: bold;
        }


        /* QUANTIDADE */

        .quantidade {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }

        .quantidade span {
            color: #aaa;
            font-size: 14px;
        }

        .quantidade strong {
            background: #333;
            padding: 7px 14px;
            border-radius: 5px;
        }


        /* LADO DIREITO DO ITEM */

        .item-total {
            text-align: right;
        }

        .item-total small {
            display: block;
            color: #777;
            margin-bottom: 5px;
        }

        .item-total strong {
            display: block;
            font-size: 21px;
            margin-bottom: 15px;
        }

        .remover {
            display: inline-block;
            color: #e10600;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }

        .remover:hover {
            color: #ff3333;
        }


        /* RESUMO */

        .resumo {
            background: #1c1c1c;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 25px;

            position: sticky;
            top: 100px;
        }

        .resumo h2 {
            font-size: 24px;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .resumo h2 span {
            color: #e10600;
        }

        .linha-resumo {
            display: flex;
            justify-content: space-between;
            padding: 13px 0;
            border-bottom: 1px solid #333;
            color: #999;
        }

        .linha-resumo strong {
            color: #fff;
        }

        .linha-total {
            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 20px 0;

            font-size: 22px;
        }

        .linha-total span:last-child {
            color: #e10600;
            font-weight: bold;
        }


        /* BOTÕES */

        .btn-finalizar {
            display: block;
            width: 100%;

            background: #e10600;
            color: #fff;

            text-decoration: none;
            text-align: center;

            padding: 16px;
            border-radius: 6px;

            font-weight: bold;
            transition: 0.3s;
        }

        .btn-finalizar:hover {
            background: #b80500;
        }

        .btn-continuar {
            display: block;
            width: 100%;

            border: 1px solid #555;
            color: #fff;

            text-decoration: none;
            text-align: center;

            padding: 14px;
            border-radius: 6px;

            font-weight: bold;
            margin-top: 10px;

            transition: 0.3s;
        }

        .btn-continuar:hover {
            background: #292929;
        }


        /* CARRINHO VAZIO */

        .vazio {
            max-width: 700px;
            margin: 40px auto 80px;

            background: #1c1c1c;
            border: 1px solid #333;
            border-radius: 12px;

            padding: 70px 30px;

            text-align: center;
        }

        .vazio i {
            font-size: 70px;
            color: #e10600;
            margin-bottom: 25px;
        }

        .vazio h2 {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .vazio p {
            color: #888;
            margin-bottom: 25px;
        }

        .btn-loja {
            display: inline-block;

            background: #e10600;
            color: #fff;

            text-decoration: none;

            padding: 14px 25px;
            border-radius: 6px;

            font-weight: bold;

            transition: 0.3s;
        }

        .btn-loja:hover {
            background: #b80500;
        }


        /* AVISO */

        .aviso {
            margin-top: 20px;
            padding: 15px;

            background: #151515;
            border-left: 3px solid #e10600;

            color: #777;
            font-size: 13px;
            line-height: 1.5;
        }


        /* FOOTER */

        footer {
            background: #000;
            border-top: 2px solid #e10600;

            text-align: center;

            padding: 30px 20px;

            color: #777;
        }

        footer strong {
            color: #fff;
        }

        footer span {
            color: #e10600;
        }


        /* RESPONSIVO */

        @media (max-width: 900px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .carrinho-layout {
                grid-template-columns: 1fr;
            }

            .resumo {
                position: static;
            }

        }


        @media (max-width: 600px) {

            .topo h1 {
                font-size: 32px;
            }

            .item {
                grid-template-columns: 80px 1fr;
            }

            .item-foto {
                width: 80px;
                height: 80px;
            }

            .item-total {
                grid-column: 1 / -1;
                text-align: left;

                border-top: 1px solid #333;
                padding-top: 15px;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav>

    <a href="index.php" class="logo">
        BOXE<span>FIGHT</span>
    </a>

    <div class="menu">

        <a href="index.php">
            Início
        </a>

        <a href="lutadores.php">
            Lutadores
        </a>

        <a href="lutas.php">
            Lutas
        </a>

        <a href="loja.php">
            Loja
        </a>

        <a href="painel.php">
            Meu Painel
        </a>

        <a href="carrinho.php" class="carrinho-menu">
            <i class="fas fa-shopping-cart"></i>
            Carrinho
        </a>

        <a href="logout.php">
            Sair
        </a>

    </div>

</nav>


<!-- PÁGINA -->

<section class="pagina">


    <div class="topo">

        <i class="fas fa-shopping-cart"></i>

        <h1>
            Meu <span>Carrinho</span>
        </h1>

        <p>
            Confira seus produtos antes de finalizar o pedido.
        </p>

    </div>


    <?php if (count($carrinho) > 0) { ?>


        <div class="carrinho-layout">


            <!-- PRODUTOS -->

            <div class="produtos-carrinho">


                <?php foreach ($carrinho as $item) { ?>


                    <?php

                    $subtotal =
                        $item["preco"] * $item["quantidade"];

                    $total += $subtotal;

                    ?>


                    <div class="item">


                        <!-- FOTO -->

                        <div class="item-foto">

                            <?php if (!empty($item["foto"])) { ?>

                                <img
                                    src="imagens/<?php echo htmlspecialchars($item["foto"]); ?>"
                                    alt="<?php echo htmlspecialchars($item["nome"]); ?>"
                                >

                            <?php } else { ?>

                                <div class="sem-foto">
                                    <i class="fas fa-box-open"></i>
                                </div>

                            <?php } ?>

                        </div>


                        <!-- INFORMAÇÕES -->

                        <div class="item-info">

                            <h2>
                                <?php
                                echo htmlspecialchars($item["nome"]);
                                ?>
                            </h2>

                            <p>
                                Preço por unidade:
                                <span class="preco-unitario">

                                    R$
                                    <?php
                                    echo number_format(
                                        $item["preco"],
                                        2,
                                        ",",
                                        "."
                                    );
                                    ?>

                                </span>
                            </p>


                            <div class="quantidade">

                                <span>
                                    Quantidade:
                                </span>

                                <strong>
                                    <?php
                                    echo $item["quantidade"];
                                    ?>
                                </strong>

                            </div>

                        </div>


                        <!-- TOTAL DO ITEM -->

                        <div class="item-total">

                            <small>
                                Subtotal
                            </small>

                            <strong>

                                R$
                                <?php
                                echo number_format(
                                    $subtotal,
                                    2,
                                    ",",
                                    "."
                                );
                                ?>

                            </strong>


                            <a
                                href="remover_carrinho.php?id=<?php echo $item["id"]; ?>"
                                class="remover"
                                onclick="return confirm('Deseja remover este produto do carrinho?');"
                            >

                                <i class="fas fa-trash"></i>

                                REMOVER

                            </a>

                        </div>


                    </div>


                <?php } ?>


            </div>


            <!-- RESUMO -->

            <div class="resumo">

                <h2>
                    Resumo do <span>Pedido</span>
                </h2>


                <div class="linha-resumo">

                    <span>
                        Produtos
                    </span>

                    <strong>

                        <?php

                        $quantidadeTotal = 0;

                        foreach ($carrinho as $item) {

                            $quantidadeTotal +=
                                $item["quantidade"];

                        }

                        echo $quantidadeTotal;

                        ?>

                    </strong>

                </div>


                <div class="linha-resumo">

                    <span>
                        Entrega
                    </span>

                    <strong>
                        A calcular
                    </strong>

                </div>


                <div class="linha-total">

                    <span>
                        TOTAL
                    </span>

                    <span>

                        R$
                        <?php
                        echo number_format(
                            $total,
                            2,
                            ",",
                            "."
                        );
                        ?>

                    </span>

                </div>


                <a
                    href="finalizar_pedido.php"
                    class="btn-finalizar"
                >

                    <i class="fas fa-check"></i>

                    FINALIZAR PEDIDO

                </a>


                <a
                    href="loja.php"
                    class="btn-continuar"
                >

                    <i class="fas fa-arrow-left"></i>

                    CONTINUAR COMPRANDO

                </a>


                <div class="aviso">

                    <i class="fas fa-info-circle"></i>

                    O pedido só será registrado quando você
                    clicar em <strong>Finalizar Pedido</strong>.

                </div>

            </div>


        </div>


    <?php } else { ?>


        <!-- CARRINHO VAZIO -->

        <div class="vazio">

            <i class="fas fa-shopping-cart"></i>

            <h2>
                Seu carrinho está vazio
            </h2>

            <p>
                Você ainda não adicionou nenhum produto.
                Explore nossa loja e encontre seus equipamentos.
            </p>

            <a
                href="loja.php"
                class="btn-loja"
            >

                <i class="fas fa-store"></i>

                IR PARA A LOJA

            </a>

        </div>


    <?php } ?>


</section>


<!-- FOOTER -->

<footer>

    <p>

        <strong>
            BOXE<span>FIGHT</span>
        </strong>

        — Loja oficial do projeto Boxe Fight.

    </p>

    <p>

        © 2026 Boxe Fight.
        Todos os direitos reservados.

    </p>

</footer>


</body>

</html>


<?php

$conexao->close();

?>
