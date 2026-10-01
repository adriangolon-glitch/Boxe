<?php

session_start();
include("conexao.php");

/* ========================================
   SOMENTE ADMIN
======================================== */

if (
    !isset($_SESSION["id"]) ||
    !isset($_SESSION["tipo"]) ||
    $_SESSION["tipo"] != "admin"
) {
    header("Location: login.php");
    exit;
}


/* ========================================
   BUSCAR PEDIDOS
======================================== */

$sql = "
    SELECT
        p.id,
        p.usuario_id,
        p.total,
        p.status,
        p.data_pedido,
        u.usuario
    FROM pedidos p
    LEFT JOIN usuarios u
        ON p.usuario_id = u.id
    ORDER BY p.data_pedido DESC, p.id DESC
";

$resultado = $conexao->query($sql);


/* ========================================
   CONTADORES
======================================== */

$sql_pendente = "
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Pendente'
";

$pendentes = $conexao->query($sql_pendente)->fetch_assoc()["total"];


$sql_processando = "
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Processando'
";

$processando = $conexao->query($sql_processando)->fetch_assoc()["total"];


$sql_enviado = "
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Enviado'
";

$enviados = $conexao->query($sql_enviado)->fetch_assoc()["total"];


$sql_concluido = "
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Concluído'
";

$concluidos = $conexao->query($sql_concluido)->fetch_assoc()["total"];


$sql_cancelado = "
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Cancelado'
";

$cancelados = $conexao->query($sql_cancelado)->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Pedidos | Boxe Fight</title>

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


/* ========================================
   NAVBAR
======================================== */

nav {
    background: #000;
    border-bottom: 2px solid #e10600;

    padding: 18px 7%;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: #e10600;
    font-size: 25px;
    font-weight: bold;
    letter-spacing: 2px;
}

.logo span {
    color: #fff;
}

.menu {
    display: flex;
    gap: 22px;
    align-items: center;
}

.menu a {
    color: #ddd;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
    transition: .3s;
}

.menu a:hover {
    color: #e10600;
}

.menu .ativo {
    color: #e10600;
}

.menu .sair {
    color: #e10600;
}


/* ========================================
   CONTAINER
======================================== */

.container {
    width: 90%;
    max-width: 1300px;
    margin: 50px auto;
}


/* ========================================
   TÍTULO
======================================== */

.topo {
    margin-bottom: 35px;
}

