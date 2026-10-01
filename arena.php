<?php
session_start();
include("conexao.php");

$logado = isset($_SESSION["id"]);
$admin = $logado && isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin";
$usuario = $logado ? $_SESSION["usuario"] : "";

/* =========================
   PRÓXIMA LUTA
========================= */

$sql_proxima = "SELECT l.*,
                       a.nome AS nome_lutador1,
                       a.foto AS foto_lutador1,
                       a.categoria AS categoria_lutador1,
                       b.nome AS nome_lutador2,
                       b.foto AS foto_lutador2,
                       b.categoria AS categoria_lutador2
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

$sql_ultimas = "SELECT l.*,
                       a.nome AS nome_lutador1,
                       a.foto AS foto_lutador1,
                       b.nome AS nome_lutador2,
                       b.foto AS foto_lutador2
                FROM lutas l
                LEFT JOIN lutadores a ON l.lutador1 = a.nome
                LEFT JOIN lutadores b ON l.lutador2 = b.nome
                WHERE l.data_luta < CURDATE()
                ORDER BY l.data_luta DESC, l.id DESC
                LIMIT 6";

$ultimas = $conexao->query($sql_ultimas);

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
    ) AS vitorias_calculadas,

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
    ) AS derrotas_calculadas,

    (
        SELECT COUNT(*)
        FROM lutas lu
        WHERE lu.resultado = 'Empate'
        AND (
            lu.lutador1 = l.nome
            OR lu.lutador2 = l.nome
        )
    ) AS empates_calculados

    FROM lutadores l
    ORDER BY vitorias_calculadas DESC,
             derrotas_calculadas ASC,
             l.nome ASC
    LIMIT 10";

$ranking = $conexao->query($sql_ranking);

/* =========================
   TOTAL DE LUTADORES
========================= */

$sql_total = "SELECT COUNT(*) AS total FROM lutadores";
$resultado_total = $conexao->query($sql_total);
$total_lutadores = $resultado_total->fetch_assoc()["total"];

/* =========================
   TOTAL DE LUTAS
========================= */

$sql_total_lutas = "SELECT COUNT(*) AS total FROM lutas";
$resultado_total_lutas = $conexao->query($sql_total_lutas);
$total_lutas = $resultado_total_lutas->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Arena | Boxe Fight</title>

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
    background: #0b0b0b;
    color: #fff;
}

/* =========================
   NAVBAR
========================= */

.navbar {
    background: #000;
    border-bottom: 2px solid #e10600;
    height: 75px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 6%;
}

.logo {
    color: #fff;
    text-decoration: none;
    font-size: 27px;
    font-weight: 900;
    letter-spacing: 1px;
}

.logo span {
    color: #e10600;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-links a {
    color: #ddd;
    text-decoration: none;
    padding: 11px 14px;
    border-radius: 5px;
    font-weight: bold;
    transition: 0.3s;
}

.nav-links a:hover {
    background: #e10600;
    color: #fff;
}

.nav-links .ativo {
    background: #e10600;
    color: #fff;
}

.nav-admin {
    color: #e10600 !important;
}

/* =========================
   HERO ARENA
========================= */

.arena-hero {
    min-height: 380px;

    display: flex;
    align-items: center;
    justify-content: center;

    text-align: center;

    padding: 60px 20px;

    background:
        linear-gradient(rgba(0,0,0,.78), rgba(0,0,0,.9)),
        url("imagens/boxe1.jpg") center/cover;
}

