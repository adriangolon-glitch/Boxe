<?php

session_start();

// Se não estiver logado, volta para o login
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION["usuario"];
$tipo = $_SESSION["tipo"];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel - Boxe Fight</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #111111;
            color: #ffffff;
        }

        /* NAVBAR */

        nav {
            background: #000000;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e10600;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
            color: #ffffff;
        }

        .logo span {
            color: #e10600;
        }

        nav a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        nav a:hover {
            color: #e10600;
        }

        /* CONTAINER */

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 50px auto;
        }

        .topo {
            text-align: center;
            margin-bottom: 40px;
        }

        .topo h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .topo h1 span {
            color: #e10600;
        }

        .topo p {
            color: #bbbbbb;
            font-size: 18px;
        }

        /* AVISO DO TIPO */

        .tipo {
            width: fit-content;
            margin: 20px auto 0;
            padding: 10px 22px;
            border-radius: 30px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .tipo-admin {
            background: #e10600;
            color: #ffffff;
        }

        .tipo-usuario {
            background: #333333;
            color: #ffffff;
            border: 1px solid #555555;
        }

        /* CARDS */

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: #1c1c1c;
            border: 1px solid #333333;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: #e10600;
        }

        .card .icone {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .card h2 {
            margin-bottom: 10px;
        }

        .card p {
            color: #bbbbbb;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .botao {
            display: inline-block;
            background: #e10600;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 6px;
            font-weight: bold;
            transition: 0.3s;
        }

        .botao:hover {
            background: #b80500;
        }

        .botao-cinza {
            background: #333333;
        }

        .botao-cinza:hover {
            background: #555555;
        }

        /* ÁREA ADMIN */

        .admin-area {
            margin-top: 50px;
        }

        .admin-titulo {
            background: #e10600;
            padding: 18px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .admin-titulo h2 {
            font-size: 25px;
        }

        .admin-conteudo {
            background: #1c1c1c;
            padding: 30px;
            border: 1px solid #333333;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .admin-conteudo p {
            color: #cccccc;
            text-align: center;
            margin-bottom: 25px;
        }

        .admin-botoes {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .admin-botao {
            background: #000000;
            border: 1px solid #e10600;
            color: #ffffff;
            text-decoration: none;
            padding: 18px;
            text-align: center;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .admin-botao:hover {
            background: #e10600;
        }

        /* ÁREA EXCLUSIVA */

        .exclusivo {
            margin-top: 50px;
            background: #1c1c1c;
            border-left: 5px solid #e10600;
            padding: 30px;
            border-radius: 8px;
        }

        .exclusivo h2 {
            margin-bottom: 12px;
        }

        .exclusivo p {
            color: #cccccc;
            line-height: 1.6;
        }

        /* FOOTER */

        footer {
            background: #000000;
            text-align: center;
            padding: 25px;
            margin-top: 60px;
            border-top: 1px solid #333333;
            color: #999999;
        }

        /* RESPONSIVO */

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .admin-botoes {
                grid-template-columns: 1fr;
            }

            nav {
                flex-direction: column;
                gap: 15px;
            }

            nav a {
                margin: 5px;
            }

        }

    </style>

</head>

<body>

    <nav>

        <div class="logo">
            BOXE <span>FIGHT</span>
        </div>

        <div>

            <a href="index.php">Início</a>

            <a href="lutadores.php">Lutadores</a>

            <a href="lutas.php">Lutas</a>

            <a href="logout.php">Sair</a>

        </div>

    </nav>


    <div class="container">

        <div class="topo">

            <?php if ($tipo == "admin") { ?>

                <h1>
                    PAINEL <span>ADMINISTRATIVO</span>
                </h1>

                <p>
                    Bem-vindo, administrador <strong><?php echo htmlspecialchars($usuario); ?></strong>.
                </p>

                <div class="tipo tipo-admin">
                    ADMINISTRADOR
                </div>

            <?php } else { ?>

                <h1>
                    PAINEL DO <span>USUÁRIO</span>
                </h1>

                <p>
                    Bem-vindo, <strong><?php echo htmlspecialchars($usuario); ?></strong>!
                </p>

                <div class="tipo tipo-usuario">
                    USUÁRIO
                </div>

            <?php } ?>

        </div>


        <!-- ÁREA DO USUÁRIO -->

        <div class="cards">

            <div class="card">

                <div class="icone">
                    🥊
                </div>

                <h2>Lutadores</h2>

                <p>
                    Consulte os lutadores cadastrados,
                    suas categorias e seus históricos.
                </p>

                <a href="lutadores.php" class="botao">
                    VER LUTADORES
                </a>

            </div>


            <div class="card">

                <div class="icone">
                    🏆
                </div>

                <h2>Lutas</h2>

                <p>
                    Veja o histórico de lutas,
                    resultados e métodos de vitória.
                </p>

                <a href="lutas.php" class="botao">
                    VER LUTAS
                </a>

            </div>


            <div class="card">

                <div class="icone">
                    🔒
                </div>

                <h2>Área Exclusiva</h2>

                <p>
                    Você está conectado e possui acesso
                    a conteúdos exclusivos para membros.
                </p>

                <a href="#exclusivo" class="botao botao-cinza">
                    ACESSAR
                </a>

            </div>

        </div>


        <!-- ÁREA EXCLUSIVA PARA QUEM ESTÁ LOGADO -->

        <div class="exclusivo" id="exclusivo">

            <h2>🔐 Conteúdo exclusivo para membros</h2>

            <p>
                Esta área só pode ser acessada por usuários
                que possuem uma conta e fizeram login no sistema.
                Aqui poderão ser adicionadas informações,
                estatísticas, novidades e conteúdos exclusivos
                do Boxe Fight.
            </p>

        </div>


        <!-- ÁREA SOMENTE DO ADMIN -->

        <?php if ($tipo == "admin") { ?>

            <div class="admin-area">

                <div class="admin-titulo">

                    <h2>
                        ÁREA DE ADMINISTRAÇÃO
                    </h2>

                </div>

                <div class="admin-conteudo">

                    <p>
                        Como administrador, você possui acesso
                        ao gerenciamento completo do sistema.
                    </p>

                    <div class="admin-botoes">

                        <a href="cadastrar_lutador.php" class="admin-botao">
                            + CADASTRAR LUTADOR
                        </a>

                        <a href="cadastrar_luta.php" class="admin-botao">
                            + CADASTRAR LUTA
                        </a>

                        <a href="usuarios.php" class="admin-botao">
                            GERENCIAR USUÁRIOS
                        </a>

                        <a href="lutadores.php" class="admin-botao">
                            EDITAR LUTADORES
                        </a>

                        <a href="lutas.php" class="admin-botao">
                            EDITAR LUTAS
                        </a>

                        <a href="index.php" class="admin-botao">
                            VER SITE
                        </a>

                    </div>

                </div>

            </div>

        <?php } ?>


    </div>


    <footer>

        <p>
            &copy; 2026 Boxe Fight - Sistema de Boxe
        </p>

    </footer>

</body>

</html>
