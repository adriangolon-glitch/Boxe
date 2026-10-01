<?php

session_start();
include("conexao.php");


// Verifica se o usuário está logado
if (!isset($_SESSION["id"])) {
    echo "<script>
            alert('Você precisa fazer login para ver seus pedidos!');
            window.location.href = 'login.php';
          </script>";
    exit;
}

$usuario_id = $_SESSION["id"];


// Busca os pedidos do usuário
$sql = "SELECT *
        FROM pedidos
        WHERE usuario_id = ?
        ORDER BY id DESC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meus Pedidos - Boxe Fight</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #111;
            color: #fff;
        }

        /* NAVBAR */

        nav {
            background: #000;
            border-bottom: 2px solid #e10600;
            padding: 18px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo {
            color: #e10600;
            font-size: 26px;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }

        .logo span {
            color: #fff;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 25px;
            list-style: none;
            flex-wrap: wrap;
        }

        .menu a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .menu a:hover {
            color: #e10600;
        }

        /* CABEÇALHO */

        .topo {
            background: linear-gradient(
                135deg,
                #000,
                #1c1c1c
            );

            padding: 65px 20px;
            text-align: center;
            border-bottom: 1px solid #333;
        }

        .topo i {
            font-size: 45px;
            color: #e10600;
            margin-bottom: 15px;
        }

        .topo h1 {
            font-size: 38px;
            margin-bottom: 10px;
        }

        .topo p {
            color: #bbb;
            font-size: 17px;
        }

        /* CONTAINER */

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 45px auto;
        }

        /* PEDIDO */

        .pedido {
            background: #1c1c1c;
            border: 1px solid #333;
            border-left: 5px solid #e10600;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.35);
        }

        .pedido-topo {
            padding: 22px 25px;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .pedido-numero {
            font-size: 20px;
            font-weight: bold;
        }

        .pedido-numero span {
            color: #e10600;
        }

        .data {
            color: #aaa;
            font-size: 14px;
        }

        /* STATUS */

        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .Pendente {
            background: #444;
            color: #fff;
        }

        .Processando {
            background: #555;
            color: #fff;
        }

        .Enviado {
            background: #e10600;
            color: #fff;
        }

        .Concluído {
            background: #fff;
            color: #000;
        }

        .Cancelado {
            background: #333;
            color: #aaa;
        }

        /* CONTEÚDO */

        .pedido-conteudo {
            padding: 25px;
        }

        .pedido-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 18px;
            border-bottom: 1px solid #333;
            gap: 15px;
            flex-wrap: wrap;
        }

        .pedido-info p {
            color: #bbb;
        }

        .pedido-info strong {
            color: #fff;
        }

        .total {
            font-size: 22px;
            color: #e10600;
            font-weight: bold;
        }

        /* ITENS */

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 16px 0;
            border-bottom: 1px solid #292929;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-info h3 {
            font-size: 17px;
            margin-bottom: 6px;
        }

        .item-info p {
            color: #999;
            font-size: 14px;
        }

        .item-preco {
            text-align: right;
            white-space: nowrap;
        }

        .item-preco strong {
            color: #fff;
        }

        .item-preco small {
            display: block;
            color: #999;
            margin-top: 4px;
        }

        /* SEM PEDIDOS */

        .sem-pedidos {
            background: #1c1c1c;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 70px 20px;
            text-align: center;
        }

        .sem-pedidos i {
            font-size: 55px;
            color: #555;
            margin-bottom: 20px;
        }

        .sem-pedidos h2 {
            margin-bottom: 10px;
        }

        .sem-pedidos p {
            color: #999;
            margin-bottom: 25px;
        }

        .botao {
            display: inline-block;
            background: #e10600;
            color: #fff;
            text-decoration: none;
            padding: 13px 25px;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.3s;
        }

        .botao:hover {
            background: #b80500;
            transform: translateY(-2px);
        }

        /* FOOTER */

        footer {
            background: #000;
            border-top: 2px solid #e10600;
            text-align: center;
            padding: 30px 20px;
            margin-top: 60px;
            color: #888;
        }

        footer strong {
            color: #e10600;
        }

        /* RESPONSIVO */

        @media (max-width: 700px) {

            nav {
                flex-direction: column;
            }

            .menu {
                justify-content: center;
            }

            .topo h1 {
                font-size: 30px;
            }

            .item {
                flex-direction: column;
                align-items: flex-start;
            }

            .item-preco {
                text-align: left;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav>

    <a href="index.php" class="logo">
        BOXE <span>FIGHT</span>
    </a>

    <ul class="menu">

        <li>
            <a href="index.php">Início</a>
        </li>

        <li>
            <a href="lutadores.php">Lutadores</a>
        </li>

        <li>
            <a href="lutas.php">Lutas</a>
        </li>

        <li>
            <a href="loja.php">Loja</a>
        </li>

        <li>
            <a href="carrinho.php">
                <i class="fas fa-shopping-cart"></i>
                Carrinho
            </a>
        </li>

        <li>
            <a href="painel.php">
                <i class="fas fa-user"></i>
                Meu Painel
            </a>
        </li>

        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Sair
            </a>
        </li>

    </ul>

</nav>


<!-- TOPO -->

<section class="topo">

    <i class="fas fa-box"></i>

    <h1>Meus Pedidos</h1>

    <p>
        Acompanhe todos os pedidos realizados na Boxe Fight.
    </p>

</section>


<!-- PEDIDOS -->

<div class="container">

<?php if ($resultado->num_rows > 0) { ?>


    <?php while ($pedido = $resultado->fetch_assoc()) { ?>


        <div class="pedido">


            <!-- CABEÇALHO DO PEDIDO -->

            <div class="pedido-topo">

                <div>

                    <div class="pedido-numero">

                        Pedido
                        <span>
                            #<?php echo $pedido["id"]; ?>
                        </span>

                    </div>

                    <div class="data">

                        <?php
                        echo date(
                            "d/m/Y H:i",
                            strtotime($pedido["data_pedido"])
                        );
                        ?>

                    </div>

                </div>


                <div>

                    <span class="status <?php echo htmlspecialchars($pedido["status"]); ?>">

                        <?php echo htmlspecialchars($pedido["status"]); ?>

                    </span>

                </div>

            </div>


            <!-- CONTEÚDO -->

            <div class="pedido-conteudo">


                <div class="pedido-info">

                    <p>

                        <i class="fas fa-shopping-bag"></i>

                        Produtos do pedido

                    </p>

                    <div class="total">

                        R$

                        <?php
                        echo number_format(
                            $pedido["total"],
                            2,
                            ",",
                            "."
                        );
                        ?>

                    </div>

                </div>


                <?php

                // Busca os itens desse pedido
                $sql_itens = "SELECT
                                itens_pedido.quantidade,
                                itens_pedido.preco,
                                produtos.nome
                              FROM itens_pedido
                              INNER JOIN produtos
                              ON itens_pedido.produto_id = produtos.id
                              WHERE itens_pedido.pedido_id = ?
                              ORDER BY itens_pedido.id ASC";

                $stmt_itens = $conexao->prepare($sql_itens);

                $stmt_itens->bind_param(
                    "i",
                    $pedido["id"]
                );

                $stmt_itens->execute();

                $resultado_itens = $stmt_itens->get_result();

                ?>


                <?php while ($item = $resultado_itens->fetch_assoc()) { ?>


                    <div class="item">


                        <div class="item-info">

                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $item["nome"]
                                );
                                ?>

                            </h3>

                            <p>

                                Quantidade:
                                <?php
                                echo $item["quantidade"];
                                ?>

                            </p>

                        </div>


                        <div class="item-preco">

                            <strong>

                                R$

                                <?php
                                echo number_format(
                                    $item["preco"] * $item["quantidade"],
                                    2,
                                    ",",
                                    "."
                                );
                                ?>

                            </strong>

                            <small>

                                R$
                                <?php
                                echo number_format(
                                    $item["preco"],
                                    2,
                                    ",",
                                    "."
                                );
                                ?>

                                cada

                            </small>

                        </div>


                    </div>


                <?php } ?>


                <?php $stmt_itens->close(); ?>


            </div>

        </div>


    <?php } ?>


<?php } else { ?>


    <!-- NENHUM PEDIDO -->

    <div class="sem-pedidos">

        <i class="fas fa-shopping-bag"></i>

        <h2>Você ainda não fez nenhum pedido</h2>

        <p>
            Escolha seus produtos favoritos na nossa loja
            e faça seu primeiro pedido.
        </p>

        <a href="loja.php" class="botao">

            <i class="fas fa-store"></i>

            IR PARA A LOJA

        </a>

    </div>


<?php } ?>


</div>


<!-- FOOTER -->

<footer>

    <p>
        &copy; <?php echo date("Y"); ?>
        <strong>Boxe Fight</strong>.
        Todos os direitos reservados.
    </p>

</footer>


</body>

</html>

<?php

$stmt->close();
$conexao->close();

?>
