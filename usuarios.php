<?php

session_start();

include("conexao.php");

// Somente administrador pode acessar
if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: painel.php");
    exit;
}

// Busca todos os usuários
$sql = "SELECT * FROM usuarios ORDER BY id DESC";

$resultado = $conexao->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Usuários - Boxe Fight</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #111111;
            color: #ffffff;
        }

        /* NAVBAR */

        nav {
            background: #000000;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e10600;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
        }

        .logo span {
            color: #e10600;
        }

        nav a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
        }

        nav a:hover {
            color: #e10600;
        }

        /* CONTAINER */

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 50px auto;
        }

        .topo {
            text-align: center;
            margin-bottom: 35px;
        }

        .topo h1 {
            font-size: 38px;
            margin-bottom: 10px;
        }

        .topo h1 span {
            color: #e10600;
        }

        .topo p {
            color: #bbbbbb;
        }

        /* AVISO ADMIN */

        .aviso {
            background: #1c1c1c;
            border-left: 5px solid #e10600;
            padding: 18px;
            margin-bottom: 25px;
            color: #cccccc;
        }

        /* TABELA */

        .tabela-container {
            background: #1c1c1c;
            border: 1px solid #333333;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #e10600;
            color: #ffffff;
            padding: 16px;
            text-align: left;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #333333;
            color: #dddddd;
        }

        tr:hover {
            background: #252525;
        }

        /* TIPO */

        .tipo {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .admin {
            background: #e10600;
            color: #ffffff;
        }

        .usuario {
            background: #333333;
            color: #ffffff;
            border: 1px solid #555555;
        }

        /* BOTÃO EXCLUIR */

        .excluir {
            display: inline-block;
            background: #e10600;
            color: #ffffff;
            text-decoration: none;
            padding: 9px 14px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 13px;
        }

        .excluir:hover {
            background: #b80500;
        }

        /* BOTÃO VOLTAR */

        .voltar {
            display: inline-block;
            margin-top: 25px;
            background: #333333;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        .voltar:hover {
            background: #555555;
        }

        /* RODAPÉ */

        footer {
            background: #000000;
            text-align: center;
            padding: 25px;
            margin-top: 60px;
            color: #999999;
            border-top: 1px solid #333333;
        }

        /* RESPONSIVO */

        @media (max-width: 700px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            nav a {
                margin: 5px;
            }

            .topo h1 {
                font-size: 30px;
            }

            th,
            td {
                padding: 10px;
                font-size: 13px;
            }

        }

    </style>

</head>

<body>

    <nav>

        <div class="logo">
            BOXE <span>FIGHT</span>
        </div>

        <div>

            <a href="index.php">Início</a>

            <a href="painel.php">Painel</a>

            <a href="lutadores.php">Lutadores</a>

            <a href="lutas.php">Lutas</a>

            <a href="logout.php">Sair</a>

        </div>

    </nav>


    <div class="container">

        <div class="topo">

            <h1>
                GERENCIAR <span>USUÁRIOS</span>
            </h1>

            <p>
                Área exclusiva do administrador.
            </p>

        </div>


        <div class="aviso">

            <strong>Área administrativa:</strong>

            somente o administrador pode visualizar e
            gerenciar as contas cadastradas no sistema.

        </div>


        <div class="tabela-container">

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Usuário</th>

                        <th>Tipo</th>

                        <th>Ação</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while ($usuario = $resultado->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <?php echo $usuario["id"]; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($usuario["usuario"]); ?>
                            </td>

                            <td>

                                <?php if ($usuario["tipo"] == "admin") { ?>

                                    <span class="tipo admin">
                                        Administrador
                                    </span>

                                <?php } else { ?>

                                    <span class="tipo usuario">
                                        Usuário
                                    </span>

                                <?php } ?>

                            </td>

                            <td>

                                <?php if ($usuario["id"] != $_SESSION["id"]) { ?>

                                    <a
                                        href="excluir_usuario.php?id=<?php echo $usuario["id"]; ?>"
                                        class="excluir"
                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?');"
                                    >
                                        EXCLUIR
                                    </a>

                                <?php } else { ?>

                                    <span style="color: #999999;">
                                        Conta atual
                                    </span>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>


        <a href="painel.php" class="voltar">
            ← VOLTAR AO PAINEL
        </a>

    </div>


    <footer>

        <p>
            &copy; 2026 Boxe Fight - Administração
        </p>

    </footer>

</body>

</html>

