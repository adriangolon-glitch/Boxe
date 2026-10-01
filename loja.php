<?php

session_start();

include("conexao.php");

$sql = "SELECT * FROM produtos ORDER BY id DESC";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Loja | Boxe Fight</title>

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
            font-size: 25px;
            font-weight: bold;
            text-decoration: none;
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

        /* CABEÇALHO */

        .topo-loja {
            min-height: 330px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 50px 20px;

            background:
                linear-gradient(
                    rgba(0,0,0,0.75),
                    rgba(0,0,0,0.95)
                ),
                radial-gradient(
                    circle at center,
                    #4d0000,
                    #111 65%
                );
        }

        .topo-conteudo {
            max-width: 850px;
        }

        .topo-conteudo .icone {
            font-size: 50px;
            color: #e10600;
            margin-bottom: 15px;
        }

        .topo-conteudo h1 {
            font-size: 52px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .topo-conteudo h1 span {
            color: #e10600;
        }

        .topo-conteudo p {
            color: #bbb;
            font-size: 18px;
            line-height: 1.6;
        }

        /* LOJA */

        .loja {
            max-width: 1250px;
            margin: auto;
            padding: 70px 25px;
        }

        .titulo-secao {
            text-align: center;
            margin-bottom: 45px;
        }

        .titulo-secao h2 {
            font-size: 35px;
            text-transform: uppercase;
        }

        .titulo-secao h2 span {
            color: #e10600;
        }

        .linha-vermelha {
            width: 70px;
            height: 4px;
            background: #e10600;
            margin: 15px auto;
        }

        .titulo-secao p {
            color: #999;
        }

        /* PRODUTOS */

        .produtos {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .produto {
            background: #1c1c1c;
            border: 1px solid #333;
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s;
            position: relative;
        }

        .produto:hover {
            transform: translateY(-8px);
            border-color: #e10600;
            box-shadow: 0 15px 35px rgba(225, 6, 0, 0.18);
        }

        .produto-imagem {
            width: 100%;
            height: 240px;
            background: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .produto-imagem img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.4s;
        }

        .produto:hover .produto-imagem img {
            transform: scale(1.05);
        }

        .sem-foto {
            font-size: 60px;
            color: #555;
        }

        .produto-info {
            padding: 20px;
        }

        .categoria {
            color: #e10600;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .produto-info h3 {
            font-size: 21px;
            margin: 8px 0;
        }

        .descricao {
            color: #999;
            font-size: 14px;
            line-height: 1.5;
            min-height: 43px;
            margin-bottom: 15px;
        }

        .preco {
            font-size: 25px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 8px;
        }

        .estoque {
            font-size: 13px;
            color: #aaa;
            margin-bottom: 18px;
        }

        .estoque strong {
            color: #e10600;
        }

        .botoes {
            display: flex;
            gap: 8px;
        }

        .btn {
            flex: 1;
            text-decoration: none;
            text-align: center;
            padding: 12px 8px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
            transition: 0.3s;
        }

        .btn-detalhes {
            border: 1px solid #555;
            color: #fff;
            background: #292929;
        }

        .btn-detalhes:hover {
            background: #333;
        }

        .btn-comprar {
            background: #e10600;
            color: #fff;
        }

        .btn-comprar:hover {
            background: #b80500;
        }

        .btn-esgotado {
            width: 100%;
            background: #333;
            color: #777;
            cursor: not-allowed;
        }

        /* AVISO LOGIN */

        .aviso-login {
            margin-top: 50px;
            background: #1c1c1c;
            border-left: 4px solid #e10600;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-radius: 6px;
        }

        .aviso-login h3 {
            margin-bottom: 5px;
        }

        .aviso-login p {
            color: #999;
        }

        .btn-login {
            background: #e10600;
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: bold;
            white-space: nowrap;
        }

        .btn-login:hover {
            background: #b80500;
        }

        /* SEM PRODUTOS */

        .sem-produtos {
            text-align: center;
            background: #1c1c1c;
            padding: 60px 20px;
            border-radius: 10px;
        }

        .sem-produtos i {
            font-size: 55px;
            color: #e10600;
            margin-bottom: 20px;
        }

        .sem-produtos h2 {
            margin-bottom: 10px;
        }

        .sem-produtos p {
            color: #999;
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

        @media (max-width: 1000px) {

            .produtos {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 700px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            .menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .topo-conteudo h1 {
                font-size: 38px;
            }

            .produtos {
                grid-template-columns: 1fr;
            }

            .aviso-login {
                flex-direction: column;
                align-items: flex-start;
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


<!-- TOPO -->

<section class="topo-loja">

    <div class="topo-conteudo">

        <div class="icone">
            <i class="fas fa-shopping-bag"></i>
        </div>

        <h1>
            BOXE <span>FIGHT</span> STORE
        </h1>

        <p>
            Equipamentos, acessórios e produtos para quem leva
            o boxe a sério.
        </p>

    </div>

</section>


<!-- PRODUTOS -->

<section class="loja">

    <div class="titulo-secao">

        <h2>
            Nossos <span>Produtos</span>
        </h2>

        <div class="linha-vermelha"></div>

        <p>
            Escolha seus equipamentos e prepare-se para o próximo combate.
        </p>

    </div>


    <?php if ($resultado->num_rows > 0) { ?>

        <div class="produtos">

            <?php while ($produto = $resultado->fetch_assoc()) { ?>

                <div class="produto">

                    <div class="produto-imagem">

                        <?php if (!empty($produto["foto"])) { ?>

                            <img
                                src="imagens/<?php echo htmlspecialchars($produto["foto"]); ?>"
                                alt="<?php echo htmlspecialchars($produto["nome"]); ?>"
                            >

                        <?php } else { ?>

                            <div class="sem-foto">
                                <i class="fas fa-box-open"></i>
                            </div>

                        <?php } ?>

                    </div>


                    <div class="produto-info">

                        <div class="categoria">
                            <?php echo htmlspecialchars($produto["categoria"]); ?>
                        </div>

                        <h3>
                            <?php echo htmlspecialchars($produto["nome"]); ?>
                        </h3>

                        <p class="descricao">
                            <?php echo htmlspecialchars($produto["descricao"]); ?>
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

                                Disponível:
                                <strong>
                                    <?php echo $produto["estoque"]; ?>
                                </strong>
                                unidades

                            <?php } else { ?>

                                <strong>Produto esgotado</strong>

                            <?php } ?>

                        </div>


                        <div class="botoes">

                            <a
                                href="produto.php?id=<?php echo $produto["id"]; ?>"
                                class="btn btn-detalhes"
                            >
                                VER DETALHES
                            </a>


                            <?php if ($produto["estoque"] > 0) { ?>

                                <?php if (isset($_SESSION["id"])) { ?>

                                    <a
                                        href="adicionar_carrinho.php?id=<?php echo $produto["id"]; ?>"
                                        class="btn btn-comprar"
                                    >
                                        <i class="fas fa-cart-plus"></i>
                                        COMPRAR
                                    </a>

                                <?php } else { ?>

                                    <a
                                        href="login.php"
                                        class="btn btn-comprar"
                                    >
                                        ENTRAR
                                    </a>

                                <?php } ?>

                            <?php } else { ?>

                                <span class="btn btn-esgotado">
                                    ESGOTADO
                                </span>

                            <?php } ?>

                        </div>

                    </div>

                </div>

            <?php } ?>

        </div>


        <?php if (!isset($_SESSION["id"])) { ?>

            <div class="aviso-login">

                <div>

                    <h3>
                        Quer comprar nossos produtos?
                    </h3>

                    <p>
                        Crie sua conta ou faça login para adicionar
                        produtos ao carrinho e realizar pedidos.
                    </p>

                </div>

                <a href="cadastro.php" class="btn-login">
                    CRIAR CONTA
                </a>

            </div>

        <?php } ?>


    <?php } else { ?>

        <div class="sem-produtos">

            <i class="fas fa-store-slash"></i>

            <h2>
                Nenhum produto disponível
            </h2>

            <p>
                Nossa loja ainda não possui produtos cadastrados.
            </p>

        </div>

    <?php } ?>

</section>


<!-- FOOTER -->

<footer>

    <p>
        <strong>BOXE<span>FIGHT</span></strong>
        — Loja oficial do projeto Boxe Fight.
    </p>

    <p>
        © 2026 Boxe Fight. Todos os direitos reservados.
    </p>

</footer>

</body>

</html>

<?php

$conexao->close();

?>
