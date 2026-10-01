<?php
session_start();
include("conexao.php");

$logado = isset($_SESSION["id"]);
$admin = $logado && isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin";
$usuario = $logado ? $_SESSION["usuario"] : "";

/* =========================
   ESTATÍSTICAS
========================= */

$total_lutadores = 0;
$total_lutas = 0;
$total_usuarios = 0;
$total_produtos = 0;

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM lutadores");
if ($resultado) {
    $total_lutadores = $resultado->fetch_assoc()["total"];
}

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM lutas");
if ($resultado) {
    $total_lutas = $resultado->fetch_assoc()["total"];
}

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM usuarios");
if ($resultado) {
    $total_usuarios = $resultado->fetch_assoc()["total"];
}

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM produtos");
if ($resultado) {
    $total_produtos = $resultado->fetch_assoc()["total"];
}

/* =========================
   PRÓXIMA LUTA
========================= */

$sql_proxima = "SELECT l.*,
                       a.nome AS nome1,
                       a.foto AS foto1,
                       a.categoria AS categoria1,
                       b.nome AS nome2,
                       b.foto AS foto2,
                       b.categoria AS categoria2
                FROM lutas l
                LEFT JOIN lutadores a ON l.lutador1 = a.nome
                LEFT JOIN lutadores b ON l.lutador2 = b.nome
                WHERE l.data_luta >= CURDATE()
                ORDER BY l.data_luta ASC, l.id ASC
                LIMIT 1";

$proxima = $conexao->query($sql_proxima);

/* =========================
   ÚLTIMAS LUTAS
========================= */

$sql_lutas = "SELECT l.*,
                     a.nome AS nome1,
                     a.foto AS foto1,
                     b.nome AS nome2,
                     b.foto AS foto2
              FROM lutas l
              LEFT JOIN lutadores a ON l.lutador1 = a.nome
              LEFT JOIN lutadores b ON l.lutador2 = b.nome
              ORDER BY l.data_luta DESC, l.id DESC
              LIMIT 3";

$ultimas_lutas = $conexao->query($sql_lutas);

/* =========================
   RANKING
========================= */

$sql_ranking = "SELECT l.*,

    (
        SELECT COUNT(*)
        FROM lutas lu
        WHERE
        (
            lu.resultado = 'Vitória do Lutador 1'
            AND lu.lutador1 = l.nome
        )
        OR
        (
            lu.resultado = 'Vitória do Lutador 2'
            AND lu.lutador2 = l.nome
        )
    ) AS vitorias,

    (
        SELECT COUNT(*)
        FROM lutas lu
        WHERE
        (
            lu.resultado = 'Vitória do Lutador 1'
            AND lu.lutador2 = l.nome
        )
        OR
        (
            lu.resultado = 'Vitória do Lutador 2'
            AND lu.lutador1 = l.nome
        )
    ) AS derrotas,

    (
        SELECT COUNT(*)
        FROM lutas lu
        WHERE lu.resultado = 'Empate'
        AND (
            lu.lutador1 = l.nome
            OR lu.lutador2 = l.nome
        )
    ) AS empates

    FROM lutadores l

    ORDER BY vitorias DESC,
             derrotas ASC,
             l.nome ASC

    LIMIT 5";

$ranking = $conexao->query($sql_ranking);

/* =========================
   ÚLTIMOS LUTADORES
========================= */

$sql_lutadores = "SELECT * FROM lutadores
                  ORDER BY id DESC
                  LIMIT 4";

$lutadores = $conexao->query($sql_lutadores);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Boxe Fight | O mundo do boxe</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>

/* =========================
   RESET
========================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #080808;
    color: #fff;
}

a {
    text-decoration: none;
}

/* =========================
   NAVBAR
========================= */

.navbar {
    position: fixed;
    top: 0;
    left: 0;

    width: 100%;
    height: 76px;

    z-index: 1000;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 6%;

    background: rgba(0,0,0,.88);

    border-bottom: 1px solid rgba(225,6,0,.35);

    backdrop-filter: blur(12px);
}

.logo {
    font-size: 28px;
    font-weight: 900;
    color: #fff;
    letter-spacing: 2px;
}