.tag {
    color: #e10600;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.topo h1 {
    font-size: 44px;
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


/* ========================================
   STATUS CARDS
======================================== */

.estatisticas {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-bottom: 35px;
}

.estatistica {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 9px;
    padding: 20px;

    transition: .3s;
}

.estatistica:hover {
    transform: translateY(-4px);
    border-color: #e10600;
}

.estatistica strong {
    display: block;
    font-size: 30px;
    margin-bottom: 6px;
}

.estatistica span {
    color: #888;
    font-size: 12px;
    text-transform: uppercase;
}

.numero-vermelho {
    color: #e10600;
}

.numero-amarelo {
    color: #f0c000;
}

.numero-verde {
    color: #35d46a;
}


/* ========================================
   ÁREA DOS PEDIDOS
======================================== */

.painel {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    overflow: hidden;
}

.painel-topo {
    background: #111;
    border-bottom: 1px solid #292929;

    padding: 20px;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.painel-topo h2 {
    font-size: 18px;
    text-transform: uppercase;
}

.painel-topo h2 span {
    color: #e10600;
}

.admin-badge {
    background: #e10600;
    color: #fff;

    padding: 7px 11px;

    border-radius: 4px;

    font-size: 10px;
    font-weight: bold;
}


/* ========================================
   PEDIDO
======================================== */

.pedido {
    padding: 25px;
    border-bottom: 1px solid #282828;
}

.pedido:last-child {
    border-bottom: none;
}

.pedido-cabecalho {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 20px;
}

.numero {
    color: #e10600;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 7px;
}

.cliente {
    font-size: 18px;
    font-weight: bold;
}

.usuario-id {
    color: #777;
    font-size: 12px;
    margin-top: 5px;
}

.data {
    color: #888;
    font-size: 12px;
}


/* ========================================
   INFORMAÇÕES
======================================== */

.info-pedido {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;

    margin-bottom: 20px;
}

.info-box {
    background: #0e0e0e;
    border: 1px solid #252525;
    border-radius: 6px;
    padding: 15px;
}

.info-box span {
    display: block;
    color: #777;
    font-size: 10px;
    text-transform: uppercase;
    margin-bottom: 7px;
}

.info-box strong {
    font-size: 18px;
}


/* ========================================
   STATUS
======================================== */

.status-form {
    display: flex;
    align-items: center;
    gap: 12px;
}

.status-form label {
    color: #888;
    font-size: 12px;
    font-weight: bold;
}

.status-form select {
    background: #0b0b0b;
    border: 1px solid #444;

    color: #fff;

    padding: 10px;

    border-radius: 5px;

    outline: none;
}

.status-form select:focus {
    border-color: #e10600;
}

.btn {
    border: none;

    background: #e10600;
    color: #fff;

    padding: 10px 15px;

    border-radius: 5px;

    font-weight: bold;
    font-size: 12px;

    cursor: pointer;

    text-decoration: none;

    transition: .3s;
}

.btn:hover {
    background: #b80500;
}


/* ========================================
   STATUS VISUAL
======================================== */

.status-atual {
    display: inline-block;

    padding: 7px 11px;

    border-radius: 4px;

    font-size: 10px;
    font-weight: bold;

    text-transform: uppercase;
}

.pendente {
    background: #4a3900;
    color: #f0c000;
}

.processando {
    background: #102f4a;
    color: #42aaff;
}

.enviado {
    background: #182f1d;
    color: #35d46a;
}

.concluido {
    background: #123d20;
    color: #50e879;
}

.cancelado {
    background: #3d1111;
    color: #e10600;
}


/* ========================================
   VAZIO
======================================== */

.vazio {
    text-align: center;
    padding: 70px 20px;
    color: #777;
}

.vazio h2 {
    color: #fff;
    margin-bottom: 10px;
}


/* ========================================
   FOOTER
======================================== */

footer {
    background: #000;

    border-top: 1px solid #222;

    margin-top: 80px;

    padding: 35px;

    text-align: center;

    color: #777;
}

footer strong {
    color: #fff;
}

footer strong span {
    color: #e10600;
}


/* ========================================
   RESPONSIVO
======================================== */

@media (max-width: 1000px) {

    .estatisticas {
        grid-template-columns: repeat(3, 1fr);
    }

}

@media (max-width: 750px) {

    nav {
        flex-direction: column;
        gap: 15px;
    }

    .menu {
        flex-wrap: wrap;
        justify-content: center;
    }

    .estatisticas {
        grid-template-columns: repeat(2, 1fr);
    }

    .info-pedido {
        grid-template-columns: 1fr;
    }

    .pedido-cabecalho {
        flex-direction: column;
    }

    .status-form {
        flex-wrap: wrap;
    }

}

@media (max-width: 500px) {

    .container {
        width: 94%;
    }

    .estatisticas {
        grid-template-columns: 1fr;
    }

    .topo h1 {
        font-size: 34px;
    }

}

</style>

</head>

<body>


<!-- ========================================
     NAVBAR
======================================== -->

<nav>

    <div class="logo">
        BOXE <span>FIGHT</span>
    </div>


    <div class="menu">

        <a href="index.php">
            Início
        </a>

        <a href="dashboard.php">
            Dashboard
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

        <a href="pedidos_admin.php" class="ativo">
            Pedidos
        </a>

        <a href="logout.php" class="sair">
            Sair
        </a>

    </div>

</nav>


<div class="container">


    <!-- ========================================
         TÍTULO
    ======================================== -->

    <section class="topo">

        <span class="tag">
            Administração da loja
        </span>

        <h1>
            Gerenciar <span>Pedidos</span>
        </h1>

        <p>
            Acompanhe os pedidos realizados pelos usuários
            e atualize o status de cada compra.
        </p>

    </section>


    <!-- ========================================
         ESTATÍSTICAS
    ======================================== -->

    <section class="estatisticas">

        <div class="estatistica">

            <strong class="numero-amarelo">
                <?php echo $pendentes; ?>
            </strong>

            <span>
                Pendentes
            </span>

        </div>


        <div class="estatistica">

            <strong>
                <?php echo $processando; ?>
            </strong>

            <span>
                Processando
            </span>

        </div>


        <div class="estatistica">

            <strong class="numero-verde">
                <?php echo $enviados; ?>
            </strong>

            <span>
                Enviados
            </span>

        </div>


        <div class="estatistica">

            <strong class="numero-verde">
                <?php echo $concluidos; ?>
            </strong>

            <span>
                Concluídos
            </span>

        </div>


        <div class="estatistica">

            <strong class="numero-vermelho">
                <?php echo $cancelados; ?>
            </strong>

            <span>
                Cancelados
            </span>

        </div>

    </section>


    <!-- ========================================
         LISTA DE PEDIDOS
    ======================================== -->

    <section class="painel">


        <div class="painel-topo">

            <h2>
                Pedidos <span>recentes</span>
            </h2>

            <span class="admin-badge">
                ADMIN
            </span>

        </div>


        <?php if ($resultado && $resultado->num_rows > 0): ?>


            <?php while ($pedido = $resultado->fetch_assoc()): ?>


                <div class="pedido">


                    <!-- CABEÇALHO -->

                    <div class="pedido-cabecalho">

                        <div>

                            <div class="numero">
                                PEDIDO #<?php echo $pedido["id"]; ?>
                            </div>

                            <div class="cliente">

                                <?php

                                if (!empty($pedido["usuario"])) {

                                    echo htmlspecialchars(
                                        $pedido["usuario"]
                                    );

                                } else {

                                    echo "Usuário #" .
                                        intval($pedido["usuario_id"]);

                                }

                                ?>

                            </div>

                            <div class="usuario-id">

                                ID do usuário:
                                <?php echo $pedido["usuario_id"]; ?>

                            </div>

                        </div>


                        <div class="data">

                            <?php

                            echo date(
                                "d/m/Y H:i",
                                strtotime($pedido["data_pedido"])
                            );

                            ?>

                        </div>

                    </div>


                    <!-- INFORMAÇÕES -->

                    <div class="info-pedido">


                        <div class="info-box">

                            <span>
                                Valor total
                            </span>

                            <strong>

                                R$

                                <?php

                                echo number_format(
                                    $pedido["total"],
                                    2,
                                    ",",
                                    "."
                                );

                                ?>

                            </strong>

                        </div>


                        <div class="info-box">

                            <span>
                                Status atual
                            </span>


                            <?php

                            $classe_status = "";

                            switch ($pedido["status"]) {

                                case "Pendente":
                                    $classe_status = "pendente";
                                    break;

                                case "Processando":
                                    $classe_status = "processando";
                                    break;

                                case "Enviado":
                                    $classe_status = "enviado";
                                    break;

                                case "Concluído":
                                    $classe_status = "concluido";
                                    break;

                                case "Cancelado":
                                    $classe_status = "cancelado";
                                    break;

                                default:
                                    $classe_status = "";
                            }

                            ?>


                            <span
                                class="status-atual <?php echo $classe_status; ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $pedido["status"]
                                );
                                ?>

                            </span>

                        </div>


                        <div class="info-box">

                            <span>
                                ID do pedido
                            </span>

                            <strong>
                                #<?php echo $pedido["id"]; ?>
                            </strong>

                        </div>


                    </div>


                    <!-- ALTERAR STATUS -->

                    <form
                        action="atualizar_pedido.php"
                        method="POST"
                        class="status-form"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?php echo $pedido["id"]; ?>"
                        >


                        <label>
                            Alterar status:
                        </label>


                        <select
                            name="status"
                            required
                        >

                            <option
                                value="Pendente"
                                <?php
                                if ($pedido["status"] == "Pendente") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Pendente
                            </option>


                            <option
                                value="Processando"
                                <?php
                                if ($pedido["status"] == "Processando") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Processando
                            </option>


                            <option
                                value="Enviado"
                                <?php
                                if ($pedido["status"] == "Enviado") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Enviado
                            </option>


                            <option
                                value="Concluído"
                                <?php
                                if ($pedido["status"] == "Concluído") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Concluído
                            </option>


                            <option
                                value="Cancelado"
                                <?php
                                if ($pedido["status"] == "Cancelado") {
                                    echo "selected";
                                }
                                ?>
                            >
                                Cancelado
                            </option>

                        </select>


                        <button
                            type="submit"
                            class="btn"
                        >
                            Atualizar status
                        </button>

                    </form>


                </div>


            <?php endwhile; ?>


        <?php else: ?>


            <div class="vazio">

                <h2>
                    Nenhum pedido encontrado
                </h2>

                <p>
                    Ainda não existem pedidos registrados na loja.
                </p>

            </div>


        <?php endif; ?>


    </section>


</div>


<footer>

    <strong>
        BOXE <span>FIGHT</span>
    </strong>

    <p>
        Área administrativa da loja Boxe Fight.
    </p>

</footer>


</body>

</html>
