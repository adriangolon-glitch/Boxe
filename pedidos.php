<?php
session_start();
include("conexao.php");

/* =========================
   VERIFICAR ADMIN
========================= */

if (
    !isset($_SESSION["id"]) ||
    !isset($_SESSION["tipo"]) ||
    $_SESSION["tipo"] != "admin"
) {
    header("Location: login.php");
    exit;
}


/* =========================
   ATUALIZAR STATUS
========================= */

if (
    isset($_POST["atualizar_status"]) &&
    isset($_POST["pedido_id"]) &&
    isset($_POST["status"])
) {

    $pedido_id = intval($_POST["pedido_id"]);
    $status = $_POST["status"];

    $status_permitidos = [
        "Pendente",
        "Processando",
        "Enviado",
        "Concluído",
        "Cancelado"
    ];

    if (in_array($status, $status_permitidos)) {

        $sql = "
            UPDATE pedidos
            SET status = ?
            WHERE id = ?
        ";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $status, $pedido_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: pedidos.php");
    exit;
}


/* =========================
   BUSCAR PEDIDOS
========================= */

$sql = "
    SELECT
        p.id,
        p.usuario_id,
        p.total,
        p.status,
        p.data_pedido,
        u.usuario
    FROM pedidos p
    INNER JOIN usuarios u
        ON p.usuario_id = u.id
    ORDER BY p.data_pedido DESC, p.id DESC
";

$resultado = $conexao->query($sql);


/* =========================
   CONTADORES
========================= */

$total_pedidos = 0;
$pendentes = 0;
$processando = 0;
$enviados = 0;
$concluidos = 0;