.logo span {
    color: #e10600;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 4px;
}

.nav-links a {
    color: #ddd;

    padding: 11px 14px;

    border-radius: 5px;

    font-size: 14px;
    font-weight: bold;

    transition: .3s;
}

.nav-links a:hover {
    background: #e10600;
    color: #fff;
}

.nav-links .ativo {
    color: #fff;
    background: #e10600;
}

.nav-links .admin-link {
    color: #ff3b35;
}

/* =========================
   HERO
========================= */

.hero {
    min-height: 820px;

    position: relative;

    display: flex;
    align-items: center;

    overflow: hidden;

    background:
        linear-gradient(
            90deg,
            rgba(0,0,0,.96) 0%,
            rgba(0,0,0,.82) 42%,
            rgba(0,0,0,.38) 75%,
            rgba(0,0,0,.72) 100%
        ),
        url("imagens/ringue1.jpg") center/cover;
}

.hero::after {
    content: "";

    position: absolute;

    left: 0;
    right: 0;
    bottom: 0;

    height: 180px;

    background: linear-gradient(
        transparent,
        #080808
    );
}

.hero-conteudo {
    position: relative;
    z-index: 2;

    width: 90%;
    max-width: 1250px;

    margin: auto;

    padding-top: 70px;
}

.tag {
    display: inline-flex;
    align-items: center;
    gap: 10px;

    color: #fff;

    font-size: 13px;
    font-weight: bold;

    letter-spacing: 2px;

    padding: 9px 14px;

    border-left: 4px solid #e10600;

    background: rgba(255,255,255,.06);
}

.tag i {
    color: #e10600;
}

.hero h1 {
    max-width: 850px;

    margin-top: 25px;

    font-size: clamp(55px, 8vw, 105px);

    line-height: .9;

    font-weight: 900;

    text-transform: uppercase;

    letter-spacing: -4px;
}

.hero h1 span {
    color: #e10600;

    display: block;
}

.hero p {
    max-width: 650px;

    margin-top: 30px;

    color: #ccc;

    font-size: 18px;
    line-height: 1.7;
}

.hero-botoes {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;

    margin-top: 35px;
}

.botao {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 10px;

    padding: 15px 24px;

    border-radius: 4px;

    font-weight: 900;

    transition: .3s;
}

.botao-vermelho {
    background: #e10600;
    color: #fff;

    box-shadow: 0 10px 30px rgba(225,6,0,.22);
}

.botao-vermelho:hover {
    background: #b80500;
    transform: translateY(-3px);
}

.botao-outline {
    border: 1px solid #666;
    color: #fff;
    background: rgba(0,0,0,.35);
}

.botao-outline:hover {
    border-color: #e10600;
    background: #e10600;
}

/* =========================
   MINI INFO HERO
========================= */

.hero-info {
    display: flex;
    flex-wrap: wrap;

    gap: 35px;

    margin-top: 55px;
}

.hero-info-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.hero-info-item i {
    color: #e10600;
    font-size: 23px;
}

.hero-info-item strong {
    display: block;
    font-size: 20px;
}

.hero-info-item span {
    color: #888;
    font-size: 12px;
}

/* =========================
   SEÇÃO
========================= */

.secao {
    width: 90%;
    max-width: 1200px;

    margin: 0 auto 100px;
}

.titulo {
    margin-bottom: 35px;
}

.titulo .mini {
    color: #e10600;

    font-size: 12px;
    font-weight: 900;

    letter-spacing: 3px;
}

.titulo h2 {
    margin-top: 8px;

    font-size: 40px;

    text-transform: uppercase;
}

.titulo h2 span {
    color: #e10600;
}

.titulo p {
    color: #888;

    margin-top: 10px;

    line-height: 1.6;
}

/* =========================
   STATS
========================= */

.stats {
    position: relative;
    z-index: 5;

    width: 90%;
    max-width: 1100px;

    margin: -65px auto 100px;

    display: grid;
    grid-template-columns: repeat(4, 1fr);

    background: #111;

    border: 1px solid #292929;

    box-shadow: 0 20px 50px rgba(0,0,0,.5);
}

