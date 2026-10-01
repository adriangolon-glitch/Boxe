<?php

session_start();
include("conexao.php");


/* =========================
   VERIFICAR LOGIN
========================= */

if (!isset($_SESSION["id"])) {

    header("Location: login.php");
    exit;

}


$id_usuario = intval($_SESSION["id"]);

$admin = isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "admin";


/* =========================
   BUSCAR USUÁRIO
========================= */

$sql_usuario = "
    SELECT id, usuario, tipo
    FROM usuarios
    WHERE id = ?
";

$stmt_usuario = $conexao->prepare($sql_usuario);

$stmt_usuario->bind_param("i", $id_usuario);

$stmt_usuario->execute();

$resultado_usuario = $stmt_usuario->get_result();


if ($resultado_usuario->num_rows == 0) {

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit;

}


$usuario = $resultado_usuario->fetch_assoc();


/* =========================
   BUSCAR PEDIDOS
========================= */

$sql_pedidos = "
    SELECT
        id,
        total,
        status,
        data_pedido
    FROM pedidos
    WHERE usuario_id = ?
    ORDER BY data_pedido DESC
";

$stmt_pedidos = $conexao->prepare($sql_pedidos);

$stmt_pedidos->bind_param("i", $id_usuario);

$stmt_pedidos->execute();

$resultado_pedidos = $stmt_pedidos->get_result();


/* =========================
   ESTATÍSTICAS
========================= */

$total_pedidos = 0;
$valor_total = 0;

$sql_stats = "
    SELECT
        COUNT(*) AS quantidade,
        COALESCE(SUM(total), 0) AS valor
    FROM pedidos
    WHERE usuario_id = ?
";

$stmt_stats = $conexao->prepare($sql_stats);

$stmt_stats->bind_param("i", $id_usuario);

$stmt_stats->execute();

$resultado_stats = $stmt_stats->get_result();

$stats = $resultado_stats->fetch_assoc();

$total_pedidos = $stats["quantidade"];
$valor_total = $stats["valor"];


/* =========================
   FUNÇÃO STATUS
========================= */

