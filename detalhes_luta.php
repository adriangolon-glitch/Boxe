<?php
session_start();
include("conexao.php");

/* =========================
   VERIFICAR ID
========================= */

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: lutas.php");
    exit;
}

$id = intval($_GET["id"]);


/* =========================
   BUSCAR LUTA
========================= */

$sql = "
    SELECT
        l.*,

        a.id AS id_lutador1,
        a.nome AS nome_lutador1,
        a.nacionalidade AS nacionalidade1,
        a.idade AS idade1,
        a.categoria AS categoria1,
        a.foto AS foto1,

        b.id AS id_lutador2,
        b.nome AS nome_lutador2,
        b.nacionalidade AS nacionalidade2,
        b.idade AS idade2,
        b.categoria AS categoria2,
        b.foto AS foto2

    FROM lutas l

    LEFT JOIN lutadores a
        ON l.lutador1 = a.nome

    LEFT JOIN lutadores b
        ON l.lutador2 = b.nome

    WHERE l.id = ?
";


$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar a consulta.");
}

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();


/* =========================
   VERIFICAR SE A LUTA EXISTE
========================= */

if ($resultado->num_rows == 0) {
    header("Location: lutas.php");
    exit;
}

$luta = $resultado->fetch_assoc();


/* =========================
   NOMES DOS LUTADORES
========================= */

$nome1 = !empty($luta["nome_lutador1"])
    ? $luta["nome_lutador1"]
    : $luta["lutador1"];

$nome2 = !empty($luta["nome_lutador2"])
    ? $luta["nome_lutador2"]
    : $luta["lutador2"];


/* =========================
   DEFINIR RESULTADO
========================= */

$resultado_luta = $luta["resultado"];

$lado_vencedor = 0;
$vencedor = "SEM RESULTADO";
$texto_resultado = "SEM RESULTADO";


if ($resultado_luta == "Vitória do Lutador 1") {

    $vencedor = $nome1;
    $texto_resultado = "VITÓRIA";
    $lado_vencedor = 1;

} elseif ($resultado_luta == "Vitória do Lutador 2") {

    $vencedor = $nome2;
    $texto_resultado = "VITÓRIA";
    $lado_vencedor = 2;

} elseif ($resultado_luta == "Empate") {

    $vencedor = "EMPATE";
    $texto_resultado = "EMPATE";
    $lado_vencedor = 0;

} elseif ($resultado_luta == "Sem resultado") {

    $vencedor = "SEM RESULTADO";
    $texto_resultado = "SEM RESULTADO";
    $lado_vencedor = 0;
}


/* =========================
   DATA
========================= */

$data_formatada = date(
    "d/m/Y",
    strtotime($luta["data_luta"])
);


/* =========================
   CATEGORIAS
========================= */

$categoria1 = !empty($luta["categoria1"])
    ? $luta["categoria1"]
    : "Categoria não informada";

$categoria2 = !empty($luta["categoria2"])
    ? $luta["categoria2"]
    : "Categoria não informada";


/* =========================
   NACIONALIDADES
========================= */

$nacionalidade1 = !empty($luta["nacionalidade1"])
    ? $luta["nacionalidade1"]
    : "Não informada";

$nacionalidade2 = !empty($luta["nacionalidade2"])
    ? $luta["nacionalidade2"]
    : "Não informada";


/* =========================
   IDADES
========================= */

$idade1 = !empty($luta["idade1"])
    ? $luta["idade1"] . " anos"
    : "Não informada";

$idade2 = !empty($luta["idade2"])
    ? $luta["idade2"] . " anos"
    : "Não informada";


/* =========================
   USUÁRIO
========================= */

$logado = isset($_SESSION["id"]);