$consulta = $conexao->query("
    SELECT status, COUNT(*) AS total
    FROM pedidos
    GROUP BY status
");

while ($status = $consulta->fetch_assoc()) {

    $total_pedidos += $status["total"];

    if ($status["status"] == "Pendente") {
        $pendentes = $status["total"];
    }

    if ($status["status"] == "Processando") {
        $processando = $status["total"];
    }

    if ($status["status"] == "Enviado") {
        $enviados = $status["total"];
    }

    if ($status["status"] == "Concluído") {
        $concluidos = $status["total"];
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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
    gap: 22px;
    align-items: center;
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

.menu .ativo {
    color: #e10600;
}

.menu .sair {
    color: #e10600;
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
   TOPO
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
    text-transform: uppercase;
    margin-top: 10px;
}

.topo h1 span {
    color: #e10600;
}

.topo p {
    color: #888;
    margin-top: 10px;
}


/* =========================
   ESTATÍSTICAS
========================= */

.stats {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-bottom: 35px;
}

.stat {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 9px;
    padding: 22px;

    transition: 0.3s;
}

.stat:hover {
    border-color: #e10600;
    transform: translateY(-3px);
}

.stat strong {
    display: block;
    font-size: 30px;
    margin-bottom: 6px;
}

.stat span {
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


/* =========================
   TABELA
========================= */

.tabela-area {
    background: #151515;
    border: 1px solid #292929;
    border-radius: 10px;
    overflow: hidden;
}

.tabela-topo {
    padding: 20px;

    background: #111;
    border-bottom: 1px solid #292929;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tabela-topo strong {
    text-transform: uppercase;
    font-size: 15px;
}

.tabela-topo span {
    color: #777;
    font-size: 12px;
}

.tabela {
    width: 100%;
    border-collapse: collapse;
}

.tabela th {
    text-align: left;
    padding: 16px 18px;

    background: #0f0f0f;

    color: #888;

    font-size: 11px;
    text-transform: uppercase;

    border-bottom: 1px solid #292929;
}

.tabela td {
    padding: 17px 18px;

    border-bottom: 1px solid #242424;

    font-size: 13px;
}

.tabela tr:last-child td {
    border-bottom: none;
}

.tabela tr:hover td {
    background: #191919;
}


/* =========================
   ID PEDIDO
========================= */

.id-pedido {
    color: #e10600;
    font-weight: bold;
}


/* =========================
   CLIENTE
========================= */

.cliente strong {
    display: block;
    margin-bottom: 4px;
}

.cliente small {
    color: #777;
}


/* =========================
   TOTAL
========================= */

.total {
    font-weight: bold;
    font-size: 15px;
}


/* =========================
   STATUS
========================= */

.status-badge {
    display: inline-block;

    padding: 7px 10px;

    border-radius: 4px;

    font-size: 10px;
    font-weight: bold;

    text-transform: uppercase;
}

.status-pendente {
    background: #3a2f08;
    color: #f0c000;
}

.status-processando {
    background: #292929;
    color: #fff;
}

.status-enviado {
    background: #102b38;
    color: #48bde8;
}

.status-concluido {
    background: #10331b;
    color: #35d46a;
}

.status-cancelado {
    background: #381313;
    color: #e10600;
}


/* =========================
   SELECT
========================= */

.form-status {
    display: flex;
    gap: 7px;
    align-items: center;
}

select {
    background: #0c0c0c;
    color: #fff;

    border: 1px solid #333;
    border-radius: 4px;

    padding: 8px;

    outline: none;
}

select:focus {
    border-color: #e10600;
}

.btn-status {
    border: none;

    background: #e10600;
    color: #fff;

    padding: 8px 10px;

    border-radius: 4px;

    cursor: pointer;

    font-size: 11px;
    font-weight: bold;
}

.btn-status:hover {
    background: #b80500;
}


/* =========================
   VAZIO
========================= */

.vazio {
    text-align: center;
    padding: 70px 20px;
    color: #777;
}

.vazio h2 {
    color: #fff;
    margin-bottom: 10px;
}


/* =========================
   VOLTAR
========================= */

.voltar {
    margin-top: 25px;
}

.voltar a {
    display: inline-block;

    color: #aaa;

    text-decoration: none;

    border: 1px solid #333;

    padding: 10px 15px;

    border-radius: 5px;

    font-size: 12px;
    font-weight: bold;
}

.voltar a:hover {
    color: #fff;
    border-color: #e10600;
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

    .tabela-area {
        overflow-x: auto;
    }

    .tabela {
        min-width: 950px;
    }

}

@media (max-width: 750px) {

    .menu {
        display: none;
    }

    .container {
        width: 92%;
    }

    .stats {
        grid-template-columns: repeat(2, 1fr);
    }

    .topo h1 {
        font-size: 36px;
    }

}

@media (max-width: 500px) {

    .stats {
        grid-template-columns: 1fr;
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

        <a href="pedidos.php" class="ativo">
            Pedidos
        </a>

        <a href="logout.php" class="sair">
            Sair
        </a>

    </div>

</nav>


<div class="container">


    <!-- =========================
         TOPO
    ========================= -->

    <section class="topo">

        <span class="tag">
            Administração
        </span>

        <h1>
            Gerenciar <span>Pedidos</span>
        </h1>

        <p>
            Acompanhe as compras realizadas na loja e
            atualize o status de cada pedido.
        </p>

    </section>


    <!-- =========================
         ESTATÍSTICAS
    ========================= -->

    <section class="stats">

        <div class="stat">

            <strong>
                <?php echo $total_pedidos; ?>
            </strong>

            <span>
                Total de pedidos
            </span>

        </div>


        <div class="stat">

            <strong class="numero-amarelo">
                <?php echo $pendentes; ?>
            </strong>

            <span>
                Pendentes
            </span>

        </div>


        <div class="stat">

            <strong>
                <?php echo $processando; ?>
            </strong>

            <span>
                Processando
            </span>

        </div>


        <div class="stat">

            <strong>
                <?php echo $enviados; ?>
            </strong>

            <span>
                Enviados
            </span>

        </div>


        <div class="stat">

            <strong class="numero-verde">
                <?php echo $concluidos; ?>
            </strong>

            <span>
                Concluídos
            </span>

        </div>

    </section>


    <!-- =========================
         PEDIDOS
    ========================= -->

    <div class="tabela-area">

        <div class="tabela-topo">

            <strong>
                Pedidos registrados
            </strong>

            <span>
                <?php echo $total_pedidos; ?> pedido(s)
            </span>

        </div>


        <?php if ($resultado && $resultado->num_rows > 0): ?>

            <table class="tabela">

                <thead>

                    <tr>

                        <th>
                            Pedido
                        </th>

                        <th>
                            Cliente
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Data
                        </th>

                        <th>
                            Status atual
                        </th>

                        <th>
                            Alterar status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($pedido = $resultado->fetch_assoc()): ?>

                        <?php

                        $classe_status = "status-processando";

                        if ($pedido["status"] == "Pendente") {
                            $classe_status = "status-pendente";
                        }

                        elseif ($pedido["status"] == "Enviado") {
                            $classe_status = "status-enviado";
                        }

                        elseif ($pedido["status"] == "Concluído") {
                            $classe_status = "status-concluido";
                        }

                        elseif ($pedido["status"] == "Cancelado") {
                            $classe_status = "status-cancelado";
                        }

                        ?>

                        <tr>


                            <!-- ID -->

                            <td>

                                <span class="id-pedido">

                                    #<?php
                                    echo $pedido["id"];
                                    ?>

                                </span>

                            </td>


                            <!-- CLIENTE -->

                            <td>

                                <div class="cliente">

                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $pedido["usuario"]
                                        );
                                        ?>

                                    </strong>

                                    <small>

                                        Usuário #
                                        <?php
                                        echo $pedido["usuario_id"];
                                        ?>

                                    </small>

                                </div>

                            </td>


                            <!-- TOTAL -->

                            <td>

                                <span class="total">

                                    R$
                                    <?php
                                    echo number_format(
                                        $pedido["total"],
                                        2,
                                        ",",
                                        "."
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- DATA -->

                            <td>

                                <?php

                                echo date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $pedido["data_pedido"]
                                    )
                                );

                                ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="status-badge <?php echo $classe_status; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $pedido["status"]
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- ALTERAR -->

                            <td>

                                <form
                                    method="POST"
                                    class="form-status"
                                >

                                    <input
                                        type="hidden"
                                        name="pedido_id"
                                        value="<?php echo $pedido["id"]; ?>"
                                    >


                                    <select name="status">

                                        <option
                                            value="Pendente"
                                            <?php
                                            if (
                                                $pedido["status"]
                                                ==
                                                "Pendente"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Pendente
                                        </option>


                                        <option
                                            value="Processando"
                                            <?php
                                            if (
                                                $pedido["status"]
                                                ==
                                                "Processando"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Processando
                                        </option>


                                        <option
                                            value="Enviado"
                                            <?php
                                            if (
                                                $pedido["status"]
                                                ==
                                                "Enviado"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Enviado
                                        </option>


                                        <option
                                            value="Concluído"
                                            <?php
                                            if (
                                                $pedido["status"]
                                                ==
                                                "Concluído"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Concluído
                                        </option>


                                        <option
                                            value="Cancelado"
                                            <?php
                                            if (
                                                $pedido["status"]
                                                ==
                                                "Cancelado"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Cancelado
                                        </option>

                                    </select>


                                    <button
                                        type="submit"
                                        name="atualizar_status"
                                        class="btn-status"
                                    >
                                        Salvar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="vazio">

                <h2>
                    Nenhum pedido encontrado
                </h2>

                <p>
                    Ainda não existem pedidos realizados na loja.
                </p>

            </div>

        <?php endif; ?>

    </div>


    <!-- =========================
         VOLTAR
    ========================= -->

    <div class="voltar">

        <a href="dashboard.php">
            ← Voltar para o Dashboard
        </a>

    </div>


</div>


<!-- =========================
     FOOTER
========================= -->

<footer>

    <strong>
        BOXE<span>FIGHT</span>
    </strong>

    <p>
        Administração da loja Boxe Fight.
    </p>

</footer>


</body>

</html>