function classeStatus($status)
{

    if ($status == "Concluído") {

        return "concluido";

    }

    if ($status == "Cancelado") {

        return "cancelado";

    }

    if ($status == "Enviado") {

        return "enviado";

    }

    if ($status == "Processando") {

        return "processando";

    }

    return "pendente";

}

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
        Meu Perfil - Boxe Fight
    </title>


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
           HERO
        ========================= */

        .hero {

            min-height: 300px;

            background:

                linear-gradient(
                    rgba(0,0,0,.72),
                    rgba(0,0,0,.92)
                ),

                url("imagens/boxe1.jpg")
                center/cover;

            display: flex;

            justify-content: center;

            align-items: center;

            text-align: center;

            padding: 50px 20px;

        }


        .hero-conteudo {

            max-width: 800px;

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

            margin-bottom: 18px;

        }


        .hero h1 {

            font-size: 52px;

            font-weight: 900;

            text-transform: uppercase;

        }


        .hero h1 span {

            color: #e10600;

        }


        .hero p {

            color: #aaa;

            margin-top: 12px;

        }


        /* =========================
           CONTAINER
        ========================= */

        .container {

            width: 90%;

            max-width: 1200px;

            margin: -50px auto 70px;

            position: relative;

        }


        /* =========================
           PERFIL PRINCIPAL
        ========================= */

        .perfil {

            background: #151515;

            border: 1px solid #292929;

            border-radius: 12px;

            overflow: hidden;

            box-shadow: 0 20px 60px rgba(0,0,0,.45);

        }


        .perfil-topo {

            background: #101010;

            border-bottom: 1px solid #292929;

            padding: 30px;

            display: flex;

            align-items: center;

            gap: 25px;

        }


        .avatar {

            width: 100px;

            height: 100px;

            border-radius: 50%;

            background: #e10600;

            display: flex;

            justify-content: center;

            align-items: center;

            font-size: 40px;

            font-weight: 900;

            border: 5px solid #292929;

            box-shadow: 0 0 30px rgba(225,6,0,.18);

        }


        .perfil-nome h2 {

            font-size: 30px;

            text-transform: uppercase;

        }


        .perfil-nome p {

            color: #888;

            margin-top: 7px;

        }


        .badge {

            display: inline-block;

            margin-top: 12px;

            padding: 6px 12px;

            border-radius: 4px;

            font-size: 10px;

            font-weight: bold;

            text-transform: uppercase;

        }


        .badge-admin {

            background: #e10600;

            color: white;

        }


        .badge-usuario {

            background: #333;

            color: #ddd;

        }


        /* =========================
           INFORMAÇÕES
        ========================= */

        .informacoes {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

        }


        .info {

            padding: 25px 30px;

            border-bottom: 1px solid #292929;

        }


        .info:nth-child(odd) {

            border-right: 1px solid #292929;

        }


        .info span {

            display: block;

            color: #777;

            font-size: 11px;

            text-transform: uppercase;

            margin-bottom: 8px;

        }


        .info strong {

            font-size: 17px;

        }


        /* =========================
           ESTATÍSTICAS
        ========================= */

        .estatisticas {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;

            margin-top: 25px;

        }


        .stat {

            background: #151515;

            border: 1px solid #292929;

            border-radius: 10px;

            padding: 25px;

            text-align: center;

            transition: .3s;

        }


        .stat:hover {

            border-color: #e10600;

            transform: translateY(-3px);

        }


        .stat-numero {

            color: #e10600;

            font-size: 35px;

            font-weight: 900;

        }


        .stat p {

            color: #888;

            font-size: 12px;

            text-transform: uppercase;

            margin-top: 7px;

        }


        /* =========================
           PEDIDOS
        ========================= */

        .secao {

            margin-top: 35px;

        }


        .titulo-secao {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 18px;

        }


        .titulo-secao h2 {

            font-size: 24px;

            text-transform: uppercase;

        }


        .titulo-secao h2 span {

            color: #e10600;

        }


        .ver-todos {

            color: #e10600;

            text-decoration: none;

            font-size: 12px;

            font-weight: bold;

        }


        .ver-todos:hover {

            text-decoration: underline;

        }


        .pedidos {

            display: flex;

            flex-direction: column;

            gap: 12px;

        }


        .pedido {

            background: #151515;

            border: 1px solid #292929;

            border-radius: 8px;

            padding: 20px;

            display: grid;

            grid-template-columns: 80px 1fr 150px 120px;

            align-items: center;

            gap: 20px;

        }


        .pedido-id {

            color: #e10600;

            font-weight: 900;

        }


        .pedido-info strong {

            display: block;

            font-size: 15px;

        }


        .pedido-info span {

            display: block;

            color: #777;

            font-size: 11px;

            margin-top: 5px;

        }


        .pedido-valor {

            font-weight: bold;

            text-align: center;

        }


        .status {

            padding: 7px 10px;

            border-radius: 4px;

            font-size: 10px;

            font-weight: bold;

            text-align: center;

            text-transform: uppercase;

        }


        .pendente {

            background: #3b3200;

            color: #ffd83d;

        }


        .processando {

            background: #222b3b;

            color: #8db8ff;

        }


        .enviado {

            background: #132f25;

            color: #63d9a2;

        }


        .concluido {

            background: #10391f;

            color: #65e58b;

        }


        .cancelado {

            background: #3d1111;

            color: #ff7777;

        }


        .sem-pedidos {

            background: #151515;

            border: 1px dashed #333;

            border-radius: 10px;

            padding: 50px 20px;

            text-align: center;

        }


        .sem-pedidos h3 {

            margin-bottom: 8px;

        }


        .sem-pedidos p {

            color: #777;

            margin-bottom: 20px;

        }


        /* =========================
           AÇÕES
        ========================= */

        .acoes {

            display: flex;

            justify-content: center;

            gap: 15px;

            margin-top: 30px;

        }


        .btn {

            display: inline-block;

            background: #e10600;

            color: white;

            text-decoration: none;

            padding: 13px 22px;

            border-radius: 5px;

            font-size: 13px;

            font-weight: bold;

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

        .admin-box {

            margin-top: 35px;

            background:

                linear-gradient(
                    135deg,
                    #190000,
                    #110000
                );

            border: 1px solid #5b0000;

            border-radius: 10px;

            padding: 25px;

        }


        .admin-box h2 {

            color: #e10600;

            margin-bottom: 8px;

            text-transform: uppercase;

        }


        .admin-box p {

            color: #999;

            font-size: 13px;

            margin-bottom: 20px;

        }


        .admin-links {

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

        }


        .admin-links a {

            background: #e10600;

            color: white;

            text-decoration: none;

            padding: 10px 15px;

            border-radius: 4px;

            font-size: 11px;

            font-weight: bold;

        }


        .admin-links a:hover {

            background: #b80500;

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


            .pedido {

                grid-template-columns: 1fr 1fr;

            }


            .pedido-valor {

                text-align: left;

            }

        }


        @media (max-width: 600px) {

            .hero h1 {

                font-size: 36px;

            }


            .container {

                width: 94%;

                margin-top: -30px;

            }


            .perfil-topo {

                flex-direction: column;

                text-align: center;

            }


            .informacoes {

                grid-template-columns: 1fr;

            }


            .info:nth-child(odd) {

                border-right: none;

            }


            .estatisticas {

                grid-template-columns: 1fr;

            }


            .pedido {

                grid-template-columns: 1fr;

                text-align: center;

            }


            .pedido-valor {

                text-align: center;

            }


            .titulo-secao {

                flex-direction: column;

                align-items: flex-start;

                gap: 8px;

            }


            .acoes {

                flex-direction: column;

            }


            .acoes .btn {

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

        <a href="perfil.php">
            Perfil
        </a>


        <?php if ($admin) { ?>

            <a href="dashboard.php">
                Admin
            </a>

        <?php } ?>


        <a href="logout.php" class="sair">
            Sair
        </a>

    </div>

</nav>


<!-- =========================
     HERO
========================= -->

<section class="hero">

    <div class="hero-conteudo">

        <div class="tag">
            BOXE FIGHT • CONTA
        </div>

        <h1>
            MEU <span>PERFIL</span>
        </h1>

        <p>
            Gerencie sua conta e acompanhe suas atividades.
        </p>

    </div>

</section>


<!-- =========================
     CONTAINER
========================= -->

<div class="container">


    <!-- =========================
         PERFIL
    ========================= -->

    <div class="perfil">


        <div class="perfil-topo">


            <div class="avatar">

                <?php

                echo strtoupper(
                    substr($usuario["usuario"], 0, 1)
                );

                ?>

            </div>


            <div class="perfil-nome">

                <h2>

                    <?php
                    echo htmlspecialchars(
                        $usuario["usuario"]
                    );
                    ?>

                </h2>


                <p>
                    Membro do Boxe Fight
                </p>


                <?php if ($admin) { ?>

                    <span class="badge badge-admin">
                        Administrador
                    </span>

                <?php } else { ?>

                    <span class="badge badge-usuario">
                        Usuário
                    </span>

                <?php } ?>

            </div>


        </div>


        <div class="informacoes">


            <div class="info">

                <span>
                    ID da conta
                </span>

                <strong>
                    #<?php echo $usuario["id"]; ?>
                </strong>

            </div>


            <div class="info">

                <span>
                    Nome de usuário
                </span>

                <strong>
                    <?php
                    echo htmlspecialchars(
                        $usuario["usuario"]
                    );
                    ?>
                </strong>

            </div>


            <div class="info">

                <span>
                    Tipo de conta
                </span>

                <strong>

                    <?php

                    echo $admin
                        ? "Administrador"
                        : "Usuário";

                    ?>

                </strong>

            </div>


            <div class="info">

                <span>
                    Acesso
                </span>

                <strong>

                    <?php

                    echo $admin
                        ? "Área administrativa"
                        : "Área do usuário";

                    ?>

                </strong>

            </div>


        </div>

    </div>


    <!-- =========================
         ESTATÍSTICAS
    ========================= -->

    <div class="estatisticas">


        <div class="stat">

            <div class="stat-numero">

                <?php
                echo $total_pedidos;
                ?>

            </div>

            <p>
                Pedidos realizados
            </p>

        </div>


        <div class="stat">

            <div class="stat-numero">

                R$

                <?php

                echo number_format(
                    $valor_total,
                    2,
                    ",",
                    "."
                );

                ?>

            </div>

            <p>
                Total em compras
            </p>

        </div>


    </div>


    <!-- =========================
         PEDIDOS
    ========================= -->

    <section class="secao">


        <div class="titulo-secao">

            <h2>
                Meus <span>Pedidos</span>
            </h2>


            <a
                href="meus_pedidos.php"
                class="ver-todos"
            >
                VER TODOS →
            </a>

        </div>


        <?php if ($resultado_pedidos->num_rows > 0) { ?>


            <div class="pedidos">


                <?php

                $contador = 0;

                while (
                    $pedido = $resultado_pedidos->fetch_assoc()
                ) {

                    if ($contador >= 5) {
                        break;
                    }

                    $contador++;

                ?>


                    <div class="pedido">


                        <div class="pedido-id">

                            #<?php
                            echo $pedido["id"];
                            ?>

                        </div>


                        <div class="pedido-info">

                            <strong>
                                Pedido realizado
                            </strong>

                            <span>

                                <?php

                                echo date(
                                    "d/m/Y H:i",
                                    strtotime(
                                        $pedido["data_pedido"]
                                    )
                                );

                                ?>

                            </span>

                        </div>


                        <div class="pedido-valor">

                            R$

                            <?php

                            echo number_format(
                                $pedido["total"],
                                2,
                                ",",
                                "."
                            );

                            ?>

                        </div>


                        <div
                            class="status
                            <?php
                            echo classeStatus(
                                $pedido["status"]
                            );
                            ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $pedido["status"]
                            );
                            ?>

                        </div>


                    </div>


                <?php } ?>


            </div>


        <?php } else { ?>


            <div class="sem-pedidos">

                <h3>
                    Você ainda não fez nenhum pedido.
                </h3>

                <p>
                    Confira nossa loja e encontre os equipamentos
                    para seus treinos.
                </p>

                <a
                    href="loja.php"
                    class="btn"
                >
                    IR PARA A LOJA
                </a>

            </div>


        <?php } ?>


    </section>


    <!-- =========================
         ADMIN
    ========================= -->

    <?php if ($admin) { ?>


        <div class="admin-box">

            <h2>
                Área Administrativa
            </h2>

            <p>
                Sua conta possui acesso total ao gerenciamento
                do sistema Boxe Fight.
            </p>


            <div class="admin-links">

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="lutadores.php">
                    Lutadores
                </a>

                <a href="lutas.php">
                    Lutas
                </a>

                <a href="usuarios.php">
                    Usuários
                </a>

                <a href="produtos_admin.php">
                    Produtos
                </a>

                <a href="pedidos.php">
                    Pedidos
                </a>

            </div>

        </div>


    <?php } ?>


    <!-- =========================
         AÇÕES
    ========================= -->

    <div class="acoes">

        <a
            href="meus_pedidos.php"
            class="btn"
        >
            Ver Meus Pedidos
        </a>


        <a
            href="loja.php"
            class="btn btn-secundario"
        >
            Ir para Loja
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

$stmt_usuario->close();
$stmt_pedidos->close();
$stmt_stats->close();

$conexao->close();

?>