.stat {
    padding: 30px 20px;

    text-align: center;

    border-right: 1px solid #292929;
}

.stat:last-child {
    border-right: none;
}

.stat i {
    color: #e10600;

    font-size: 24px;

    margin-bottom: 12px;
}

.stat strong {
    display: block;

    font-size: 34px;
}

.stat span {
    color: #777;

    font-size: 13px;
}

/* =========================
   PRÓXIMA LUTA
========================= */

.proxima-box {
    position: relative;

    overflow: hidden;

    display: grid;

    grid-template-columns: 1fr 180px 1fr;

    align-items: center;

    gap: 25px;

    padding: 45px;

    background:
        linear-gradient(
            135deg,
            #191919,
            #0c0c0c
        );

    border: 1px solid #333;

    border-top: 4px solid #e10600;

    box-shadow: 0 20px 50px rgba(0,0,0,.35);
}

.proxima-box::before {
    content: "NEXT FIGHT";

    position: absolute;

    top: 20px;
    right: -35px;

    transform: rotate(45deg);

    background: #e10600;

    padding: 7px 45px;

    font-size: 10px;
    font-weight: 900;

    letter-spacing: 2px;
}

.fighter {
    text-align: center;
}

.fighter img {
    width: 160px;
    height: 160px;

    object-fit: cover;

    border-radius: 50%;

    border: 4px solid #e10600;

    background: #222;

    transition: .3s;
}

.fighter:hover img {
    transform: scale(1.05);

    box-shadow:
        0 0 0 8px rgba(225,6,0,.08),
        0 0 35px rgba(225,6,0,.25);
}

.sem-foto {
    width: 160px;
    height: 160px;

    margin: auto;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    border: 4px solid #e10600;

    background: #222;

    color: #666;

    font-size: 50px;
}

.fighter h3 {
    margin-top: 18px;

    font-size: 25px;
}

.fighter p {
    margin-top: 6px;

    color: #888;
}

.vs {
    text-align: center;
}

.vs strong {
    font-size: 52px;

    font-weight: 900;

    font-style: italic;
}

.vs span {
    display: block;

    margin-top: 10px;

    color: #e10600;

    font-weight: bold;
}

.vs small {
    display: block;

    margin-top: 8px;

    color: #777;
}

/* =========================
   LUTAS
========================= */

.lutas-grid {
    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 20px;
}

.luta-card {
    background: #121212;

    border: 1px solid #292929;

    padding: 25px;

    transition: .3s;
}

.luta-card:hover {
    border-color: #e10600;

    transform: translateY(-6px);

    box-shadow: 0 15px 35px rgba(0,0,0,.4);
}

.luta-data {
    color: #e10600;

    font-size: 12px;
    font-weight: bold;

    margin-bottom: 22px;
}

.luta-versus {
    display: grid;

    grid-template-columns: 1fr 40px 1fr;

    align-items: center;

    gap: 8px;
}

.luta-fighter {
    text-align: center;
}

.luta-fighter img {
    width: 72px;
    height: 72px;

    border-radius: 50%;

    object-fit: cover;

    border: 2px solid #444;
}

.luta-fighter h4 {
    margin-top: 10px;

    font-size: 14px;
}

.luta-vs {
    color: #e10600;

    font-weight: 900;

    text-align: center;
}

.resultado {
    margin-top: 22px;

    padding: 10px;

    background: #0b0b0b;

    border-left: 3px solid #e10600;

    color: #ccc;

    font-size: 13px;

    text-align: center;
}

/* =========================
   RANKING
========================= */

.ranking-area {
    display: grid;

    grid-template-columns: 1.5fr 1fr;

    gap: 30px;

    align-items: start;
}

.ranking-box {
    background: #111;

    border: 1px solid #292929;
}

.ranking-row {
    display: grid;

    grid-template-columns: 55px 1fr 70px 70px;

    align-items: center;

    gap: 10px;

    padding: 15px 20px;

    border-bottom: 1px solid #242424;
}

.ranking-row:last-child {
    border-bottom: none;
}

.ranking-head {
    background: #e10600;

    color: #fff;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;
}

.posicao {
    font-size: 20px;
    font-weight: 900;
}

