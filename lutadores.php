<?php
session_start();
include("conexao.php");

$logado = isset($_SESSION["id"]);
$admin = $logado && isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin";

/* =========================
   BUSCAR LUTADORES
========================= */

$sql = "
    SELECT 
        l.*,

        (
            SELECT COUNT(*)
            FROM lutas lu
            WHERE
                (lu.lutador1 = l.nome AND lu.resultado = 'Vitória do Lutador 1')
                OR
                (lu.lutador2 = l.nome AND lu.resultado = 'Vitória do Lutador 2')
        ) AS vitorias_calculadas,

        (
            SELECT COUNT(*)
            FROM lutas lu
            WHERE
                (lu.lutador1 = l.nome AND lu.resultado = 'Vitória do Lutador 2')
                OR
                (lu.lutador2 = l.nome AND lu.resultado = 'Vitória do Lutador 1')
        ) AS derrotas_calculadas,

        (
            SELECT COUNT(*)
            FROM lutas lu
            WHERE
                lu.resultado = 'Empate'
                AND
                (lu.lutador1 = l.nome OR lu.lutador2 = l.nome)
        ) AS empates_calculados

    FROM lutadores l
    ORDER BY l.nome ASC
";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Lutadores | Boxe Fight</title>

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
    align-items: center;
    gap: 25px;
}

