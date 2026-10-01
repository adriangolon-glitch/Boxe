<?php
session_start();
include("conexao.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: lutadores.php");
    exit;
}

$id = intval($_GET["id"]);

/* =========================
   BUSCAR LUTADOR
========================= */

$sql = "SELECT * FROM lutadores WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "<script>alert('Lutador não encontrado!'); window.location.href='lutadores.php';</script>";
    exit;
}

$lutador = $resultado->fetch_assoc();

$nome = $lutador["nome"];

/* =========================
   CALCULAR CARTEL
========================= */

$sql_vitorias = "
    SELECT COUNT(*) AS total
    FROM lutas
    WHERE
    (lutador1 = ? AND resultado = 'Vitória do Lutador 1')
    OR
    (lutador2 = ? AND resultado = 'Vitória do Lutador 2')
";

$stmt = $conexao->prepare($sql_vitorias);
$stmt->bind_param("ss", $nome, $nome);
$stmt->execute();
$vitorias = $stmt->get_result()->fetch_assoc()["total"];


/* DERROTAS */

$sql_derrotas = "
    SELECT COUNT(*) AS total
    FROM lutas
    WHERE
    (lutador1 = ? AND resultado = 'Vitória do Lutador 2')
    OR
    (lutador2 = ? AND resultado = 'Vitória do Lutador 1')
";

$stmt = $conexao->prepare($sql_derrotas);
$stmt->bind_param("ss", $nome, $nome);
$stmt->execute();
$derrotas = $stmt->get_result()->fetch_assoc()["total"];


/* EMPATES */

$sql_empates = "
    SELECT COUNT(*) AS total
    FROM lutas
    WHERE
    resultado = 'Empate'
    AND
    (lutador1 = ? OR lutador2 = ?)
";

$stmt = $conexao->prepare($sql_empates);
$stmt->bind_param("ss", $nome, $nome);
$stmt->execute();
$empates = $stmt->get_result()->fetch_assoc()["total"];

$total_lutas = $vitorias + $derrotas + $empates;

if ($total_lutas > 0) {
    $aproveitamento = round(($vitorias / $total_lutas) * 100);
} else {
    $aproveitamento = 0;
}


/* =========================
   HISTÓRICO DE LUTAS
========================= */

$sql_lutas = "
    SELECT *
    FROM lutas
    WHERE lutador1 = ? OR lutador2 = ?
    ORDER BY data_luta DESC, id DESC
";

$stmt = $conexao->prepare($sql_lutas);
$stmt->bind_param("ss", $nome, $nome);
$stmt->execute();

$lutas = $stmt->get_result();

$logado = isset($_SESSION["id"]);
$admin = $logado && isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin";

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo htmlspecialchars($nome); ?> | Boxe Fight</title>

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