$admin = isset($_SESSION["tipo"])
    && $_SESSION["tipo"] == "admin";

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($nome1); ?>
        vs
        <?php echo htmlspecialchars($nome2); ?>
        - Boxe Fight
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }


        body {
            background: #080808;
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
            gap: 20px;
            align-items: center;
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


        .btn-menu {
            background: #e10600;
            color: white !important;

            padding: 10px 15px;

            border-radius: 5px;
        }


        .btn-menu:hover {
            background: #b80500;
        }


        /* =========================
           HERO
        ========================= */

        .hero {

            min-height: 350px;

            background:
                linear-gradient(
                    rgba(0,0,0,.72),
                    rgba(0,0,0,.92)
                ),
                url("imagens/boxe1.jpg")
                center/cover;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 60px 20px;
        }


        .hero-conteudo {
            max-width: 900px;
        }


        .tag {

            display: inline-block;

            color: #e10600;

            border: 1px solid #e10600;

            padding: 7px 15px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: bold;

            letter-spacing: 2px;

            margin-bottom: 20px;
        }


        .hero h1 {

            font-size: 55px;

            font-weight: 900;

            text-transform: uppercase;
        }


        .hero h1 span {
            color: #e10600;
        }


        .hero p {

            color: #aaa;

            margin-top: 15px;

            font-size: 15px;
        }


        /* =========================
           CONTAINER
        ========================= */

        .container {

            width: 90%;

            max-width: 1200px;

            margin: -70px auto 70px;

            position: relative;
        }


        /* =========================
           CARD PRINCIPAL
        ========================= */

        .luta-card {

            background: #141414;

            border: 1px solid #292929;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 20px 60px
                rgba(0,0,0,.5);
        }


        /* =========================
           CABEÇALHO DA LUTA
        ========================= */

        .cabecalho-luta {

            background: #101010;

            border-bottom: 1px solid #292929;

            padding: 18px 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }


        .evento {

            color: #999;

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: 1px;
        }


        .data {

            color: white;

            font-weight: bold;

            font-size: 14px;
        }


        /* =========================
           LUTADORES
        ========================= */

        .lutadores {

            display: grid;

            grid-template-columns:
                1fr 180px 1fr;

            align-items: center;

            padding: 45px 35px;
        }


        .lutador {

            text-align: center;
        }


        .foto {

            width: 210px;
            height: 210px;

            border-radius: 50%;

            object-fit: cover;

            border: 5px solid #292929;

            background: #222;

            transition: .3s;
        }


        .vencedor .foto {

            border-color: #e10600;

            box-shadow:
                0 0 35px
                rgba(225,6,0,.25);
        }


        .sem-foto {

            width: 210px;
            height: 210px;

            border-radius: 50%;

            background: #222;

            border: 5px solid #292929;

            display: flex;

            justify-content: center;
            align-items: center;

            margin: auto;

            font-size: 65px;

            font-weight: 900;

            color: #e10600;
        }


        .vencedor .sem-foto {

            border-color: #e10600;
        }


        .lutador h2 {

            margin-top: 20px;

            font-size: 28px;

            text-transform: uppercase;
        }


        .lutador p {

            color: #999;

            font-size: 13px;

            margin-top: 7px;
        }


        .winner {

            display: inline-block;

            background: #e10600;

            color: white;

            padding: 6px 12px;

            border-radius: 4px;

            margin-top: 12px;

            font-size: 10px;

            font-weight: bold;
        }


        /* =========================
           VS
        ========================= */

        .versus {

            text-align: center;
        }


        .versus span {

            display: flex;

            align-items: center;
            justify-content: center;

            margin: auto;

            width: 90px;
            height: 90px;

            border-radius: 50%;

            background: #e10600;

            color: white;

            font-size: 28px;

            font-weight: 900;

            box-shadow:
                0 0 35px
                rgba(225,6,0,.2);
        }


        .resultado {

            margin-top: 15px;

            color: #aaa;

            font-size: 11px;

            text-transform: uppercase;

            font-weight: bold;
        }


        /* =========================
           DETALHES
        ========================= */

        .detalhes {

            border-top: 1px solid #292929;

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);
        }


        .detalhe {

            padding: 25px;

            text-align: center;

            border-right: 1px solid #292929;
        }


        .detalhe:last-child {

            border-right: none;
        }


        .detalhe span {

            display: block;

            color: #777;

            font-size: 11px;

            text-transform: uppercase;

            margin-bottom: 8px;
        }


        .detalhe strong {

            font-size: 17px;

            color: white;
        }


        /* =========================
           PERFIS
        ========================= */

        .info-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 25px;

            margin-top: 25px;
        }


        .info-card {

            background: #141414;

            border: 1px solid #292929;

            border-radius: 10px;

            padding: 25px;
        }


        .info-card h2 {

            font-size: 19px;

            margin-bottom: 20px;

            text-transform: uppercase;
        }


        .info-card h2 span {

            color: #e10600;
        }


        .linha {

            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 12px 0;

            border-bottom: 1px solid #292929;

            font-size: 13px;
        }


        .linha:last-child {

            border-bottom: none;
        }


        .linha span {

            color: #777;
        }


        .linha strong {

            color: white;

            text-align: right;
        }


        /* =========================
           BOTÕES
        ========================= */

        .acoes {

            display: flex;

            justify-content: center;

            gap: 15px;

            margin-top: 30px;
        }


        .btn {

            display: inline-block;

            text-decoration: none;

            padding: 13px 22px;

            border-radius: 5px;

            background: #e10600;

            color: white;

            font-weight: bold;

            font-size: 13px;

            transition: .3s;
        }


        .btn:hover {

            background: #b80500;

            transform: translateY(-2px);
        }


        .btn-secundario {

            background: #222;

            border: 1px solid #333;
        }


        .btn-secundario:hover {

            background: #333;
        }


        /* =========================
           ADMIN
        ========================= */

        .admin-area {

            margin-top: 25px;

            background: #111;

            border: 1px solid #292929;

            border-left: 4px solid #e10600;

            border-radius: 8px;

            padding: 20px;
        }


        .admin-area h3 {

            color: #e10600;

            font-size: 15px;

            margin-bottom: 8px;

            text-transform: uppercase;
        }


        .admin-area p {

            color: #777;

            font-size: 12px;

            margin-bottom: 15px;
        }


        .admin-botoes {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;
        }


        /* =========================
           FOOTER
        ========================= */

        footer {

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


            .lutadores {

                grid-template-columns: 1fr;

                gap: 35px;
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


            .info-grid {

                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 600px) {

            .hero h1 {

                font-size: 35px;
            }


            .container {

                width: 94%;
            }


            .cabecalho-luta {

                flex-direction: column;

                gap: 10px;

                align-items: flex-start;
            }


            .lutadores {

                padding: 30px 15px;
            }


            .foto,
            .sem-foto {

                width: 160px;
                height: 160px;
            }


            .lutador h2 {

                font-size: 22px;
            }


            .versus span {

                width: 70px;
                height: 70px;

                font-size: 22px;
            }


            .detalhes {

                grid-template-columns: 1fr;
            }


            .detalhe {

                border-right: none;

                border-bottom:
                    1px solid #292929;
            }


            .detalhe:last-child {

                border-bottom: none;
            }


            .acoes {

                flex-direction: column;
            }


            .acoes .btn {

                text-align: center;
            }


            .admin-botoes {

                flex-direction: column;
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

        <a href="index.php">
            Início
        </a>

        <a href="arena.php">
            Arena
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


        <?php if ($logado) { ?>

            <a href="perfil.php">
                Perfil
            </a>

        <?php } ?>


        <?php if ($admin) { ?>

            <a href="dashboard.php">
                Admin
            </a>

        <?php } ?>


        <?php if ($logado) { ?>

            <a
                href="logout.php"
                class="btn-menu"
            >
                Sair
            </a>

        <?php } else { ?>

            <a
                href="login.php"
                class="btn-menu"
            >
                Entrar
            </a>

        <?php } ?>

    </div>

</nav>


<!-- =========================
     HERO
========================= -->

<section class="hero">

    <div class="hero-conteudo">

        <div class="tag">

            BOXE FIGHT • COMBATE

        </div>


        <h1>

            <span>FIGHT</span> NIGHT

        </h1>


        <p>

            Confira todos os detalhes deste confronto.

        </p>

    </div>

</section>


<!-- =========================
     CONTEÚDO
========================= -->

<div class="container">


    <!-- =========================
         LUTA
    ========================= -->

    <div class="luta-card">


        <!-- CABEÇALHO -->

        <div class="cabecalho-luta">

            <span class="evento">

                BOXE FIGHT •
                CONFRONTO
                #<?php echo $luta["id"]; ?>

            </span>


            <span class="data">

                <?php echo $data_formatada; ?>

            </span>

        </div>


        <!-- LUTADORES -->

        <div class="lutadores">


            <!-- LUTADOR 1 -->

            <div
                class="lutador
                <?php
                echo ($lado_vencedor == 1)
                    ? "vencedor"
                    : "";
                ?>"
            >

                <?php if (!empty($luta["foto1"])) { ?>

                    <img
                        class="foto"
                        src="fotos/<?php
                        echo htmlspecialchars(
                            $luta["foto1"]
                        );
                        ?>"
                        alt="<?php
                        echo htmlspecialchars(
                            $nome1
                        );
                        ?>"
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


                <h2>

                    <?php
                    echo htmlspecialchars($nome1);
                    ?>

                </h2>


                <p>

                    <?php
                    echo htmlspecialchars($categoria1);
                    ?>

                </p>


                <?php if ($lado_vencedor == 1) { ?>

                    <span class="winner">

                        VENCEDOR

                    </span>

                <?php } ?>

            </div>


            <!-- VS -->

            <div class="versus">

                <span>
                    VS
                </span>


                <div class="resultado">

                    <?php
                    echo htmlspecialchars(
                        $texto_resultado
                    );
                    ?>

                </div>

            </div>


            <!-- LUTADOR 2 -->

            <div
                class="lutador
                <?php
                echo ($lado_vencedor == 2)
                    ? "vencedor"
                    : "";
                ?>"
            >

                <?php if (!empty($luta["foto2"])) { ?>

                    <img
                        class="foto"
                        src="fotos/<?php
                        echo htmlspecialchars(
                            $luta["foto2"]
                        );
                        ?>"
                        alt="<?php
                        echo htmlspecialchars(
                            $nome2
                        );
                        ?>"
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


                <h2>

                    <?php
                    echo htmlspecialchars($nome2);
                    ?>

                </h2>


                <p>

                    <?php
                    echo htmlspecialchars($categoria2);
                    ?>

                </p>


                <?php if ($lado_vencedor == 2) { ?>

                    <span class="winner">

                        VENCEDOR

                    </span>

                <?php } ?>

            </div>

        </div>


        <!-- =========================
             DETALHES DA LUTA
        ========================= -->

        <div class="detalhes">


            <div class="detalhe">

                <span>
                    Data
                </span>

                <strong>

                    <?php
                    echo $data_formatada;
                    ?>

                </strong>

            </div>


            <div class="detalhe">

                <span>
                    Método
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $luta["metodo"]
                    );
                    ?>

                </strong>

            </div>


            <div class="detalhe">

                <span>
                    Resultado
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $vencedor
                    );
                    ?>

                </strong>

            </div>

        </div>

    </div>


    <!-- =========================
         PERFIS DOS LUTADORES
    ========================= -->

    <div class="info-grid">


        <!-- LUTADOR 1 -->

        <div class="info-card">

            <h2>

                Perfil

                <span>

                    <?php
                    echo htmlspecialchars($nome1);
                    ?>

                </span>

            </h2>


            <div class="linha">

                <span>
                    Nome
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars($nome1);
                    ?>

                </strong>

            </div>


            <div class="linha">

                <span>
                    Nacionalidade
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $nacionalidade1
                    );
                    ?>

                </strong>

            </div>


            <div class="linha">

                <span>
                    Idade
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars($idade1);
                    ?>

                </strong>

            </div>


            <div class="linha">

                <span>
                    Categoria
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars($categoria1);
                    ?>

                </strong>

            </div>

        </div>


        <!-- LUTADOR 2 -->

        <div class="info-card">

            <h2>

                Perfil

                <span>

                    <?php
                    echo htmlspecialchars($nome2);
                    ?>

                </span>

            </h2>


            <div class="linha">

                <span>
                    Nome
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars($nome2);
                    ?>

                </strong>

            </div>


            <div class="linha">

                <span>
                    Nacionalidade
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $nacionalidade2
                    );
                    ?>

                </strong>

            </div>


            <div class="linha">

                <span>
                    Idade
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars($idade2);
                    ?>

                </strong>

            </div>


            <div class="linha">

                <span>
                    Categoria
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars($categoria2);
                    ?>

                </strong>

            </div>

        </div>

    </div>


    <!-- =========================
         ÁREA DO ADMIN
    ========================= -->

    <?php if ($admin) { ?>

        <div class="admin-area">

            <h3>
                Área administrativa
            </h3>

            <p>
                Como administrador, você pode alterar ou excluir
                este confronto.
            </p>


            <div class="admin-botoes">

                <a
                    href="editar_luta.php?id=<?php
                    echo $luta["id"];
                    ?>"
                    class="btn"
                >
                    Editar luta
                </a>


                <a
                    href="excluir_luta.php?id=<?php
                    echo $luta["id"];
                    ?>"
                    class="btn"
                    onclick="return confirm(
                        'Tem certeza que deseja excluir esta luta?'
                    );"
                >
                    Excluir luta
                </a>

            </div>

        </div>

    <?php } ?>


    <!-- =========================
         BOTÕES
    ========================= -->

    <div class="acoes">

        <a
            href="lutas.php"
            class="btn btn-secundario"
        >
            ← Voltar para Lutas
        </a>


        <a
            href="arena.php"
            class="btn"
        >
            Ir para Arena
        </a>

    </div>


</div>


<!-- =========================
     FOOTER
========================= -->

<footer>

    <p>

        © 2026

        <strong>
            Boxe Fight
        </strong>

        —

        Sistema de Boxe e Artes Marciais.

    </p>

</footer>


</body>

</html>

<?php

$stmt->close();

$conexao->close();

?>
