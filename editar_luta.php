<?php
session_start();
include("conexao.php");

if (!isset($_SESSION["id"]) || !isset($_SESSION["tipo"]) || $_SESSION["tipo"] != "admin") {
    header("Location: login.php");
    exit;
}

/* =========================
   ESTATÍSTICAS
========================= */

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM usuarios");
$usuarios = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM lutadores");
$lutadores = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM lutas");
$lutas = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM produtos");
$produtos = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("SELECT COUNT(*) AS total FROM pedidos");
$pedidos = $resultado->fetch_assoc()["total"];

$resultado = $conexao->query("
    SELECT COUNT(*) AS total
    FROM pedidos
    WHERE status = 'Pendente'
");
$pendentes = $resultado->fetch_assoc()["total"];


/* =========================
   VALOR TOTAL DOS PEDIDOS
========================= */

$resultado = $conexao->query("
    SELECT COALESCE(SUM(total), 0) AS valor
    FROM pedidos
    WHERE status != 'Cancelado'
");

$valor_vendas = $resultado->fetch_assoc()["valor"];


/* =========================
   ÚLTIMOS PEDIDOS
========================= */

$sql_pedidos = "
    SELECT 
        p.id,
        p.total,
        p.status,
        p.data_pedido,
        u.usuario
    FROM pedidos p
    INNER JOIN usuarios u ON p.usuario_id = u.id
    ORDER BY p.data_pedido DESC
    LIMIT 5
";

$ultimos_pedidos = $conexao->query($sql_pedidos);


/* =========================
   ÚLTIMOS LUTADORES
========================= */

$sql_lutadores = "
    SELECT id, nome, categoria, foto
    FROM lutadores
    ORDER BY id DESC
    LIMIT 5
";

$ultimos_lutadores = $conexao->query($sql_lutadores);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Boxe Fight</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #0a0a0a;
            color: white;
        }

        /* =========================
           NAVBAR
        ========================= */

        nav {
            background: #000;
            border-bottom: 1px solid #292929;
            padding: 18px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            gap: 18px;
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
            padding: 10px 16px;
            border-radius: 5px;
            color: white !important;
        }

        .sair:hover {
            background: #b80500 !important;
        }

        /* =========================
           CONTAINER
        ========================= */

        .container {
            width: 90%;
            max-width: 1400px;
            margin: 45px auto;
        }

        .cabecalho {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .cabecalho h1 {
            font-size: 42px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .cabecalho h1 span {
            color: #e10600;
        }

        .cabecalho p {
            color: #999;
            margin-top: 8px;
        }

        .admin-badge {
            background: #e10600;
            color: white;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* =========================
           CARDS
        ========================= */

        .estatisticas {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 9px;
            padding: 23px;
            transition: .3s;
        }

        .stat:hover {
            transform: translateY(-4px);
            border-color: #e10600;
        }

        .stat .titulo {
            color: #999;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .stat .numero {
            display: block;
            color: white;
            font-size: 32px;
            font-weight: 900;
            margin-top: 10px;
        }

        .stat.vermelho .numero {
            color: #e10600;
        }

        .stat .descricao {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }

        /* =========================
           GRID PRINCIPAL
        ========================= */

        .grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 25px;
        }

        .card {
            background: #151515;
            border: 1px solid #292929;
            border-radius: 10px;
            padding: 25px;
        }

        .card-titulo {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .card-titulo h2 {
            font-size: 20px;
            text-transform: uppercase;
        }

        .card-titulo h2 span {
            color: #e10600;
        }

        .ver-tudo {
            color: #e10600;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .ver-tudo:hover {
            text-decoration: underline;
        }

        /* =========================
           PEDIDOS
        ========================= */

        .pedido {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #292929;
        }

        .pedido:last-child {
            border-bottom: none;
        }

        .pedido h3 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .pedido p {
            color: #777;
            font-size: 12px;
        }

        .pedido-direita {
            text-align: right;
        }

        .pedido-valor {
            color: #e10600;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .status {
            display: inline-block;
            background: #292929;
            color: #ddd;
            padding: 5px 9px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }

        /* =========================
           LUTADORES
        ========================= */

        .lutador {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 13px 0;
            border-bottom: 1px solid #292929;
        }

        .lutador:last-child {
            border-bottom: none;
        }

        .lutador img {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #333;
        }

        .sem-foto {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: #292929;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e10600;
            font-size: 20px;
            font-weight: 900;
        }

        .lutador-info h3 {
            font-size: 14px;
        }

        .lutador-info p {
            color: #777;
            font-size: 12px;
            margin-top: 5px;
        }

        /* =========================
           ATALHOS
        ========================= */

        .atalhos {
            margin-top: 25px;
        }

        .atalhos-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .atalho {
            background: #111;
            border: 1px solid #292929;
            border-radius: 8px;
            padding: 23px;
            text-decoration: none;
            color: white;
            transition: .3s;
        }

        .atalho:hover {
            border-color: #e10600;
            transform: translateY(-4px);
        }

        .icone {
            width: 45px;
            height: 45px;
            background: #e10600;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 15px;
        }

        .atalho h3 {
            font-size: 15px;
            margin-bottom: 6px;
        }

        .atalho p {
            color: #777;
            font-size: 12px;
            line-height: 1.5;
        }

        /* =========================
           ALERTA
        ========================= */

        .alerta {
            margin-top: 25px;
            background: #180505;
            border: 1px solid #570000;
            border-left: 5px solid #e10600;
            border-radius: 7px;
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alerta h3 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .alerta p {
            color: #999;
            font-size: 12px;
        }

        .btn-alerta {
            background: #e10600;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-alerta:hover {
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

        @media (max-width: 1200px) {

            .estatisticas {
                grid-template-columns: repeat(3, 1fr);
            }

            .atalhos-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 900px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .menu {
                gap: 10px;
            }

            .menu a:nth-child(1),
            .menu a:nth-child(2),
            .menu a:nth-child(3),
            .menu a:nth-child(4) {
                display: none;
            }

        }

        @media (max-width: 600px) {

            .container {
                width: 94%;
            }

            .cabecalho {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .cabecalho h1 {
                font-size: 30px;
            }

            .estatisticas {
                grid-template-columns: repeat(2, 1fr);
            }

            .atalhos-grid {
                grid-template-columns: 1fr;
            }

            .alerta {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

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
        <a href="perfil.php">Perfil</a>
        <a href="pedidos.php">Pedidos</a>
        <a href="logout.php" class="sair">Sair</a>

    </div>

</nav>


<div class="container">


    <!-- CABEÇALHO -->

    <div class="cabecalho">

        <div>

            <h1>
                Painel <span>Admin</span>
            </h1>

            <p>
                Controle completo do sistema Boxe Fight.
            </p>

        </div>

        <div class="admin-badge">
            Administrador
        </div>

    </div>


    <!-- ESTATÍSTICAS -->

    <div class="estatisticas">


        <div class="stat vermelho">

            <span class="titulo">
                Usuários
            </span>

            <span class="numero">
                <?php echo $usuarios; ?>
            </span>

            <span class="descricao">
                Contas cadastradas
            </span>

        </div>


        <div class="stat">

            <span class="titulo">
                Lutadores
            </span>

            <span class="numero">
                <?php echo $lutadores; ?>
            </span>

            <span class="descricao">
                Atletas cadastrados
            </span>

        </div>


        <div class="stat">

            <span class="titulo">
                Lutas
            </span>

            <span class="numero">
                <?php echo $lutas; ?>
            </span>

            <span class="descricao">
                Confrontos registrados
            </span>

        </div>


        <div class="stat">

            <span class="titulo">
                Produtos
            </span>

            <span class="numero">
                <?php echo $produtos; ?>
            </span>

            <span class="descricao">
                Produtos na loja
            </span>

        </div>


        <div class="stat">

            <span class="titulo">
                Pedidos
            </span>

            <span class="numero">
                <?php echo $pedidos; ?>
            </span>

            <span class="descricao">
                Pedidos realizados
            </span>

        </div>


        <div class="stat vermelho">

            <span class="titulo">
                Pendentes
            </span>

            <span class="numero">
                <?php echo $pendentes; ?>
            </span>

            <span class="descricao">
                Aguardando processamento
            </span>

        </div>

    </div>


    <!-- GRID -->

    <div class="grid">


        <!-- PEDIDOS -->

        <div class="card">

            <div class="card-titulo">

                <h2>
                    Últimos <span>Pedidos</span>
                </h2>

                <a href="pedidos.php" class="ver-tudo">
                    VER TODOS
                </a>

            </div>


            <?php if ($ultimos_pedidos->num_rows > 0) { ?>

                <?php while ($pedido = $ultimos_pedidos->fetch_assoc()) { ?>

                    <div class="pedido">

                        <div>

                            <h3>
                                Pedido #<?php echo $pedido["id"]; ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($pedido["usuario"]); ?>
                                •
                                <?php
                                echo date(
                                    "d/m/Y H:i",
                                    strtotime($pedido["data_pedido"])
                                );
                                ?>
                            </p>

                        </div>


                        <div class="pedido-direita">

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

                            <span class="status">
                                <?php echo htmlspecialchars($pedido["status"]); ?>
                            </span>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <p style="color:#777;">
                    Nenhum pedido encontrado.
                </p>

            <?php } ?>

        </div>


        <!-- LUTADORES -->

        <div class="card">

            <div class="card-titulo">

                <h2>
                    Novos <span>Lutadores</span>
                </h2>

                <a href="lutadores.php" class="ver-tudo">
                    VER TODOS
                </a>

            </div>


            <?php if ($ultimos_lutadores->num_rows > 0) { ?>

                <?php while ($lutador = $ultimos_lutadores->fetch_assoc()) { ?>

                    <div class="lutador">


                        <?php if (!empty($lutador["foto"])) { ?>

                            <img
                                src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                                alt="<?php echo htmlspecialchars($lutador["nome"]); ?>"
                            >

                        <?php } else { ?>

                            <div class="sem-foto">
                                <?php
                                echo strtoupper(
                                    substr($lutador["nome"], 0, 1)
                                );
                                ?>
                            </div>

                        <?php } ?>


                        <div class="lutador-info">

                            <h3>
                                <?php echo htmlspecialchars($lutador["nome"]); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($lutador["categoria"]); ?>
                            </p>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <p style="color:#777;">
                    Nenhum lutador cadastrado.
                </p>

            <?php } ?>

        </div>

    </div>


    <!-- ALERTA -->

    <?php if ($pendentes > 0) { ?>

        <div class="alerta">

            <div>

                <h3>
                    Existem <?php echo $pendentes; ?> pedido(s) pendente(s).
                </h3>

                <p>
                    Acesse o gerenciamento de pedidos para atualizar os status.
                </p>

            </div>

            <a href="pedidos.php" class="btn-alerta">
                GERENCIAR PEDIDOS
            </a>

        </div>

    <?php } ?>


    <!-- ATALHOS -->

    <div class="atalhos">

        <div class="card-titulo">

            <h2>
                Acesso <span>Rápido</span>
            </h2>

        </div>


        <div class="atalhos-grid">


            <a href="cadastrar_lutador.php" class="atalho">

                <div class="icone">
                    +
                </div>

                <h3>
                    Novo Lutador
                </h3>

                <p>
                    Cadastre um novo atleta no sistema.
                </p>

            </a>


            <a href="cadastrar_luta.php" class="atalho">

                <div class="icone">
                    VS
                </div>

                <h3>
                    Nova Luta
                </h3>

                <p>
                    Registre um novo confronto.
                </p>

            </a>


            <a href="produtos_admin.php" class="atalho">

                <div class="icone">
                    $
                </div>

                <h3>
                    Produtos
                </h3>

                <p>
                    Gerencie os produtos da loja.
                </p>

            </a>


            <a href="usuarios.php" class="atalho">

                <div class="icone">
                    U
                </div>

                <h3>
                    Usuários
                </h3>

                <p>
                    Gerencie as contas cadastradas.
                </p>

            </a>


            <a href="pedidos.php" class="atalho">

                <div class="icone">
                    #
                </div>

                <h3>
                    Pedidos
                </h3>

                <p>
                    Controle todas as compras realizadas.
                </p>

            </a>


            <a href="lutadores.php" class="atalho">

                <div class="icone">
                    L
                </div>

                <h3>
                    Lutadores
                </h3>

                <p>
                    Edite ou exclua atletas.
                </p>

            </a>


            <a href="lutas.php" class="atalho">

                <div class="icone">
                    X
                </div>

                <h3>
                    Lutas
                </h3>

                <p>
                    Gerencie os confrontos registrados.
                </p>

            </a>


            <a href="loja.php" class="atalho">

                <div class="icone">
                    >
                </div>

                <h3>
                    Ver Loja
                </h3>

                <p>
                    Visualize a loja como usuário.
                </p>

            </a>

        </div>

    </div>

</div>


<footer>

    <p>
        © 2026 <strong>Boxe Fight</strong> —
        Painel Administrativo
    </p>

</footer>


</body>

</html>

<?php
$conexao->close();
?>
