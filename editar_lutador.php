<?php

session_start();

// SOMENTE ADMIN PODE ACESSAR
if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: painel.php");
    exit;
}

include("conexao.php");

// Verifica se foi informado o ID
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: lutadores.php");
    exit;
}

$id = $_GET["id"];

// Busca o lutador
$sql = "SELECT * FROM lutadores WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {

    echo "<script>
            alert('Lutador não encontrado!');
            window.location.href = 'lutadores.php';
          </script>";

    exit;
}

$lutador = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Lutador - Boxe Fight</title>

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
            max-width: 800px;
            margin: 50px auto;
        }

        .titulo {
            text-align: center;
            margin-bottom: 30px;
        }

        .titulo h1 {
            font-size: 38px;
            margin-bottom: 10px;
        }

        .titulo h1 span {
            color: #e10600;
        }

        .titulo p {
            color: #bbbbbb;
        }

        /* AVISO */

        .aviso {
            background: #000000;
            border: 1px solid #e10600;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: center;
            color: #cccccc;
        }

        .aviso strong {
            color: #e10600;
        }

        /* FORMULÁRIO */

        .formulario {
            background: #1c1c1c;
            border: 1px solid #333333;
            border-radius: 10px;
            padding: 35px;
        }

        .campo {
            margin-bottom: 20px;
        }

        .campo label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .campo input,
        .campo select {
            width: 100%;
            padding: 13px;
            background: #111111;
            color: #ffffff;
            border: 1px solid #444444;
            border-radius: 6px;
            outline: none;
        }

        .campo input:focus,
        .campo select:focus {
            border-color: #e10600;
        }

        .linha {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        /* FOTO ATUAL */

        .foto-atual {
            text-align: center;
            margin-bottom: 25px;
        }

        .foto-atual img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #e10600;
        }

        .sem-foto {
            width: 160px;
            height: 160px;
            margin: auto;
            background: #333333;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            color: #999999;
        }

        /* BOTÕES */

        .botoes {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .botao {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            font-size: 15px;
        }

        .salvar {
            background: #e10600;
            color: #ffffff;
        }

        .salvar:hover {
            background: #b80500;
        }

        .voltar {
            background: #333333;
            color: #ffffff;
        }

        .voltar:hover {
            background: #555555;
        }

        /* FOOTER */

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

            .linha {
                grid-template-columns: 1fr;
            }

            .botoes {
                flex-direction: column;
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

            <a href="usuarios.php">Usuários</a>

            <a href="logout.php">Sair</a>

        </div>

    </nav>


    <div class="container">

        <div class="titulo">

            <h1>
                EDITAR <span>LUTADOR</span>
            </h1>

            <p>
                Alterando informações do lutador.
            </p>

        </div>


        <div class="aviso">

            <strong>ÁREA ADMINISTRATIVA:</strong>

            somente administradores podem editar lutadores.

        </div>


        <div class="formulario">

            <form
                action="atualizar_lutador.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <!-- ID DO LUTADOR -->

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $lutador["id"]; ?>"
                >


                <!-- FOTO ATUAL -->

                <div class="foto-atual">

                    <?php if (!empty($lutador["foto"])) { ?>

                        <img
                            src="fotos/<?php echo htmlspecialchars($lutador["foto"]); ?>"
                            alt="Foto do lutador"
                        >

                    <?php } else { ?>

                        <div class="sem-foto">
                            Sem foto
                        </div>

                    <?php } ?>

                </div>


                <!-- NOME -->

                <div class="campo">

                    <label for="nome">
                        Nome do lutador
                    </label>

                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="<?php echo htmlspecialchars($lutador["nome"]); ?>"
                        required
                    >

                </div>


                <!-- NACIONALIDADE -->

                <div class="campo">

                    <label for="nacionalidade">
                        Nacionalidade
                    </label>

                    <input
                        type="text"
                        id="nacionalidade"
                        name="nacionalidade"
                        value="<?php echo htmlspecialchars($lutador["nacionalidade"]); ?>"
                        required
                    >

                </div>


                <!-- IDADE / VITÓRIAS / DERROTAS -->

                <div class="linha">

                    <div class="campo">

                        <label for="idade">
                            Idade
                        </label>

                        <input
                            type="number"
                            id="idade"
                            name="idade"
                            min="1"
                            max="100"
                            value="<?php echo $lutador["idade"]; ?>"
                            required
                        >

                    </div>


                    <div class="campo">

                        <label for="vitorias">
                            Vitórias
                        </label>

                        <input
                            type="number"
                            id="vitorias"
                            name="vitorias"
                            min="0"
                            value="<?php echo $lutador["vitorias"]; ?>"
                            required
                        >

                    </div>


                    <div class="campo">

                        <label for="derrotas">
                            Derrotas
                        </label>

                        <input
                            type="number"
                            id="derrotas"
                            name="derrotas"
                            min="0"
                            value="<?php echo $lutador["derrotas"]; ?>"
                            required
                        >

                    </div>

                </div>


                <!-- EMPATES -->

                <div class="campo">

                    <label for="empates">
                        Empates
                    </label>

                    <input
                        type="number"
                        id="empates"
                        name="empates"
                        min="0"
                        value="<?php echo $lutador["empates"]; ?>"
                        required
                    >

                </div>


                <!-- CATEGORIA -->

                <div class="campo">

                    <label for="categoria">
                        Categoria
                    </label>

                    <select
                        id="categoria"
                        name="categoria"
                        required
                    >

                        <option value="">
                            Selecione uma categoria
                        </option>

                        <option value="Peso Mosca"
                            <?php if ($lutador["categoria"] == "Peso Mosca") echo "selected"; ?>>
                            Peso Mosca
                        </option>

                        <option value="Peso Galo"
                            <?php if ($lutador["categoria"] == "Peso Galo") echo "selected"; ?>>
                            Peso Galo
                        </option>

                        <option value="Peso Pena"
                            <?php if ($lutador["categoria"] == "Peso Pena") echo "selected"; ?>>
                            Peso Pena
                        </option>

                        <option value="Peso Leve"
                            <?php if ($lutador["categoria"] == "Peso Leve") echo "selected"; ?>>
                            Peso Leve
                        </option>

                        <option value="Peso Meio-Médio"
                            <?php if ($lutador["categoria"] == "Peso Meio-Médio") echo "selected"; ?>>
                            Peso Meio-Médio
                        </option>

                        <option value="Peso Médio"
                            <?php if ($lutador["categoria"] == "Peso Médio") echo "selected"; ?>>
                            Peso Médio
                        </option>

                        <option value="Peso Meio-Pesado"
                            <?php if ($lutador["categoria"] == "Peso Meio-Pesado") echo "selected"; ?>>
                            Peso Meio-Pesado
                        </option>

                        <option value="Peso Pesado"
                            <?php if ($lutador["categoria"] == "Peso Pesado") echo "selected"; ?>>
                            Peso Pesado
                        </option>

                    </select>

                </div>


                <!-- NOVA FOTO -->

                <div class="campo">

                    <label for="foto">
                        Nova foto
                    </label>

                    <input
                        type="file"
                        id="foto"
                        name="foto"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                </div>


                <!-- BOTÕES -->

                <div class="botoes">

                    <a
                        href="lutadores.php"
                        class="botao voltar"
                    >
                        CANCELAR
                    </a>

                    <button
                        type="submit"
                        class="botao salvar"
                    >
                        SALVAR ALTERAÇÕES
                    </button>

                </div>

            </form>

        </div>

    </div>


    <footer>

        <p>
            &copy; 2026 Boxe Fight - Área Administrativa
        </p>

    </footer>

</body>

</html>

<?php

$stmt->close();
$conexao->close();

?>

