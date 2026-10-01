<?php

session_start();

include("conexao.php");

if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: loja.php");
    exit;
}

$id = $_GET["id"];

$sql = "SELECT * FROM produtos WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "<script>
            alert('Produto não encontrado!');
            window.location.href = 'loja.php';
          </script>";
    exit;
}

$produto = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($produto["nome"]); ?> | Boxe Fight
    </title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

        .produto-pagina {
            max-width: 1200px;
            margin: auto;
            padding: 70px 25px;
        }

        .voltar {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #aaa;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 30px;
            transition: 0.3s;
        }

        .voltar:hover {
            color: #e10600;
        }

        .produto-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            background: #1c1c1c;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 40px;
        }

        /* IMAGEM */

        .produto-imagem {
            width: 100%;
            height: 500px;
            background: #222;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .produto-imagem img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sem-foto {
            font-size: 100px;
            color: #555;
        }

        /* INFORMAÇÕES */

        .categoria {
            display: inline-block;
            color: #e10600;
            border: 1px solid #e10600;
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 18px;
        }

        .informacoes h1 {
            font-size: 45px;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .linha {
            width: 70px;
            height: 4px;
            background: #e10600;
            margin-bottom: 25px;
        }

        .descricao {
            color: #aaa;
            font-size: 17px;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .preco {
            font-size: 38px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 10px;
        }

        .estoque {
            color: #999;
            margin-bottom: 30px;
        }

        .estoque strong {
            color: #e10600;
        }

        /* COMPRA */

        .area-compra {
            background: #151515;
            border: 1px solid #333;
            padding: 20px;
            border-radius: 8px;
        }

        .area-compra h3 {
            margin-bottom: 15px;
        }

        .quantidade {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .quantidade label {
            color: #aaa;
            font-weight: bold;
        }

        .quantidade input {
            width: 80px;
            background: #222;
            border: 1px solid #555;
            color: #fff;
            padding: 11px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        .btn-comprar {
            width: 100%;
            display: block;
            background: #e10600;
            color: #fff;
            text-decoration: none;
            border: none;
            padding: 16px;
            border-radius: 6px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-comprar:hover {
            background: #b80500;
        }

        .btn-login {
            width: 100%;
            display: block;
            background: #e10600;
            color: #fff;
            text-decoration: none;
            padding: 16px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #b80500;
        }

        .esgotado {
            background: #333;
            color: #777;
            padding: 16px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }

        /* BENEFÍCIOS */

        .beneficios {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .beneficio {
            background: #151515;
            border: 1px solid #333;
            padding: 18px;
            border-radius: 7px;
            text-align: center;
        }

        .beneficio i {
            color: #e10600;
            font-size: 25px;
            margin-bottom: 10px;
        }

        .beneficio h4 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .beneficio p {
            color: #777;
            font-size: 12px;
        }

        /* FOOTER */

        footer {
            background: #000;
            border-top: 2px solid #e10600;
            text-align: center;
            padding: 30px 20px;
            color: #777;
            margin-top: 30px;
        }

        footer strong {
            color: #fff;
        }

        footer span {
            color: #e10600;
        }

        /* RESPONSIVO */

        @media (max-width: 850px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .produto-container {
                grid-template-columns: 1fr;
                padding: 25px;
            }

            .produto-imagem {
                height: 400px;
            }

            .informacoes h1 {
                font-size: 35px;
            }

        }

        @media (max-width: 600px) {

            .beneficios {
                grid-template-columns: 1fr;
            }

            .produto-imagem {
                height: 300px;
            }

            .produto-container {
                padding: 18px;
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

        <a href="index.php">Início</a>

        <a href="lutadores.php">Lutadores</a>

        <a href="lutas.php">Lutas</a>

        <a href="loja.php">Loja</a>

        <?php if (isset($_SESSION["id"])) { ?>

            <a href="painel.php">Meu Painel</a>

            <a href="carrinho.php" class="carrinho-menu">
                <i class="fas fa-shopping-cart"></i>
                Carrinho
            </a>

            <a href="logout.php">Sair</a>

        <?php } else { ?>

            <a href="login.php">Entrar</a>

        <?php } ?>

    </div>

</nav>


<!-- PRODUTO -->

<section class="produto-pagina">

    <a href="loja.php" class="voltar">
        <i class="fas fa-arrow-left"></i>
        Voltar para a loja
    </a>


    <div class="produto-container">


        <!-- IMAGEM -->

        <div class="produto-imagem">

            <?php if (!empty($produto["foto"])) { ?>

                <img
                    src="camiseta.jpg<?php echo htmlspecialchars($produto["foto"]); ?>"
                    alt="<?php echo htmlspecialchars($produto["nome"]); ?>"
                >

            <?php } else { ?>

                <div class="sem-foto">
                    <i class="fas fa-box-open"></i>
                </div>

            <?php } ?>

        </div>


        <!-- INFORMAÇÕES -->

        <div class="informacoes">

            <div class="categoria">

                <?php
                echo htmlspecialchars($produto["categoria"]);
                ?>

            </div>


            <h1>

                <?php
                echo htmlspecialchars($produto["nome"]);
                ?>

            </h1>


            <div class="linha"></div>


            <p class="descricao">

                <?php
                echo nl2br(
                    htmlspecialchars($produto["descricao"])
                );
                ?>

            </p>


            <div class="preco">

                R$
                <?php
                echo number_format(
                    $produto["preco"],
                    2,
                    ",",
                    "."
                );
                ?>

            </div>


            <div class="estoque">

                <?php if ($produto["estoque"] > 0) { ?>

                    <i class="fas fa-check-circle"></i>

                    <strong>
                        <?php echo $produto["estoque"]; ?>
                    </strong>

                    unidades disponíveis

                <?php } else { ?>

                    <strong>
                        Produto esgotado
                    </strong>

                <?php } ?>

            </div>


            <!-- ÁREA DE COMPRA -->

            <div class="area-compra">

                <?php if ($produto["estoque"] > 0) { ?>

                    <?php if (isset($_SESSION["id"])) { ?>

                        <h3>
                            Comprar produto
                        </h3>

                        <div class="quantidade">

                            <label for="quantidade">
                                Quantidade:
                            </label>

                            <input
                                type="number"
                                id="quantidade"
                                min="1"
                                max="<?php echo $produto["estoque"]; ?>"
                                value="1"
                            >

                        </div>

                        <a
                            href="#"
                            class="btn-comprar"
                            onclick="adicionarCarrinho(); return false;"
                        >

                            <i class="fas fa-cart-plus"></i>

                            ADICIONAR AO CARRINHO

                        </a>

                        <script>

                            function adicionarCarrinho() {

                                var quantidade =
                                    document.getElementById("quantidade").value;

                                window.location.href =
                                    "adicionar_carrinho.php?id=<?php echo $produto["id"]; ?>&quantidade="
                                    + quantidade;

                            }

                        </script>

                    <?php } else { ?>

                        <h3>
                            Faça login para comprar
                        </h3>

                        <p style="
                            color:#888;
                            margin-bottom:15px;
                            line-height:1.5;
                        ">
                            Entre na sua conta para adicionar este produto
                            ao carrinho e realizar seu pedido.
                        </p>

                        <a
                            href="login.php"
                            class="btn-login"
                        >
                            <i class="fas fa-sign-in-alt"></i>
                            ENTRAR NA CONTA
                        </a>

                    <?php } ?>

                <?php } else { ?>

                    <div class="esgotado">
                        PRODUTO ESGOTADO
                    </div>

                <?php } ?>

            </div>


            <!-- BENEFÍCIOS -->

            <div class="beneficios">

                <div class="beneficio">

                    <i class="fas fa-shield-alt"></i>

                    <h4>
                        Compra segura
                    </h4>

                    <p>
                        Seus pedidos ficam registrados.
                    </p>

                </div>


                <div class="beneficio">

                    <i class="fas fa-box"></i>

                    <h4>
                        Produto disponível
                    </h4>

                    <p>
                        Estoque atualizado.
                    </p>

                </div>


                <div class="beneficio">

                    <i class="fas fa-fist-raised"></i>

                    <h4>
                        Boxe Fight
                    </h4>

                    <p>
                        Equipamentos para treino.
                    </p>

                </div>

            </div>

        </div>

    </div>

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
        © 2026 Boxe Fight. Todos os direitos reservados.
    </p>

</footer>

</body>

</html>

<?php

$stmt->close();

$conexao->close();

?>