.menu a {
    color: #ddd;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
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
   CABEÇALHO
========================= */

.topo {
    padding: 65px 0 40px;
}

.topo-tag {
    display: inline-block;
    color: #e10600;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.topo h1 {
    font-size: 48px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.topo h1 span {
    color: #e10600;
}

.topo p {
    color: #888;
    max-width: 650px;
    line-height: 1.6;
}

/* =========================
   BARRA DE ADMIN
========================= */

.admin-bar {
    background: #151515;
    border: 1px solid #292929;
    border-left: 4px solid #e10600;
    padding: 18px 20px;
    border-radius: 7px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 30px;
}

.admin-bar strong {
    font-size: 15px;
}

.admin-bar span {
    color: #888;
    font-size: 13px;
}

.btn {
    display: inline-block;
    text-decoration: none;
    background: #e10600;
    color: #fff;
    padding: 11px 17px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 13px;
    transition: 0.3s;
}

.btn:hover {
    background: #b80500;
}

.btn-cinza {
    background: #292929;
}

.btn-cinza:hover {
    background: #3a3a3a;
}

/* =========================
   GRID
========================= */

.grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

/* =========================
   CARD
========================= */

.card {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 12px;
    overflow: hidden;

    transition: 0.3s;

    position: relative;
}

.card:hover {
    transform: translateY(-6px);
    border-color: #e10600;
    box-shadow: 0 12px 35px rgba(0,0,0,0.5);
}

/* =========================
   FOTO
========================= */

.foto {
    height: 310px;
    background: #111;
    position: relative;
    overflow: hidden;
}

.foto img {
    width: 100%;
    height: 100%;
    object-fit: cover;

    transition: 0.4s;
}

.card:hover .foto img {
    transform: scale(1.05);
}

.sem-foto {
    width: 100%;
    height: 100%;

    display: flex;
    justify-content: center;
    align-items: center;

    color: #555;
    font-size: 80px;
    font-weight: bold;
}

/* =========================
   TAG
========================= */

.tag-categoria {
    position: absolute;
    top: 15px;
    left: 15px;

    background: #e10600;
    color: #fff;

    padding: 7px 10px;
    border-radius: 4px;

    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

/* =========================
   CONTEÚDO
========================= */

.conteudo {
    padding: 22px;
}

.conteudo h2 {
    font-size: 23px;
    text-transform: uppercase;
    margin-bottom: 6px;
}

.nacionalidade {
    color: #888;
    font-size: 13px;
    margin-bottom: 18px;
}

.cartel {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}

.cartel div {
    background: #0e0e0e;
    border: 1px solid #252525;
    border-radius: 5px;
    text-align: center;
    padding: 10px 5px;
}

.cartel strong {
    display: block;
    font-size: 20px;
}

.cartel span {
    display: block;
    margin-top: 3px;
    color: #777;
    font-size: 10px;
    text-transform: uppercase;
}

.vitoria strong {
    color: #35d46a;
}

.derrota strong {
    color: #e10600;
}

.empate strong {
    color: #f0c000;
}

/* =========================
   BOTÕES
========================= */

.acoes {
    display: flex;
    gap: 8px;
}

.acoes .perfil-btn {
    flex: 1;
    text-align: center;
}

.editar {
    background: #292929;
}

.editar:hover {
    background: #444;
}

.excluir {
    background: transparent;
    border: 1px solid #e10600;
    color: #e10600;
}

.excluir:hover {
    background: #e10600;
    color: #fff;
}

/* =========================
   VAZIO
========================= */

.vazio {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    text-align: center;
    padding: 70px 20px;
    color: #888;
}

.vazio h2 {
    color: #fff;
    margin-bottom: 10px;
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

@media (max-width: 1000px) {

    .grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 700px) {

    .grid {
        grid-template-columns: 1fr;
    }

    .menu {
        gap: 12px;
    }

    .menu a {
        font-size: 12px;
    }

    .topo h1 {
        font-size: 38px;
    }

    .admin-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

}

@media (max-width: 500px) {

    .menu {
        display: none;
    }

    .container {
        width: 92%;
    }

    .foto {
        height: 350px;
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

        <a href="lutadores.php">
            Lutadores
        </a>

        <a href="lutas.php">
            Lutas
        </a>

        <a href="loja.php">
            Loja
        </a>

        <?php if ($logado): ?>

            <a href="painel.php">
                Painel
            </a>

            <a href="logout.php" class="sair">
                Sair
            </a>

        <?php else: ?>

            <a href="login.php" class="entrar">
                Entrar
            </a>

        <?php endif; ?>

    </div>

</nav>


<div class="container">


    <!-- =========================
         CABEÇALHO
    ========================= -->

    <section class="topo">

        <span class="topo-tag">
            Boxe Fight
        </span>

        <h1>
            Nossos <span>Lutadores</span>
        </h1>

        <p>
            Conheça os atletas cadastrados na Boxe Fight,
            acompanhe seus cartéis e veja o histórico completo
            de cada lutador.
        </p>

    </section>


    <!-- =========================
         ÁREA ADMIN
    ========================= -->

    <?php if ($admin): ?>

        <div class="admin-bar">

            <div>

                <strong>
                    Painel de gerenciamento
                </strong>

                <br>

                <span>
                    Você está logado como administrador.
                </span>

            </div>

            <a href="cadastrar_lutador.php" class="btn">
                + Cadastrar lutador
            </a>

        </div>

    <?php endif; ?>


    <!-- =========================
         LUTADORES
    ========================= -->

    <?php if ($resultado && $resultado->num_rows > 0): ?>

        <div class="grid">

            <?php while ($lutador = $resultado->fetch_assoc()): ?>

                <?php

                $vitorias = intval($lutador["vitorias_calculadas"]);
                $derrotas = intval($lutador["derrotas_calculadas"]);
                $empates = intval($lutador["empates_calculados"]);

                ?>

                <div class="card">


                    <!-- FOTO -->

                    <div class="foto">

                        <span class="tag-categoria">

                            <?php
                            echo htmlspecialchars($lutador["categoria"]);
                            ?>

                        </span>


                        <?php if (!empty($lutador["foto"])): ?>

                            <img
                                src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                                alt="<?php echo htmlspecialchars($lutador["nome"]); ?>"
                            >

                        <?php else: ?>

                            <div class="sem-foto">
                                ?
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- CONTEÚDO -->

                    <div class="conteudo">

                        <h2>

                            <?php
                            echo htmlspecialchars($lutador["nome"]);
                            ?>

                        </h2>


                        <div class="nacionalidade">

                            <?php
                            echo htmlspecialchars($lutador["nacionalidade"]);
                            ?>

                            •

                            <?php
                            echo intval($lutador["idade"]);
                            ?>
                            anos

                        </div>


                        <!-- CARTEL -->

                        <div class="cartel">

                            <div class="vitoria">

                                <strong>
                                    <?php echo $vitorias; ?>
                                </strong>

                                <span>
                                    Vitórias
                                </span>

                            </div>


                            <div class="derrota">

                                <strong>
                                    <?php echo $derrotas; ?>
                                </strong>

                                <span>
                                    Derrotas
                                </span>

                            </div>


                            <div class="empate">

                                <strong>
                                    <?php echo $empates; ?>
                                </strong>

                                <span>
                                    Empates
                                </span>

                            </div>

                        </div>


                        <!-- AÇÕES -->

                        <div class="acoes">

                            <a
                                href="perfil_lutador.php?id=<?php echo $lutador["id"]; ?>"
                                class="btn perfil-btn"
                            >
                                Ver perfil
                            </a>


                            <?php if ($admin): ?>

                                <a
                                    href="editar_lutador.php?id=<?php echo $lutador["id"]; ?>"
                                    class="btn editar"
                                >
                                    Editar
                                </a>

                                <a
                                    href="excluir_lutador.php?id=<?php echo $lutador["id"]; ?>"
                                    class="btn excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir este lutador?');"
                                >
                                    Excluir
                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="vazio">

            <h2>
                Nenhum lutador cadastrado
            </h2>

            <p>
                Ainda não existem lutadores registrados no sistema.
            </p>

            <?php if ($admin): ?>

                <br>

                <a href="cadastrar_lutador.php" class="btn">
                    Cadastrar primeiro lutador
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>


</div>


<!-- =========================
     FOOTER
========================= -->

<footer>

    <strong>
        BOXE<span>FIGHT</span>
    </strong>

    <p>
        Sistema de gerenciamento de boxe.
    </p>

</footer>


</body>

</html>