.posicao:first-child {
    color: #e10600;
}

.ranking-name {
    display: flex;

    align-items: center;

    gap: 12px;

    font-weight: bold;
}

.ranking-name img {
    width: 45px;
    height: 45px;

    border-radius: 50%;

    object-fit: cover;

    border: 2px solid #444;
}

.numero {
    text-align: center;
}

.v {
    color: #4ed66b;
    font-weight: bold;
}

.d {
    color: #e10600;
    font-weight: bold;
}

/* =========================
   LUTADORES
========================= */

.lutadores-grid {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 20px;
}

.lutador-card {
    position: relative;

    overflow: hidden;

    background: #111;

    border: 1px solid #292929;

    transition: .3s;
}

.lutador-card:hover {
    transform: translateY(-7px);

    border-color: #e10600;
}

.lutador-card img {
    width: 100%;
    height: 280px;

    object-fit: cover;

    display: block;
}

.lutador-info {
    padding: 18px;
}

.lutador-info h3 {
    font-size: 19px;
}

.lutador-info p {
    color: #888;

    margin-top: 6px;

    font-size: 13px;
}

.ver-perfil {
    display: inline-block;

    margin-top: 14px;

    color: #e10600;

    font-size: 12px;

    font-weight: bold;
}

/* =========================
   ARENA CTA
========================= */

.arena-cta {
    position: relative;

    overflow: hidden;

    padding: 70px 35px;

    text-align: center;

    background:
        linear-gradient(
            rgba(225,6,0,.88),
            rgba(120,0,0,.92)
        ),
        url("imagens/boxe2.jpg") center/cover;
}

.arena-cta h2 {
    font-size: 48px;

    text-transform: uppercase;

    font-weight: 900;
}

.arena-cta p {
    margin-top: 12px;

    color: #eee;
}

/* =========================
   LOJA
========================= */

.loja-box {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 30px;

    padding: 45px;

    background: #111;

    border: 1px solid #292929;

    align-items: center;
}

.loja-text h2 {
    font-size: 36px;

    text-transform: uppercase;
}

.loja-text h2 span {
    color: #e10600;
}

.loja-text p {
    color: #999;

    line-height: 1.7;

    margin-top: 15px;
}

.loja-itens {
    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 12px;

    margin-top: 25px;
}

.loja-item {
    padding: 15px;

    background: #191919;

    color: #bbb;

    font-size: 13px;
}

.loja-item i {
    color: #e10600;

    margin-right: 7px;
}

/* =========================
   FOOTER
========================= */

footer {
    margin-top: 100px;

    padding: 45px 20px;

    text-align: center;

    background: #000;

    border-top: 1px solid #292929;

    color: #666;
}

footer strong {
    color: #e10600;
}

.footer-links {
    margin-top: 18px;

    display: flex;

    justify-content: center;

    gap: 22px;

    flex-wrap: wrap;
}

.footer-links a {
    color: #888;

    font-size: 13px;
}

.footer-links a:hover {
    color: #e10600;
}

/* =========================
   RESPONSIVO
========================= */

