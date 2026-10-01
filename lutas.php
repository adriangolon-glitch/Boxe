<?php
session_start();
include("conexao.php");

$logado = isset($_SESSION["id"]);
$admin = isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin";


/* =========================
   BUSCAR LUTAS
========================= */

$sql = "
    SELECT 
        l.*,
        a.nome AS nome_lutador1,
        a.foto AS foto_lutador1,
        a.categoria AS categoria_lutador1,
        b.nome AS nome_lutador2,
        b.foto AS foto_lutador2,
        b.categoria AS categoria_lutador2
    FROM lutas l

    LEFT JOIN lutadores a
        ON l.lutador1 = a.nome

    LEFT JOIN lutadores b
        ON l.lutador2 = b.nome

    ORDER BY l.data_luta DESC, l.id DESC
";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lutas - Boxe Fight</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #090909;
            color: white;
        }

        /* =========================
           NAVBAR
        ========================= */

        nav {
            width: 100%;
            background: #000;
            border-bottom: 1px solid #292929;
            padding: 18px 5%;

            display: flex;
            justify-content: space-between;
            align-items: center;

            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            color: #e10600;
            font-size: 25px;
            font-weight: 900;
            letter-spacing: 2px;
        }

        .logo span {
            color: white;
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .menu a {
            color: #ccc;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: .3s;
        }

        .menu a:hover {
            color: #e10600;
        }

        .sair {
            background: #e10600;
            color: white !important;
            padding: 10px 16px;
            border-radius: 5px;
        }

        .sair:hover {
            background: #b80500 !important;
        }

        /* =========================
           CONTAINER
        ========================= */

        .container {
            width: 90%;
            max-width: 1300px;
            margin: 55px auto;
        }

        .topo {
            margin-bottom: 35px;
        }

        .topo h1 {
            font-size: 44px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .topo h1 span {
            color: #e10600;
        }

        .topo p {
            color: #999;
            margin-top: 8px;
        }

        /* =========================
           BOTÃO ADMIN
        ========================= */

        .admin-topo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            background: #e10600;
            color: white;
            padding: 12px 18px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
            transition: .3s;
        }

        .btn:hover {
            background: #b80500;
            transform: translateY(-2px);
        }

        /* =========================
           LISTA DE LUTAS
        ========================= */

        .lista-lutas {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .luta {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 10px;
            overflow: hidden;
            transition: .3s;
        }

        .luta:hover {
            border-color: #555;
            transform: translateY(-2px);
        }

        .luta-topo {
            background: #101010;
            border-bottom: 1px solid #292929;
            padding: 13px 20px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .data {
            color: #999;
            font-size: 12px;
        }

        .id-luta {
            color: #e10600;
            font-size: 11px;
            font-weight: bold;
        }

        .confronto {
            padding: 25px;

            display: grid;
            grid-template-columns: 1fr 100px 1fr;
            align-items: center;
            gap: 20px;
        }

        /* =========================
           LUTADORES
        ========================= */

        .lutador {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .lutador-direita {
            flex-direction: row-reverse;
            text-align: right;
        }

        .foto {
            width: 85px;
            height: 85px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #333;
            background: #222;
        }

        .sem-foto {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            background: #222;
            border: 3px solid #333;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #e10600;
            font-size: 30px;
            font-weight: 900;
        }

        .lutador-info h2 {
            font-size: 20px;
            text-transform: uppercase;
        }

        .lutador-info p {
            color: #888;
            font-size: 12px;
            margin-top: 6px;
        }

        /* =========================
           VS
        ========================= */

        .versus {
            text-align: center;
        }

        .vs {
            width: 65px;
            height: 65px;
            margin: auto;

            background: #e10600;
            border-radius: 50%;

            display: flex;
            justify-content: center;
            align-items: center;

            font-size: 20px;
            font-weight: 900;
        }

        .resultado {
            color: #aaa;
            font-size: 10px;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }

        /* =========================
           RODAPÉ DA LUTA
        ========================= */

        .luta-footer {
            background: #101010;
            border-top: 1px solid #292929;

            padding: 14px 20px;

            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .metodo {
            color: #888;
            font-size: 12px;
        }

        .metodo strong {
            color: white;
        }

        .acoes {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .btn-detalhes {
            background: #e10600;
            color: white;
            text-decoration: none;

            padding: 9px 14px;
            border-radius: 4px;

            font-size: 11px;
            font-weight: bold;

            transition: .3s;
        }

        .btn-detalhes:hover {
            background: #b80500;
        }

        .btn-editar {
            background: #333;
            color: white;
            text-decoration: none;

            padding: 9px 14px;
            border-radius: 4px;

            font-size: 11px;
            font-weight: bold;
        }

        .btn-editar:hover {
            background: #444;
        }

        .btn-excluir {
            background: #570000;
            color: #ffaaaa;
            text-decoration: none;

            padding: 9px 14px;
            border-radius: 4px;

            font-size: 11px;
            font-weight: bold;
        }

        .btn-excluir:hover {
            background: #750000;
            color: white;
        }

        /* =========================
           SEM LUTAS
        ========================= */

        .sem-lutas {
            background: #151515;
            border: 1px dashed #333;
            border-radius: 10px;
            text-align: center;
            padding: 70px 20px;
        }

        .sem-lutas h2 {
            margin-bottom: 10px;
        }

        .sem-lutas p {
            color: #777;
            margin-bottom: 20px;
        }

        /* =========================
           FOOTER
        ========================= */

        footer {
            margin-top: 70px;
            background: #000;
            border-top: 1px solid #292929;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 13px;
        }

        footer strong {
            color: #e10600;
        }

        /* =========================
           RESPONSIVO
        ========================= */

        @media (max-width: 900px) {

            .menu {
                gap: 10px;
            }

            .menu a:nth-child(1),
            .menu a:nth-child(2),
            .menu a:nth-child(3) {
                display: none;
            }

            .confronto {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .lutador,
            .lutador-direita {
                justify-content: center;
                flex-direction: column;
                text-align: center;
            }

            .versus {
                order: 2;
            }

            .lutador:first-child {
                order: 1;
            }

            .lutador:last-child {
                order: 3;
            }

            .luta-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .acoes {
                justify-content: center;
            }

        }

        @media (max-width: 600px) {

            .container {
                width: 94%;
                margin-top: 35px;
            }

            .topo h1 {
                font-size: 32px;
            }

            .admin-topo {
                flex-direction: column;
                align-items: flex-start;
            }

            .confronto {
                padding: 25px 15px;
            }

            .luta-footer {
                padding: 15px;
            }

            .acoes {
                flex-direction: column;
            }

            .acoes a {
                text-align: center;
            }

        }

    </style>

</head>

<body>


<!-- =========================
     NAVBAR
========================= -->

<nav>

    <div class="logo">
        BOXE <span>FIGHT</span>
    </div>

    <div class="menu">

        <a href="index.php">Início</a>
        <a href="arena.php">Arena</a>
        <a href="lutadores.php">Lutadores</a>
        <a href="lutas.php">Lutas</a>
        <a href="loja.php">Loja</a>

        <?php if ($logado) { ?>

            <a href="perfil.php">Perfil</a>

        <?php } ?>

        <?php if ($admin) { ?>

            <a href="dashboard.php">Admin</a>

        <?php } ?>

        <?php if ($logado) { ?>

            <a href="logout.php" class="sair">
                Sair
            </a>

        <?php } else { ?>

            <a href="login.php" class="sair">
                Entrar
            </a>

        <?php } ?>

    </div>

</nav>


<!-- =========================
     CONTEÚDO
========================= -->

<div class="container">


    <div class="admin-topo">

        <div class="topo">

            <h1>
                Todas as <span>Lutas</span>
            </h1>

            <p>
                Confira os confrontos registrados no Boxe Fight.
            </p>

        </div>


        <?php if ($admin) { ?>

            <a href="cadastrar_luta.php" class="btn">
                + Cadastrar Luta
            </a>

        <?php } ?>

    </div>


    <!-- =========================
         LISTA
    ========================= -->

    <?php if ($resultado->num_rows > 0) { ?>

        <div class="lista-lutas">


            <?php while ($luta = $resultado->fetch_assoc()) { ?>


                <?php

                $nome1 = !empty($luta["nome_lutador1"])
                    ? $luta["nome_lutador1"]
                    : $luta["lutador1"];

                $nome2 = !empty($luta["nome_lutador2"])
                    ? $luta["nome_lutador2"]
                    : $luta["lutador2"];


                if ($luta["resultado"] == "Vitória do Lutador 1") {

                    $resultado_texto = "Vitória de " . $nome1;

                } elseif ($luta["resultado"] == "Vitória do Lutador 2") {

                    $resultado_texto = "Vitória de " . $nome2;

                } elseif ($luta["resultado"] == "Empate") {

                    $resultado_texto = "Empate";

                } else {

                    $resultado_texto = "Sem resultado";

                }

                ?>


                <div class="luta">


                    <!-- CABEÇALHO -->

                    <div class="luta-topo">

                        <span class="data">

                            <?php
                            echo date(
                                "d/m/Y",
                                strtotime($luta["data_luta"])
                            );
                            ?>

                        </span>


                        <span class="id-luta">

                            LUTA #<?php echo $luta["id"]; ?>

                        </span>

                    </div>


                    <!-- CONFRONTO -->

                    <div class="confronto">


                        <!-- LUTADOR 1 -->

                        <div class="lutador">


                            <?php if (!empty($luta["foto_lutador1"])) { ?>

                                <img
                                    class="foto"
                                    src="fotos/<?php echo htmlspecialchars($luta["foto_lutador1"]); ?>"
                                    alt="<?php echo htmlspecialchars($nome1); ?>"
                                >

                            <?php } else { ?>

                                <div class="sem-foto">

                                    <?php
                                    echo strtoupper(
                                        substr($nome1, 0, 1)
                                    );
                                    ?>

                                </div>

                            <?php } ?>


                            <div class="lutador-info">

                                <h2>
                                    <?php echo htmlspecialchars($nome1); ?>
                                </h2>

                                <p>
                                    <?php
                                    echo !empty($luta["categoria_lutador1"])
                                        ? htmlspecialchars($luta["categoria_lutador1"])
                                        : "Categoria não informada";
                                    ?>
                                </p>

                            </div>

                        </div>


                        <!-- VS -->

                        <div class="versus">

                            <div class="vs">
                                VS
                            </div>

                            <div class="resultado">

                                <?php echo htmlspecialchars($resultado_texto); ?>

                            </div>

                        </div>


                        <!-- LUTADOR 2 -->

                        <div class="lutador lutador-direita">


                            <?php if (!empty($luta["foto_lutador2"])) { ?>

                                <img
                                    class="foto"
                                    src="fotos/<?php echo htmlspecialchars($luta["foto_lutador2"]); ?>"
                                    alt="<?php echo htmlspecialchars($nome2); ?>"
                                >

                            <?php } else { ?>

                                <div class="sem-foto">

                                    <?php
                                    echo strtoupper(
                                        substr($nome2, 0, 1)
                                    );
                                    ?>

                                </div>

                            <?php } ?>


                            <div class="lutador-info">

                                <h2>
                                    <?php echo htmlspecialchars($nome2); ?>
                                </h2>

                                <p>
                                    <?php
                                    echo !empty($luta["categoria_lutador2"])
                                        ? htmlspecialchars($luta["categoria_lutador2"])
                                        : "Categoria não informada";
                                    ?>
                                </p>

                            </div>

                        </div>


                    </div>


                    <!-- RODAPÉ -->

                    <div class="luta-footer">


                        <div class="metodo">

                            Método:

                            <strong>
                                <?php echo htmlspecialchars($luta["metodo"]); ?>
                            </strong>

                        </div>


                        <div class="acoes">


                            <!-- NOVO BOTÃO -->

                            <a
                                href="detalhes_luta.php?id=<?php echo $luta["id"]; ?>"
                                class="btn-detalhes"
                            >
                                VER DETALHES
                            </a>


                            <?php if ($admin) { ?>

                                <a
                                    href="editar_luta.php?id=<?php echo $luta["id"]; ?>"
                                    class="btn-editar"
                                >
                                    EDITAR
                                </a>


                                <a
                                    href="excluir_luta.php?id=<?php echo $luta["id"]; ?>"
                                    class="btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir esta luta?');"
                                >
                                    EXCLUIR
                                </a>

                            <?php } ?>


                        </div>

                    </div>


                </div>


            <?php } ?>


        </div>


    <?php } else { ?>


        <div class="sem-lutas">

            <h2>
                Nenhuma luta cadastrada
            </h2>

            <p>
                Ainda não existem confrontos registrados no sistema.
            </p>


            <?php if ($admin) { ?>

                <a href="cadastrar_luta.php" class="btn">
                    Cadastrar primeira luta
                </a>

            <?php } ?>

        </div>


    <?php } ?>


</div>


<!-- =========================
     FOOTER
========================= -->

<footer>

    <p>
        © 2026 <strong>Boxe Fight</strong> —
        Sistema de Boxe e Artes Marciais.
    </p>

</footer>


</body>

</html>

<?php
$conexao->close();
?>