nav {
    background: #000;
    border-bottom: 1px solid #222;
    padding: 18px 7%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.logo {
    font-size: 25px;
    font-weight: bold;
    letter-spacing: 2px;
}

.logo span {
    color: #e10600;
}

.menu {
    display: flex;
    gap: 25px;
    align-items: center;
}

.menu a {
    color: #ddd;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
    transition: 0.3s;
}

.menu a:hover {
    color: #e10600;
}

.menu .entrar {
    background: #e10600;
    color: #fff;
    padding: 10px 17px;
    border-radius: 5px;
}

.menu .entrar:hover {
    background: #b80500;
}

.menu .sair {
    color: #e10600;
}

/* =========================
   CONTAINER
========================= */

.container {
    width: 86%;
    max-width: 1250px;
    margin: auto;
}

/* =========================
   HERO DO LUTADOR
========================= */

.perfil {
    margin-top: 45px;
    background: linear-gradient(135deg, #161616, #080808);
    border: 1px solid #292929;
    border-radius: 15px;
    overflow: hidden;
    display: grid;
    grid-template-columns: 380px 1fr;
    min-height: 430px;
    box-shadow: 0 15px 50px rgba(0,0,0,0.5);
}

.foto-area {
    background: #111;
    display: flex;
    justify-content: center;
    align-items: flex-end;
    position: relative;
    min-height: 430px;
}

.foto-area::before {
    content: "";
    position: absolute;
    width: 300px;
    height: 300px;
    background: #e10600;
    opacity: 0.12;
    border-radius: 50%;
    filter: blur(20px);
}

.foto-lutador {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: relative;
    z-index: 2;
}

.sem-foto {
    width: 100%;
    height: 100%;
    min-height: 430px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #777;
    font-size: 100px;
    font-weight: bold;
    position: relative;
    z-index: 2;
}

.info {
    padding: 50px;
}

.tag {
    display: inline-block;
    background: #e10600;
    padding: 7px 13px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
}

.info h1 {
    font-size: 52px;
    text-transform: uppercase;
    line-height: 1;
    margin-bottom: 15px;
}

.categoria {
    color: #aaa;
    font-size: 18px;
    margin-bottom: 30px;
}

.dados {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.dado {
    background: #111;
    border: 1px solid #292929;
    padding: 18px;
    border-radius: 8px;
}

.dado strong {
    display: block;
    font-size: 25px;
    margin-bottom: 5px;
}

.dado span {
    color: #888;
    font-size: 12px;
    text-transform: uppercase;
}

.cartel {
    display: flex;
    gap: 30px;
    margin-bottom: 25px;
}

.cartel div {
    font-size: 14px;
    color: #aaa;
}

.cartel strong {
    color: #fff;
    font-size: 18px;
}

.aproveitamento {
    color: #e10600 !important;
    font-weight: bold;
}

/* =========================
   INFORMAÇÕES
========================= */

.informacoes {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.info-box {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 8px;
    padding: 20px;
}

.info-box span {
    color: #888;
    font-size: 12px;
    text-transform: uppercase;
}

.info-box strong {
    display: block;
    margin-top: 7px;
    font-size: 18px;
}

/* =========================
   HISTÓRICO
========================= */

.secao {
    margin-top: 60px;
}

.titulo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.titulo h2 {
    font-size: 28px;
    text-transform: uppercase;
}

.titulo h2 span {
    color: #e10600;
}

.btn-voltar {
    background: #1d1d1d;
    color: #fff;
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 5px;
    border: 1px solid #333;
    font-size: 13px;
    font-weight: bold;
}

.btn-voltar:hover {
    background: #e10600;
}

/* =========================
   LUTAS
========================= */

.luta {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 12px;
    display: grid;
    grid-template-columns: 120px 1fr 170px;
    align-items: center;
    gap: 20px;
}

.data {
    color: #888;
    font-size: 13px;
}

.confronto {
    font-weight: bold;
}

.confronto .vs {
    color: #e10600;
    margin: 0 8px;
}

.metodo {
    color: #888;
    font-size: 13px;
    margin-top: 6px;
}

.resultado {
    text-align: right;
    font-weight: bold;
}

.vitoria {
    color: #35d46a;
}

.derrota {
    color: #e10600;
}

.empate {
    color: #f0c000;
}

.semresultado {
    color: #aaa;
}

/* =========================
   SEM LUTAS
========================= */

.sem-lutas {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    text-align: center;
    padding: 50px 20px;
    color: #888;
}

/* =========================
   FOOTER
========================= */

footer {
    background: #000;
    border-top: 1px solid #222;
    margin-top: 80px;
    padding: 35px 7%;
    text-align: center;
    color: #777;
}

footer strong {
    color: #fff;
}

footer span {
    color: #e10600;
}

/* =========================
   RESPONSIVO
========================= */

@media (max-width: 900px) {

    .perfil {
        grid-template-columns: 1fr;
    }

    .foto-area {
        height: 400px;
    }

    .info h1 {
        font-size: 40px;
    }

    .menu {
        gap: 12px;
    }

    .menu a {
        font-size: 12px;
    }

    .luta {
        grid-template-columns: 1fr;
    }

    .resultado {
        text-align: left;
    }
}

@media (max-width: 600px) {

    nav {
        padding: 15px 5%;
    }

    .menu {
        display: none;
    }

    .container {
        width: 92%;
    }

    .info {
        padding: 30px 20px;
    }

    .dados {
        grid-template-columns: 1fr;
    }

    .informacoes {
        grid-template-columns: 1fr;
    }

    .cartel {
        flex-wrap: wrap;
        gap: 15px;
    }

    .info h1 {
        font-size: 32px;
    }

    .titulo {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
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
        BOXE<span>FIGHT</span>
    </div>

    <div class="menu">

        <a href="index.php">Início</a>
        <a href="arena.php">Arena</a>
        <a href="lutadores.php">Lutadores</a>
        <a href="lutas.php">Lutas</a>
        <a href="loja.php">Loja</a>

        <?php if ($logado): ?>

            <a href="painel.php">Painel</a>
            <a href="logout.php" class="sair">Sair</a>

        <?php else: ?>

            <a href="login.php" class="entrar">Entrar</a>

        <?php endif; ?>

    </div>

</nav>


<div class="container">

    <!-- =========================
         PERFIL
    ========================= -->

    <section class="perfil">

        <div class="foto-area">

            <?php if (!empty($lutador["foto"])): ?>

                <img
                    src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                    class="foto-lutador"
                    alt="<?php echo htmlspecialchars($nome); ?>"
                >

            <?php else: ?>

                <div class="sem-foto">
                    ?
                </div>

            <?php endif; ?>

        </div>


        <div class="info">

            <span class="tag">
                Lutador
            </span>

            <h1>
                <?php echo htmlspecialchars($nome); ?>
            </h1>

            <div class="categoria">
                <?php echo htmlspecialchars($lutador["categoria"]); ?>
            </div>


            <div class="dados">

                <div class="dado">
                    <strong class="vitoria">
                        <?php echo $vitorias; ?>
                    </strong>
                    <span>Vitórias</span>
                </div>

                <div class="dado">
                    <strong class="derrota">
                        <?php echo $derrotas; ?>
                    </strong>
                    <span>Derrotas</span>
                </div>

                <div class="dado">
                    <strong class="empate">
                        <?php echo $empates; ?>
                    </strong>
                    <span>Empates</span>
                </div>

            </div>


            <div class="cartel">

                <div>
                    <strong><?php echo $total_lutas; ?></strong>
                    <br>
                    Lutas
                </div>

                <div>
                    <strong class="aproveitamento">
                        <?php echo $aproveitamento; ?>%
                    </strong>
                    <br>
                    Aproveitamento
                </div>

            </div>


            <div class="informacoes">

                <div class="info-box">
                    <span>Nacionalidade</span>
                    <strong>
                        <?php echo htmlspecialchars($lutador["nacionalidade"]); ?>
                    </strong>
                </div>

                <div class="info-box">
                    <span>Idade</span>
                    <strong>
                        <?php echo $lutador["idade"]; ?> anos
                    </strong>
                </div>

                <div class="info-box">
                    <span>Categoria</span>
                    <strong>
                        <?php echo htmlspecialchars($lutador["categoria"]); ?>
                    </strong>
                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         HISTÓRICO DE LUTAS
    ========================= -->

    <section class="secao">

        <div class="titulo">

            <h2>
                Histórico de <span>Lutas</span>
            </h2>

            <a href="lutadores.php" class="btn-voltar">
                ← Voltar para lutadores
            </a>

        </div>


        <?php if ($lutas->num_rows > 0): ?>

            <?php while ($luta = $lutas->fetch_assoc()): ?>

                <?php

                if ($luta["lutador1"] == $nome) {

                    $oponente = $luta["lutador2"];

                    if ($luta["resultado"] == "Vitória do Lutador 1") {
                        $classe_resultado = "vitoria";
                        $texto_resultado = "VITÓRIA";
                    } elseif ($luta["resultado"] == "Vitória do Lutador 2") {
                        $classe_resultado = "derrota";
                        $texto_resultado = "DERROTA";
                    } elseif ($luta["resultado"] == "Empate") {
                        $classe_resultado = "empate";
                        $texto_resultado = "EMPATE";
                    } else {
                        $classe_resultado = "semresultado";
                        $texto_resultado = "SEM RESULTADO";
                    }

                } else {

                    $oponente = $luta["lutador1"];

                    if ($luta["resultado"] == "Vitória do Lutador 2") {
                        $classe_resultado = "vitoria";
                        $texto_resultado = "VITÓRIA";
                    } elseif ($luta["resultado"] == "Vitória do Lutador 1") {
                        $classe_resultado = "derrota";
                        $texto_resultado = "DERROTA";
                    } elseif ($luta["resultado"] == "Empate") {
                        $classe_resultado = "empate";
                        $texto_resultado = "EMPATE";
                    } else {
                        $classe_resultado = "semresultado";
                        $texto_resultado = "SEM RESULTADO";
                    }
                }

                $data_formatada = date(
                    "d/m/Y",
                    strtotime($luta["data_luta"])
                );

                ?>

                <div class="luta">

                    <div class="data">
                        <?php echo $data_formatada; ?>
                    </div>


                    <div class="confronto">

                        <?php echo htmlspecialchars($nome); ?>

                        <span class="vs">
                            VS
                        </span>

                        <?php echo htmlspecialchars($oponente); ?>

                        <div class="metodo">
                            <?php echo htmlspecialchars($luta["metodo"]); ?>
                        </div>

                    </div>


                    <div class="resultado <?php echo $classe_resultado; ?>">

                        <?php echo $texto_resultado; ?>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="sem-lutas">

                <h3>Nenhuma luta registrada</h3>

                <p>
                    Este lutador ainda não possui lutas cadastradas no sistema.
                </p>

            </div>

        <?php endif; ?>

    </section>

</div>


<footer>

    <strong>BOXE<span>FIGHT</span></strong>

    <p>
        Sistema de boxe desenvolvido para gerenciamento de lutadores,
        lutas e produtos.
    </p>

</footer>

</body>

</html>