@media (max-width: 1000px) {

    .nav-links a {
        padding: 8px;
        font-size: 12px;
    }

    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .stat:nth-child(2) {
        border-right: none;
    }

    .lutas-grid {
        grid-template-columns: 1fr 1fr;
    }

    .lutadores-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .ranking-area {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 750px) {

    .navbar {
        position: relative;

        height: auto;

        padding: 18px;

        flex-direction: column;

        gap: 15px;
    }

    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
    }

    .hero {
        min-height: 700px;
    }

    .hero h1 {
        letter-spacing: -2px;
    }

    .proxima-box {
        grid-template-columns: 1fr;

        padding: 35px 20px;
    }

    .vs {
        order: 2;
    }

    .fighter:first-child {
        order: 1;
    }

    .fighter:last-child {
        order: 3;
    }

    .lutas-grid {
        grid-template-columns: 1fr;
    }

    .loja-box {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 550px) {

    .stats {
        grid-template-columns: 1fr;
    }

    .stat {
        border-right: none;

        border-bottom: 1px solid #292929;
    }

    .lutadores-grid {
        grid-template-columns: 1fr;
    }

    .titulo h2 {
        font-size: 30px;
    }

    .arena-cta h2 {
        font-size: 34px;
    }

    .hero-info {
        gap: 20px;
    }

    .hero-info-item {
        width: 100%;
    }

    .loja-itens {
        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar">

    <a href="index.php" class="logo">
        BOXE<span>FIGHT</span>
    </a>

    <div class="nav-links">

        <a href="index.php" class="ativo">
            <i class="fas fa-home"></i>
            Início
        </a>

        <a href="arena.php">
            <i class="fas fa-fist-raised"></i>
            Arena
        </a>

        <a href="lutadores.php">
            <i class="fas fa-user"></i>
            Lutadores
        </a>

        <a href="lutas.php">
            <i class="fas fa-trophy"></i>
            Lutas
        </a>

        <a href="loja.php">
            <i class="fas fa-shopping-cart"></i>
            Loja
        </a>

        <?php if ($logado) { ?>

            <a href="painel.php">
                <i class="fas fa-user-circle"></i>
                Painel
            </a>

            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Sair
            </a>

        <?php } else { ?>

            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i>
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

            <i class="fas fa-bolt"></i>

            BOXE FIGHT • A ARENA COMEÇA AQUI

        </div>


        <h1>

            VIVA O

            <span>BOXE.</span>

        </h1>


        <p>

            Acompanhe lutadores, resultados, confrontos,
            rankings e tudo que acontece dentro da
            <strong>Boxe Fight Arena.</strong>

        </p>


        <div class="hero-botoes">

            <a href="arena.php" class="botao botao-vermelho">

                <i class="fas fa-fist-raised"></i>

                ENTRAR NA ARENA

            </a>


            <a href="lutadores.php" class="botao botao-outline">

                <i class="fas fa-users"></i>

                VER LUTADORES

            </a>

        </div>


        <div class="hero-info">

            <div class="hero-info-item">

                <i class="fas fa-user-friends"></i>

                <div>

                    <strong>
                        <?php echo $total_lutadores; ?>
                    </strong>

                    <span>
                        LUTADORES
                    </span>

                </div>

            </div>


            <div class="hero-info-item">

                <i class="fas fa-trophy"></i>

                <div>

                    <strong>
                        <?php echo $total_lutas; ?>
                    </strong>

                    <span>
                        LUTAS
                    </span>

                </div>

            </div>


            <div class="hero-info-item">

                <i class="fas fa-shopping-bag"></i>

                <div>

                    <strong>
                        <?php echo $total_produtos; ?>
                    </strong>

                    <span>
                        PRODUTOS
                    </span>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     ESTATÍSTICAS
========================= -->

<section class="stats">

    <div class="stat">

        <i class="fas fa-fist-raised"></i>

        <strong>
            <?php echo $total_lutadores; ?>
        </strong>

        <span>
            LUTADORES
        </span>

    </div>


    <div class="stat">

        <i class="fas fa-trophy"></i>

        <strong>
            <?php echo $total_lutas; ?>
        </strong>

        <span>
            LUTAS REGISTRADAS
        </span>

    </div>


    <div class="stat">

        <i class="fas fa-users"></i>

        <strong>
            <?php echo $total_usuarios; ?>
        </strong>

        <span>
            USUÁRIOS
        </span>

    </div>


    <div class="stat">

        <i class="fas fa-box-open"></i>

        <strong>
            <?php echo $total_produtos; ?>
        </strong>

        <span>
            PRODUTOS
        </span>

    </div>

</section>


<!-- =========================
     PRÓXIMA LUTA
========================= -->

<section class="secao">

    <div class="titulo">

        <span class="mini">
            AO VIVO NA ARENA
        </span>

        <h2>
            PRÓXIMA <span>LUTA</span>
        </h2>

        <p>
            O próximo confronto já está marcado.
        </p>

    </div>


    <?php if ($proxima && $proxima->num_rows > 0) { ?>

        <?php $luta = $proxima->fetch_assoc(); ?>


        <div class="proxima-box">


            <!-- LUTADOR 1 -->

            <div class="fighter">

                <?php if (!empty($luta["foto1"])) { ?>

                    <img
                    src="fotos/<?php echo htmlspecialchars($luta["foto1"]); ?>"
                    alt="Lutador">

                <?php } else { ?>

                    <div class="sem-foto">

                        <i class="fas fa-user"></i>

                    </div>

                <?php } ?>


                <h3>
                    <?php
                    echo htmlspecialchars(
                        $luta["nome1"] ?? $luta["lutador1"]
                    );
                    ?>
                </h3>


                <p>
                    <?php
                    echo htmlspecialchars(
                        $luta["categoria1"] ?? "Boxe"
                    );
                    ?>
                </p>

            </div>


            <!-- VS -->

            <div class="vs">

                <strong>
                    VS
                </strong>

                <span>

                    <?php
                    echo date(
                        "d/m/Y",
                        strtotime($luta["data_luta"])
                    );
                    ?>

                </span>

                <small>
                    <?php echo htmlspecialchars($luta["metodo"]); ?>
                </small>

            </div>


            <!-- LUTADOR 2 -->

            <div class="fighter">

                <?php if (!empty($luta["foto2"])) { ?>

                    <img
                    src="fotos/<?php echo htmlspecialchars($luta["foto2"]); ?>"
                    alt="Lutador">

                <?php } else { ?>

                    <div class="sem-foto">

                        <i class="fas fa-user"></i>

                    </div>

                <?php } ?>


                <h3>
                    <?php
                    echo htmlspecialchars(
                        $luta["nome2"] ?? $luta["lutador2"]
                    );
                    ?>
                </h3>


                <p>
                    <?php
                    echo htmlspecialchars(
                        $luta["categoria2"] ?? "Boxe"
                    );
                    ?>
                </p>

            </div>


        </div>

    <?php } else { ?>

        <div class="proxima-box">

            <div style="
                grid-column:1/-1;
                text-align:center;
                padding:40px;
            ">

                <i
                class="fas fa-calendar-times"
                style="
                    color:#e10600;
                    font-size:45px;
                    margin-bottom:20px;
                ">
                </i>

                <h3>
                    Nenhuma próxima luta marcada.
                </h3>

                <p style="
                    color:#777;
                    margin-top:10px;
                ">
                    A arena está aguardando seu próximo confronto.
                </p>

                <?php if ($admin) { ?>

                    <a
                    href="cadastrar_luta.php"
                    class="botao botao-vermelho"
                    style="margin-top:20px;">

                        <i class="fas fa-plus"></i>

                        Cadastrar luta

                    </a>

                <?php } ?>

            </div>

        </div>

    <?php } ?>

</section>


<!-- =========================
     ÚLTIMAS LUTAS
========================= -->

<section class="secao">

    <div class="titulo">

        <span class="mini">
            HISTÓRICO
        </span>

        <h2>
            ÚLTIMOS <span>CONFRONTOS</span>
        </h2>

        <p>
            Veja o que aconteceu recentemente dentro do ringue.
        </p>

    </div>


    <div class="lutas-grid">

        <?php if ($ultimas_lutas && $ultimas_lutas->num_rows > 0) { ?>

            <?php while ($luta = $ultimas_lutas->fetch_assoc()) { ?>

                <?php

                $nome1 = !empty($luta["nome1"])
                    ? $luta["nome1"]
                    : $luta["lutador1"];

                $nome2 = !empty($luta["nome2"])
                    ? $luta["nome2"]
                    : $luta["lutador2"];

                if ($luta["resultado"] == "Vitória do Lutador 1") {

                    $resultado = "Vitória de " . $nome1;

                } elseif ($luta["resultado"] == "Vitória do Lutador 2") {

                    $resultado = "Vitória de " . $nome2;

                } elseif ($luta["resultado"] == "Empate") {

                    $resultado = "Empate";

                } else {

                    $resultado = "Sem resultado";

                }

                ?>


                <div class="luta-card">

                    <div class="luta-data">

                        <i class="far fa-calendar"></i>

                        <?php
                        echo date(
                            "d/m/Y",
                            strtotime($luta["data_luta"])
                        );
                        ?>

                    </div>


                    <div class="luta-versus">


                        <div class="luta-fighter">

                            <?php if (!empty($luta["foto1"])) { ?>

                                <img
                                src="fotos/<?php echo htmlspecialchars($luta["foto1"]); ?>"
                                alt="Lutador">

                            <?php } else { ?>

                                <div
                                style="
                                    width:72px;
                                    height:72px;
                                    margin:auto;
                                    border-radius:50%;
                                    background:#222;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#666;
                                    font-size:25px;
                                ">

                                    <i class="fas fa-user"></i>

                                </div>

                            <?php } ?>

                            <h4>
                                <?php echo htmlspecialchars($nome1); ?>
                            </h4>

                        </div>


                        <div class="luta-vs">
                            VS
                        </div>


                        <div class="luta-fighter">

                            <?php if (!empty($luta["foto2"])) { ?>

                                <img
                                src="fotos/<?php echo htmlspecialchars($luta["foto2"]); ?>"
                                alt="Lutador">

                            <?php } else { ?>

                                <div
                                style="
                                    width:72px;
                                    height:72px;
                                    margin:auto;
                                    border-radius:50%;
                                    background:#222;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    color:#666;
                                    font-size:25px;
                                ">

                                    <i class="fas fa-user"></i>

                                </div>

                            <?php } ?>

                            <h4>
                                <?php echo htmlspecialchars($nome2); ?>
                            </h4>

                        </div>

                    </div>


                    <div class="resultado">

                        <?php echo htmlspecialchars($resultado); ?>

                    </div>

                </div>


            <?php } ?>

        <?php } else { ?>

            <div style="
                grid-column:1/-1;
                text-align:center;
                padding:50px;
                background:#111;
                border:1px solid #292929;
                color:#777;
            ">

                Nenhuma luta registrada ainda.

            </div>

        <?php } ?>

    </div>


    <div style="text-align:center;margin-top:30px;">

        <a href="lutas.php" class="botao botao-outline">

            <i class="fas fa-trophy"></i>

            VER TODAS AS LUTAS

        </a>

    </div>

</section>


<!-- =========================
     RANKING
========================= -->

<section class="secao">

    <div class="titulo">

        <span class="mini">
            CLASSIFICAÇÃO
        </span>

        <h2>
            TOP <span>LUTADORES</span>
        </h2>

        <p>
            Ranking baseado nas vitórias registradas na Boxe Fight.
        </p>

    </div>


    <div class="ranking-area">


        <div class="ranking-box">

            <div class="ranking-row ranking-head">

                <div>#</div>

                <div>Lutador</div>

                <div class="numero">
                    V
                </div>

                <div class="numero">
                    D
                </div>

            </div>


            <?php

            $posicao = 1;

            if ($ranking && $ranking->num_rows > 0) {

                while ($lutador = $ranking->fetch_assoc()) {

            ?>

                <div class="ranking-row">

                    <div class="posicao">

                        <?php echo $posicao; ?>º

                    </div>


                    <div class="ranking-name">

                        <?php if (!empty($lutador["foto"])) { ?>

                            <img
                            src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                            alt="Lutador">

                        <?php } ?>

                        <span>
                            <?php echo htmlspecialchars($lutador["nome"]); ?>
                        </span>

                    </div>


                    <div class="numero v">

                        <?php
                        echo $lutador["vitorias"];
                        ?>

                    </div>


                    <div class="numero d">

                        <?php
                        echo $lutador["derrotas"];
                        ?>

                    </div>

                </div>


            <?php

                    $posicao++;

                }

            } else {

            ?>

                <div style="
                    padding:40px;
                    text-align:center;
                    color:#777;
                ">

                    Nenhum lutador cadastrado.

                </div>

            <?php } ?>

        </div>


        <!-- CARD ARENA -->

        <div class="arena-cta">

            <i
            class="fas fa-fist-raised"
            style="
                font-size:50px;
                margin-bottom:20px;
            ">
            </i>


            <h2>
                BOXE<br>ARENA
            </h2>


            <p>
                Veja rankings, confrontos,
                lutadores e muito mais.
            </p>


            <a
            href="arena.php"
            class="botao botao-outline"
            style="
                margin-top:25px;
                border-color:#fff;
            ">

                <i class="fas fa-arrow-right"></i>

                ABRIR ARENA

            </a>

        </div>


    </div>

</section>


<!-- =========================
     LUTADORES
========================= -->

<section class="secao">

    <div class="titulo">

        <span class="mini">
            ROSTER
        </span>

        <h2>
            NOVOS <span>LUTADORES</span>
        </h2>

        <p>
            Conheça alguns dos atletas cadastrados na plataforma.
        </p>

    </div>


    <div class="lutadores-grid">

        <?php if ($lutadores && $lutadores->num_rows > 0) { ?>

            <?php while ($lutador = $lutadores->fetch_assoc()) { ?>

                <div class="lutador-card">

                    <?php if (!empty($lutador["foto"])) { ?>

                        <img
                        src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                        alt="<?php echo htmlspecialchars($lutador["nome"]); ?>">

                    <?php } else { ?>

                        <div style="
                            height:280px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:#222;
                            color:#666;
                            font-size:60px;
                        ">

                            <i class="fas fa-user"></i>

                        </div>

                    <?php } ?>


                    <div class="lutador-info">

                        <h3>
                            <?php echo htmlspecialchars($lutador["nome"]); ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($lutador["categoria"]); ?>
                        </p>

                        <a
                        href="lutadores.php"
                        class="ver-perfil">

                            VER LUTADORES
                            <i class="fas fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div style="
                grid-column:1/-1;
                text-align:center;
                color:#777;
                padding:40px;
            ">

                Nenhum lutador cadastrado.

            </div>

        <?php } ?>

    </div>


    <div style="text-align:center;margin-top:35px;">

        <a href="lutadores.php" class="botao botao-vermelho">

            <i class="fas fa-users"></i>

            VER TODOS OS LUTADORES

        </a>

    </div>

</section>


<!-- =========================
     LOJA
========================= -->

<section class="secao">

    <div class="loja-box">

        <div class="loja-text">

            <span class="titulo .mini">
                EQUIPAMENTOS
            </span>


            <h2>
                ENTRE NO RINGUE
                <span>PREPARADO.</span>
            </h2>


            <p>
                Encontre equipamentos e acessórios
                para seus treinos dentro da loja
                oficial Boxe Fight.
            </p>


            <div class="loja-itens">

                <div class="loja-item">

                    <i class="fas fa-hand-rock"></i>

                    Luvas

                </div>


                <div class="loja-item">

                    <i class="fas fa-shield-alt"></i>

                    Proteção

                </div>


                <div class="loja-item">

                    <i class="fas fa-running"></i>

                    Treino

                </div>


                <div class="loja-item">

                    <i class="fas fa-tshirt"></i>

                    Roupas

                </div>

            </div>


            <a
            href="loja.php"
            class="botao botao-vermelho"
            style="margin-top:25px;">

                <i class="fas fa-shopping-cart"></i>

                IR PARA A LOJA

            </a>

        </div>


        <div
        style="
            min-height:350px;
            background:
            linear-gradient(
                rgba(0,0,0,.2),
                rgba(0,0,0,.7)
            ),
            url('imagens/boxe3.jpg') center/cover;
            border:1px solid #333;
        ">
        </div>

    </div>

</section>


<!-- =========================
     CTA FINAL
========================= -->

<section class="secao">

    <div class="arena-cta">

        <i class="fas fa-fist-raised"></i>


        <h2>
            O RINGUE ESTÁ ESPERANDO.
        </h2>


        <p>
            Acompanhe a Arena Boxe Fight
            e não perca nenhum confronto.
        </p>


        <a
        href="arena.php"
        class="botao botao-outline"
        style="
            margin-top:25px;
            border-color:#fff;
        ">

            ENTRAR NA ARENA

            <i class="fas fa-arrow-right"></i>

        </a>

    </div>

</section>


<!-- =========================
     FOOTER
========================= -->

<footer>

    <p>

        &copy; <?php echo date("Y"); ?>

        <strong>Boxe Fight</strong>.

        Todos os direitos reservados.

    </p>


    <div class="footer-links">

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

    </div>

</footer>


</body>

</html>

<?php
$conexao->close();
?>