.arena-hero h1 {
    font-size: 65px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.arena-hero h1 span {
    color: #e10600;
}

.arena-hero p {
    color: #ccc;
    font-size: 18px;
    margin-top: 15px;
}

.linha-vermelha {
    width: 80px;
    height: 5px;
    background: #e10600;
    margin: 25px auto;
}

/* =========================
   ESTATÍSTICAS
========================= */

.estatisticas {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;

    max-width: 900px;
    margin: -40px auto 60px;

    position: relative;
    padding: 0 20px;
}

.stat {
    background: #171717;
    border: 1px solid #333;
    border-top: 3px solid #e10600;

    padding: 25px;
    text-align: center;

    box-shadow: 0 8px 25px rgba(0,0,0,.4);
}

.stat i {
    color: #e10600;
    font-size: 27px;
    margin-bottom: 10px;
}

.stat h2 {
    font-size: 32px;
}

.stat p {
    color: #999;
    margin-top: 5px;
}

/* =========================
   CONTAINER
========================= */

.container {
    width: 90%;
    max-width: 1200px;
    margin: auto;
}

.titulo-secao {
    margin-bottom: 30px;
}

.titulo-secao h2 {
    font-size: 31px;
    text-transform: uppercase;
}

.titulo-secao h2 span {
    color: #e10600;
}

.titulo-secao p {
    color: #999;
    margin-top: 8px;
}

/* =========================
   PRÓXIMA LUTA
========================= */

.proxima-luta {
    margin-bottom: 75px;
}

.luta-destaque {
    background: linear-gradient(135deg, #181818, #0d0d0d);
    border: 1px solid #333;
    border-top: 4px solid #e10600;

    padding: 35px;

    display: grid;
    grid-template-columns: 1fr 130px 1fr;

    align-items: center;
    gap: 25px;

    box-shadow: 0 10px 35px rgba(0,0,0,.4);
}

.lutador-arena {
    text-align: center;
}

.lutador-arena img {
    width: 150px;
    height: 150px;

    object-fit: cover;

    border-radius: 50%;

    border: 4px solid #e10600;

    background: #222;
}

.sem-foto {
    width: 150px;
    height: 150px;

    margin: auto;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #222;
    border: 4px solid #e10600;

    font-size: 55px;
    color: #777;
}

.lutador-arena h3 {
    font-size: 25px;
    margin-top: 15px;
}

.lutador-arena p {
    color: #999;
    margin-top: 5px;
}

.vs {
    text-align: center;
}

.vs span {
    display: block;

    color: #fff;

    font-size: 40px;
    font-weight: 900;
}

.data-luta {
    margin-top: 15px;
    color: #e10600;
    font-weight: bold;
}

.sem-luta {
    background: #171717;
    border: 1px solid #333;
    padding: 50px;
    text-align: center;
    color: #999;
}

.sem-luta i {
    font-size: 45px;
    color: #e10600;
    margin-bottom: 15px;
}

/* =========================
   ÚLTIMAS LUTAS
========================= */

.ultimas {
    margin-bottom: 75px;
}

.lutas-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.luta-card {
    background: #171717;
    border: 1px solid #303030;
    padding: 25px;

    transition: .3s;
}

.luta-card:hover {
    transform: translateY(-5px);
    border-color: #e10600;
}

.luta-data {
    color: #e10600;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 18px;
}

.luta-versus {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.luta-nome {
    width: 42%;
    text-align: center;
    font-weight: bold;
}

.luta-nome span {
    display: block;
    margin-top: 10px;
}

.luta-nome img {
    width: 70px;
    height: 70px;

    border-radius: 50%;
    object-fit: cover;

    border: 2px solid #555;
}

.mini-sem-foto {
    width: 70px;
    height: 70px;

    margin: auto;

    border-radius: 50%;

    background: #292929;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #777;
    font-size: 25px;
}

.luta-vs {
    color: #e10600;
    font-weight: 900;
}

.resultado {
    text-align: center;
    margin-top: 20px;

    padding: 10px;

    background: #0e0e0e;

    color: #fff;
    font-weight: bold;
}

.resultado.empate {
    color: #bbb;
}

/* =========================
   RANKING
========================= */

.ranking {
    margin-bottom: 80px;
}

.tabela-ranking {
    background: #151515;
    border: 1px solid #333;
    overflow-x: auto;
}

.linha-ranking {
    display: grid;
    grid-template-columns: 70px 1fr 110px 110px 110px;

    align-items: center;

    min-width: 650px;

    padding: 17px 22px;

    border-bottom: 1px solid #292929;
}

.linha-ranking:last-child {
    border-bottom: none;
}

.cabecalho {
    background: #e10600;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 13px;
}

.posicao {
    font-size: 21px;
    font-weight: 900;
}

.primeiro {
    color: #e10600;
}

.nome-ranking {
    display: flex;
    align-items: center;
    gap: 15px;
    font-weight: bold;
}

.nome-ranking img {
    width: 48px;
    height: 48px;

    border-radius: 50%;

    object-fit: cover;

    border: 2px solid #555;
}

.ranking-numero {
    text-align: center;
}

.vitoria {
    color: #55d66b;
    font-weight: bold;
}

.derrota {
    color: #e10600;
    font-weight: bold;
}

.empate {
    color: #bbb;
    font-weight: bold;
}

/* =========================
   CTA
========================= */

.cta {
    background: linear-gradient(135deg, #e10600, #870300);

    padding: 55px 25px;

    text-align: center;

    margin-bottom: 80px;
}

.cta h2 {
    font-size: 34px;
    text-transform: uppercase;
}

.cta p {
    margin-top: 10px;
    color: #eee;
}

.botao {
    display: inline-block;

    margin-top: 25px;

    background: #000;
    color: #fff;

    text-decoration: none;

    padding: 14px 25px;

    border-radius: 5px;

    font-weight: bold;

    transition: .3s;
}

.botao:hover {
    background: #fff;
    color: #000;
}

/* =========================
   FOOTER
========================= */

footer {
    background: #000;

    border-top: 1px solid #333;

    text-align: center;

    padding: 30px;

    color: #777;
}

footer strong {
    color: #e10600;
}

/* =========================
   RESPONSIVO
========================= */

@media (max-width: 900px) {

    .nav-links {
        gap: 2px;
    }

    .nav-links a {
        padding: 9px;
        font-size: 13px;
    }

    .arena-hero h1 {
        font-size: 45px;
    }

    .lutas-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .luta-destaque {
        grid-template-columns: 1fr;
    }

    .vs {
        order: 2;
    }

    .lutador-arena:first-child {
        order: 1;
    }

    .lutador-arena:last-child {
        order: 3;
    }
}

@media (max-width: 600px) {

    .navbar {
        height: auto;
        padding: 18px;
        flex-direction: column;
        gap: 15px;
    }

    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
    }

    .arena-hero {
        min-height: 300px;
    }

    .arena-hero h1 {
        font-size: 36px;
    }

    .estatisticas {
        grid-template-columns: 1fr;
        margin-top: 25px;
    }

    .lutas-grid {
        grid-template-columns: 1fr;
    }

    .luta-destaque {
        padding: 25px 15px;
    }

    .lutador-arena img,
    .sem-foto {
        width: 120px;
        height: 120px;
    }

    .lutador-arena h3 {
        font-size: 20px;
    }

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

    <a href="index.php" class="logo">
        BOXE<span>FIGHT</span>
    </a>

    <div class="nav-links">

        <a href="index.php">
            <i class="fas fa-home"></i> Início
        </a>

        <a href="arena.php" class="ativo">
            <i class="fas fa-fist-raised"></i> Arena
        </a>

        <a href="lutadores.php">
            <i class="fas fa-user"></i> Lutadores
        </a>

        <a href="lutas.php">
            <i class="fas fa-trophy"></i> Lutas
        </a>

        <a href="loja.php">
            <i class="fas fa-shopping-cart"></i> Loja
        </a>

        <?php if ($logado) { ?>

            <a href="painel.php">
                <i class="fas fa-user-circle"></i> Painel
            </a>

            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>

        <?php } else { ?>

            <a href="login.php">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </a>

        <?php } ?>

    </div>

</nav>


<!-- HERO -->

<section class="arena-hero">

    <div>

        <h1>
            BOXE <span>ARENA</span>
        </h1>

        <div class="linha-vermelha"></div>

        <p>
            Onde os maiores confrontos acontecem.
        </p>

    </div>

</section>


<!-- ESTATÍSTICAS -->

<section class="estatisticas">

    <div class="stat">

        <i class="fas fa-users"></i>

        <h2>
            <?php echo $total_lutadores; ?>
        </h2>

        <p>Lutadores cadastrados</p>

    </div>

    <div class="stat">

        <i class="fas fa-bolt"></i>

        <h2>
            <?php echo $total_lutas; ?>
        </h2>

        <p>Lutas registradas</p>

    </div>

</section>


<!-- PRÓXIMA LUTA -->

<section class="container proxima-luta">

    <div class="titulo-secao">

        <h2>
            PRÓXIMA <span>LUTA</span>
        </h2>

        <p>
            O próximo confronto marcado na Boxe Fight.
        </p>

    </div>

    <?php if ($proxima && $proxima->num_rows > 0) { ?>

        <?php $luta = $proxima->fetch_assoc(); ?>

        <div class="luta-destaque">

            <!-- LUTADOR 1 -->

            <div class="lutador-arena">

                <?php if (!empty($luta["foto_lutador1"])) { ?>

                    <img
                    src="fotos/<?php echo htmlspecialchars($luta["foto_lutador1"]); ?>"
                    alt="Foto do lutador">

                <?php } else { ?>

                    <div class="sem-foto">
                        <i class="fas fa-user"></i>
                    </div>

                <?php } ?>

                <h3>
                    <?php echo htmlspecialchars($luta["nome_lutador1"] ?? $luta["lutador1"]); ?>
                </h3>

                <p>
                    <?php echo htmlspecialchars($luta["categoria_lutador1"] ?? "Boxe"); ?>
                </p>

            </div>


            <!-- VS -->

            <div class="vs">

                <span>VS</span>

                <div class="data-luta">

                    <?php
                    echo date(
                        "d/m/Y",
                        strtotime($luta["data_luta"])
                    );
                    ?>

                </div>

            </div>


            <!-- LUTADOR 2 -->

            <div class="lutador-arena">

                <?php if (!empty($luta["foto_lutador2"])) { ?>

                    <img
                    src="fotos/<?php echo htmlspecialchars($luta["foto_lutador2"]); ?>"
                    alt="Foto do lutador">

                <?php } else { ?>

                    <div class="sem-foto">
                        <i class="fas fa-user"></i>
                    </div>

                <?php } ?>

                <h3>
                    <?php echo htmlspecialchars($luta["nome_lutador2"] ?? $luta["lutador2"]); ?>
                </h3>

                <p>
                    <?php echo htmlspecialchars($luta["categoria_lutador2"] ?? "Boxe"); ?>
                </p>

            </div>

        </div>

    <?php } else { ?>

        <div class="sem-luta">

            <i class="fas fa-calendar-times"></i>

            <h3>Nenhuma próxima luta marcada</h3>

            <p>
                Ainda não existem lutas futuras cadastradas.
            </p>

            <?php if ($admin) { ?>

                <a href="cadastrar_luta.php" class="botao">
                    <i class="fas fa-plus"></i>
                    Cadastrar luta
                </a>

            <?php } ?>

        </div>

    <?php } ?>

</section>


<!-- ÚLTIMAS LUTAS -->

<section class="container ultimas">

    <div class="titulo-secao">

        <h2>
            ÚLTIMOS <span>CONFRONTOS</span>
        </h2>

        <p>
            Veja os resultados mais recentes da arena.
        </p>

    </div>


    <div class="lutas-grid">

        <?php if ($ultimas && $ultimas->num_rows > 0) { ?>

            <?php while ($luta = $ultimas->fetch_assoc()) { ?>

                <?php

                $nome1 = !empty($luta["nome_lutador1"])
                    ? $luta["nome_lutador1"]
                    : $luta["lutador1"];

                $nome2 = !empty($luta["nome_lutador2"])
                    ? $luta["nome_lutador2"]
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

                        <!-- LUTADOR 1 -->

                        <div class="luta-nome">

                            <?php if (!empty($luta["foto_lutador1"])) { ?>

                                <img
                                src="fotos/<?php echo htmlspecialchars($luta["foto_lutador1"]); ?>"
                                alt="Lutador">

                            <?php } else { ?>

                                <div class="mini-sem-foto">
                                    <i class="fas fa-user"></i>
                                </div>

                            <?php } ?>

                            <span>
                                <?php echo htmlspecialchars($nome1); ?>
                            </span>

                        </div>


                        <div class="luta-vs">
                            VS
                        </div>


                        <!-- LUTADOR 2 -->

                        <div class="luta-nome">

                            <?php if (!empty($luta["foto_lutador2"])) { ?>

                                <img
                                src="fotos/<?php echo htmlspecialchars($luta["foto_lutador2"]); ?>"
                                alt="Lutador">

                            <?php } else { ?>

                                <div class="mini-sem-foto">
                                    <i class="fas fa-user"></i>
                                </div>

                            <?php } ?>

                            <span>
                                <?php echo htmlspecialchars($nome2); ?>
                            </span>

                        </div>

                    </div>


                    <div class="resultado">

                        <?php echo htmlspecialchars($resultado); ?>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="sem-luta">

                <i class="fas fa-boxing-glove"></i>

                <h3>Nenhuma luta finalizada ainda.</h3>

            </div>

        <?php } ?>

    </div>

</section>


<!-- RANKING -->

<section class="container ranking">

    <div class="titulo-secao">

        <h2>
            RANKING <span>BOXE FIGHT</span>
        </h2>

        <p>
            Os lutadores com mais vitórias aparecem no topo.
        </p>

    </div>


    <div class="tabela-ranking">

        <div class="linha-ranking cabecalho">

            <div>#</div>

            <div>Lutador</div>

            <div class="ranking-numero">
                Vitórias
            </div>

            <div class="ranking-numero">
                Derrotas
            </div>

            <div class="ranking-numero">
                Empates
            </div>

        </div>


        <?php

        $posicao = 1;

        if ($ranking && $ranking->num_rows > 0) {

            while ($lutador = $ranking->fetch_assoc()) {

        ?>

            <div class="linha-ranking">

                <div class="posicao <?php echo $posicao == 1 ? 'primeiro' : ''; ?>">

                    <?php echo $posicao; ?>º

                </div>


                <div class="nome-ranking">

                    <?php if (!empty($lutador["foto"])) { ?>

                        <img
                        src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                        alt="Foto">

                    <?php } else { ?>

                        <div class="mini-sem-foto">
                            <i class="fas fa-user"></i>
                        </div>

                    <?php } ?>

                    <span>
                        <?php echo htmlspecialchars($lutador["nome"]); ?>
                    </span>

                </div>


                <div class="ranking-numero vitoria">

                    <?php
                    echo $lutador["vitorias_calculadas"];
                    ?>

                </div>


                <div class="ranking-numero derrota">

                    <?php
                    echo $lutador["derrotas_calculadas"];
                    ?>

                </div>


                <div class="ranking-numero empate">

                    <?php
                    echo $lutador["empates_calculados"];
                    ?>

                </div>

            </div>

        <?php

                $posicao++;

            }

        } else {

        ?>

            <div style="padding:40px;text-align:center;color:#999;">
                Nenhum lutador cadastrado.
            </div>

        <?php } ?>

    </div>

</section>


<!-- CTA -->

<section class="container">

    <div class="cta">

        <h2>
            PRONTO PARA ENTRAR NO RINGUE?
        </h2>

        <p>
            Confira todos os lutadores e confrontos da Boxe Fight.
        </p>

        <a href="lutadores.php" class="botao">
            <i class="fas fa-fist-raised"></i>
            Ver lutadores
        </a>

    </div>

</section>


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
$conexao->close();
?>
