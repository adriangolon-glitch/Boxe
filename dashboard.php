<?php
session_start();
include("conexao.php");

/* =========================
   VERIFICAR ADMIN
========================= */

if (!isset($_SESSION["id"]) || !isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}


/* =========================
   ESTATÍSTICAS
========================= */

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM usuarios");
$total_usuarios = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM lutadores");
$total_lutadores = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM lutas");
$total_lutas = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM produtos");
$total_produtos = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM pedidos");
$total_pedidos = $resultado->fetch_assoc()["total"];


/* =========================
   PEDIDOS PENDENTES
========================= */

$resultado = $conexao->query("
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Pendente'
");

$pedidos_pendentes = $resultado->fetch_assoc()["total"];


/* =========================
   ÚLTIMOS USUÁRIOS
========================= */

$ultimos_usuarios = $conexao->query("
    SELECT id, usuario, tipo
    FROM usuarios
    ORDER BY id DESC
    LIMIT 5
");


/* =========================
   ÚLTIMOS LUTADORES
========================= */

$ultimos_lutadores = $conexao->query("
    SELECT id, nome, categoria
    FROM lutadores
    ORDER BY id DESC
    LIMIT 5
");


/* =========================
   ÚLTIMAS LUTAS
========================= */

$ultimas_lutas = $conexao->query("
    SELECT *
    FROM lutas
    ORDER BY data_luta DESC, id DESC
    LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard | Boxe Fight</title>

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
    gap: 22px;
}

.menu a {
    color: #ccc;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    transition: 0.3s;
}

.menu a:hover {
    color: #e10600;
}

.sair {
    color: #e10600 !important;
}


/* =========================
   CONTAINER
========================= */

.container {
    width: 86%;
    max-width: 1300px;
    margin: auto;
}


/* =========================
   CABEÇALHO
========================= */

.topo {
    padding: 55px 0 35px;
}

.tag {
    color: #e10600;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.topo h1 {
    font-size: 45px;
    margin-top: 10px;
    text-transform: uppercase;
}

.topo h1 span {
    color: #e10600;
}

.topo p {
    color: #888;
    margin-top: 10px;
}


/* =========================
   STATUS ADMIN
========================= */

.status {
    background: #151515;
    border: 1px solid #292929;
    border-left: 4px solid #e10600;

    padding: 18px 20px;
    border-radius: 7px;

    margin-bottom: 30px;
}

.status strong {
    color: #fff;
}

.status span {
    color: #35d46a;
    font-weight: bold;
}


/* =========================
   ESTATÍSTICAS
========================= */

.stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
}

.stat {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;

    padding: 25px;

    position: relative;
    overflow: hidden;

    transition: 0.3s;
}

.stat:hover {
    transform: translateY(-4px);
    border-color: #e10600;
}

.stat::after {
    content: "";
    position: absolute;

    width: 80px;
    height: 80px;

    background: #e10600;
    opacity: 0.08;

    border-radius: 50%;

    right: -25px;
    top: -25px;
}

.stat h3 {
    font-size: 34px;
    margin-bottom: 7px;
}

.stat p {
    color: #888;
    font-size: 13px;
}

.numero-vermelho {
    color: #e10600;
}


/* =========================
   SEÇÃO
========================= */

.secao {
    margin-top: 50px;
}

.titulo {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 20px;
}

.titulo h2 {
    font-size: 25px;
    text-transform: uppercase;
}

.titulo h2 span {
    color: #e10600;
}

.ver-tudo {
    color: #e10600;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
}

.ver-tudo:hover {
    text-decoration: underline;
}


/* =========================
   GRID PRINCIPAL
========================= */

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}


/* =========================
   PAINEL
========================= */

.painel {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    overflow: hidden;
}

.painel-header {
    padding: 18px 20px;
    background: #111;
    border-bottom: 1px solid #292929;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.painel-header strong {
    text-transform: uppercase;
    font-size: 14px;
}

.painel-header span {
    color: #888;
    font-size: 12px;
}


/* =========================
   LISTA
========================= */

.lista {
    padding: 5px 20px;
}

.item {
    padding: 17px 0;

    border-bottom: 1px solid #242424;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.item:last-child {
    border-bottom: none;
}

.item-info strong {
    display: block;
    font-size: 14px;
    margin-bottom: 5px;
}

.item-info small {
    color: #777;
}

.badge {
    padding: 6px 9px;
    border-radius: 4px;

    background: #252525;
    color: #aaa;

    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-admin {
    background: #e10600;
    color: #fff;
}


/* =========================
   LUTAS
========================= */

.luta-item {
    padding: 17px 0;
    border-bottom: 1px solid #242424;
}

.luta-item:last-child {
    border-bottom: none;
}

.luta-data {
    color: #777;
    font-size: 11px;
    margin-bottom: 7px;
}

.luta-nomes {
    font-weight: bold;
}

.luta-nomes span {
    color: #e10600;
    margin: 0 5px;
}

.luta-metodo {
    color: #777;
    font-size: 11px;
    margin-top: 6px;
}


/* =========================
   ATALHOS
========================= */

.atalhos {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.atalho {
    background: #151515;
    border: 1px solid #292929;

    border-radius: 9px;
    padding: 25px 20px;

    text-decoration: none;
    color: #fff;

    transition: 0.3s;
}

.atalho:hover {
    background: #1c1c1c;
    border-color: #e10600;
    transform: translateY(-4px);
}

.icone {
    width: 45px;
    height: 45px;

    background: #e10600;

    border-radius: 6px;

    display: flex;
    justify-content: center;
    align-items: center;

    font-size: 20px;
    font-weight: bold;

    margin-bottom: 15px;
}

.atalho strong {
    display: block;
    margin-bottom: 5px;
}

.atalho small {
    color: #777;
}


/* =========================
   PEDIDOS
========================= */

.pedido-alerta {
    margin-top: 30px;

    background: #241313;
    border: 1px solid #5a1717;

    border-radius: 8px;

    padding: 18px 20px;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pedido-alerta strong {
    color: #fff;
}

.pedido-alerta span {
    color: #e10600;
    font-weight: bold;
}

.btn {
    background: #e10600;
    color: #fff;

    text-decoration: none;

    padding: 10px 15px;

    border-radius: 5px;

    font-size: 12px;
    font-weight: bold;
}

.btn:hover {
    background: #b80500;
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

@media (max-width: 1100px) {

    .stats {
        grid-template-columns: repeat(3, 1fr);
    }

    .atalhos {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 800px) {

    .grid {
        grid-template-columns: 1fr;
    }

    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .menu {
        gap: 12px;
    }

}

@media (max-width: 550px) {

    .container {
        width: 92%;
    }

    .menu {
        display: none;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    .atalhos {
        grid-template-columns: 1fr;
    }

    .topo h1 {
        font-size: 34px;
    }

    .pedido-alerta {
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="logout.php" class="sair">
            Sair
        </a>

    </div>

</nav>


<div class="container">


    <!-- =========================
         CABEÇALHO
    ========================= -->

    <section class="topo">

        <span class="tag">
            Área administrativa
        </span>

        <h1>
            Painel <span>Control</span>
        </h1>

        <p>
            Gerencie usuários, lutadores, lutas, produtos e pedidos
            do sistema Boxe Fight.
        </p>

    </section>


    <!-- =========================
         STATUS
    ========================= -->

    <div class="status">

        <strong>
            Administrador conectado:
        </strong>

        <?php echo htmlspecialchars($_SESSION["usuario"]); ?>

        <span>
            ● ONLINE
        </span>

    </div>


    <!-- =========================
         ESTATÍSTICAS
    ========================= -->

    <section class="stats">

        <div class="stat">

            <h3>
                <?php echo $total_usuarios; ?>
            </h3>

            <p>
                Usuários cadastrados
            </p>

        </div>


        <div class="stat">

            <h3>
                <?php echo $total_lutadores; ?>
            </h3>

            <p>
                Lutadores
            </p>

        </div>


        <div class="stat">

            <h3 class="numero-vermelho">
                <?php echo $total_lutas; ?>
            </h3>

            <p>
                Lutas registradas
            </p>

        </div>


        <div class="stat">

            <h3>
                <?php echo $total_produtos; ?>
            </h3>

            <p>
                Produtos
            </p>

        </div>


        <div class="stat">

            <h3>
                <?php echo $total_pedidos; ?>
            </h3>

            <p>
                Pedidos
            </p>

        </div>

    </section>


    <!-- =========================
         ÚLTIMOS DADOS
    ========================= -->

    <section class="secao">

        <div class="titulo">

            <h2>
                Atividade <span>recente</span>
            </h2>

        </div>


        <div class="grid">


            <!-- USUÁRIOS -->

            <div class="painel">

                <div class="painel-header">

                    <strong>
                        Últimos usuários
                    </strong>

                    <span>
                        5 recentes
                    </span>

                </div>


                <div class="lista">

                    <?php if ($ultimos_usuarios->num_rows > 0): ?>

                        <?php while ($usuario = $ultimos_usuarios->fetch_assoc()): ?>

                            <div class="item">

                                <div class="item-info">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars($usuario["usuario"]);
                                        ?>
                                    </strong>

                                    <small>
                                        ID #<?php echo $usuario["id"]; ?>
                                    </small>

                                </div>


                                <?php if ($usuario["tipo"] == "admin"): ?>

                                    <span class="badge badge-admin">
                                        ADMIN
                                    </span>

                                <?php else: ?>

                                    <span class="badge">
                                        USUÁRIO
                                    </span>

                                <?php endif; ?>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="item">
                            <small>Nenhum usuário cadastrado.</small>
                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- LUTADORES -->

            <div class="painel">

                <div class="painel-header">

                    <strong>
                        Últimos lutadores
                    </strong>

                    <span>
                        5 recentes
                    </span>

                </div>


                <div class="lista">

                    <?php if ($ultimos_lutadores->num_rows > 0): ?>

                        <?php while ($lutador = $ultimos_lutadores->fetch_assoc()): ?>

                            <div class="item">

                                <div class="item-info">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars($lutador["nome"]);
                                        ?>
                                    </strong>

                                    <small>
                                        <?php
                                        echo htmlspecialchars($lutador["categoria"]);
                                        ?>
                                    </small>

                                </div>


                                <a
                                    href="perfil_lutador.php?id=<?php echo $lutador["id"]; ?>"
                                    class="btn"
                                >
                                    Ver
                                </a>

                            </div>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <div class="item">
                            <small>Nenhum lutador cadastrado.</small>
                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         ÚLTIMAS LUTAS
    ========================= -->

    <section class="secao">

        <div class="titulo">

            <h2>
                Últimas <span>lutas</span>
            </h2>

            <a href="lutas.php" class="ver-tudo">
                Ver todas →
            </a>

        </div>


        <div class="painel">

            <div class="lista">

                <?php if ($ultimas_lutas->num_rows > 0): ?>

                    <?php while ($luta = $ultimas_lutas->fetch_assoc()): ?>

                        <div class="luta-item">

                            <div class="luta-data">

                                <?php
                                echo date(
                                    "d/m/Y",
                                    strtotime($luta["data_luta"])
                                );
                                ?>

                            </div>


                            <div class="luta-nomes">

                                <?php
                                echo htmlspecialchars($luta["lutador1"]);
                                ?>

                                <span>
                                    VS
                                </span>

                                <?php
                                echo htmlspecialchars($luta["lutador2"]);
                                ?>

                            </div>


                            <div class="luta-metodo">

                                <?php
                                echo htmlspecialchars($luta["metodo"]);
                                ?>

                                —
                                <?php
                                echo htmlspecialchars($luta["resultado"]);
                                ?>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="item">
                        <small>Nenhuma luta cadastrada.</small>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>


    <!-- =========================
         ATALHOS
    ========================= -->

    <section class="secao">

        <div class="titulo">

            <h2>
                Gerenciamento <span>rápido</span>
            </h2>

        </div>


        <div class="atalhos">


            <a href="usuarios.php" class="atalho">

                <div class="icone">
                    U
                </div>

                <strong>
                    Usuários
                </strong>

                <small>
                    Gerenciar contas
                </small>

            </a>


            <a href="lutadores.php" class="atalho">

                <div class="icone">
                    L
                </div>

                <strong>
                    Lutadores
                </strong>

                <small>
                    Cadastrar e editar atletas
                </small>

            </a>


            <a href="lutas.php" class="atalho">

                <div class="icone">
                    VS
                </div>

                <strong>
                    Lutas
                </strong>

                <small>
                    Gerenciar confrontos
                </small>

            </a>


            <a href="loja.php" class="atalho">

                <div class="icone">
                    $
                </div>

                <strong>
                    Loja
                </strong>

                <small>
                    Ver produtos
                </small>

            </a>


            <a href="cadastrar_lutador.php" class="atalho">

                <div class="icone">
                    +
                </div>

                <strong>
                    Novo lutador
                </strong>

                <small>
                    Adicionar atleta
                </small>

            </a>


            <a href="cadastrar_luta.php" class="atalho">

                <div class="icone">
                    +
                </div>

                <strong>
                    Nova luta
                </strong>

                <small>
                    Registrar confronto
                </small>

            </a>


            <a href="index.php" class="atalho">

                <div class="icone">
                    H
                </div>

                <strong>
                    Página inicial
                </strong>

                <small>
                    Voltar ao site
                </small>

            </a>


            <a href="logout.php" class="atalho">

                <div class="icone">
                    X
                </div>

                <strong>
                    Sair
                </strong>

                <small>
                    Encerrar sessão
                </small>

            </a>

        </div>

    </section>


    <!-- =========================
         PEDIDOS PENDENTES
    ========================= -->

    <?php if ($pedidos_pendentes > 0): ?>

        <div class="pedido-alerta">

            <div>

                <strong>
                    Existem
                    <span>
                        <?php echo $pedidos_pendentes; ?>
                    </span>
                    pedido(s) pendente(s).
                </strong>

                <br>

                <small>
                    Verifique os pedidos realizados na loja.
                </small>

            </div>


            <a href="pedidos.php" class="btn">
                Ver pedidos
            </a>

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
        Painel administrativo do sistema Boxe Fight.
    </p>

</footer>


</body>

</html>