<?php
session_start();
include("conexao.php");

/* SOMENTE ADMIN PODE CADASTRAR LUTAS */
if (!isset($_SESSION["id"]) || $_SESSION["tipo"] != "admin") {
    header("Location: painel.php");
    exit;
}

/* BUSCAR LUTADORES CADASTRADOS */
$sql_lutadores = "SELECT id, nome FROM lutadores ORDER BY nome ASC";
$resultado_lutadores = $conexao->query($sql_lutadores);
?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cadastrar Luta - Boxe Fight</title>

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
        min-height: 100vh;
    }

    /* NAVBAR */

    nav {
        background: #000000;
        border-bottom: 2px solid #e10600;
        padding: 18px 50px;

        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        color: #e10600;
        font-size: 26px;
        font-weight: bold;
        text-decoration: none;
    }

    .logo span {
        color: #ffffff;
    }

    .menu {
        display: flex;
        gap: 25px;
        align-items: center;
    }

    .menu a {
        color: #ffffff;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .menu a:hover {
        color: #e10600;
    }

    .admin {
        background: #e10600;
        padding: 8px 14px;
        border-radius: 5px;
    }

    .admin:hover {
        background: #b80500 !important;
        color: #ffffff !important;
    }

    /* CONTAINER */

    .container {
        width: 90%;
        max-width: 700px;
        margin: 50px auto;
    }

    .titulo {
        text-align: center;
        margin-bottom: 35px;
    }

    .titulo h1 {
        font-size: 38px;
        color: #ffffff;
    }

    .titulo h1 span {
        color: #e10600;
    }

    .titulo p {
        color: #999999;
        margin-top: 10px;
    }

    /* FORMULÁRIO */

    .formulario {
        background: #1c1c1c;
        border: 1px solid #333333;
        border-top: 4px solid #e10600;
        padding: 35px;
        border-radius: 10px;
    }

    .campo {
        margin-bottom: 22px;
    }

    .campo label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #ffffff;
    }

    .campo input,
    .campo select {
        width: 100%;
        padding: 13px;

        background: #111111;
        border: 1px solid #444444;
        border-radius: 5px;

        color: #ffffff;
        font-size: 16px;
        outline: none;
    }

    .campo input:focus,
    .campo select:focus {
        border-color: #e10600;
    }

    .campo select option {
        background: #111111;
        color: #ffffff;
    }

    /* AVISO SOBRE LUTADORES */

    .ajuda {
        color: #999999;
        font-size: 13px;
        margin-top: 7px;
    }

    /* BOTÕES */

    .botoes {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        flex: 1;
        padding: 14px;

        border: none;
        border-radius: 5px;

        font-size: 16px;
        font-weight: bold;

        cursor: pointer;
        text-decoration: none;
        text-align: center;

        transition: 0.3s;
    }

    .btn-salvar {
        background: #e10600;
        color: #ffffff;
    }

    .btn-salvar:hover {
        background: #b80500;
    }

    .btn-voltar {
        background: #333333;
        color: #ffffff;
    }

    .btn-voltar:hover {
        background: #555555;
    }

    /* AVISO ADMIN */

    .aviso {
        background: #000000;
        border-left: 4px solid #e10600;
        padding: 15px;
        margin-bottom: 25px;
        color: #dddddd;
    }

    .aviso strong {
        color: #e10600;
    }

    /* SEM LUTADORES */

    .sem-lutadores {
        background: #000000;
        border: 1px solid #e10600;
        border-radius: 6px;
        padding: 18px;
        margin-bottom: 25px;
        color: #dddddd;
    }

    .sem-lutadores strong {
        color: #e10600;
    }

    .sem-lutadores a {
        display: inline-block;
        margin-top: 10px;
        color: #ffffff;
        background: #e10600;
        padding: 9px 14px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
    }

    .sem-lutadores a:hover {
        background: #b80500;
    }

    /* FOOTER */

    footer {
        text-align: center;
        padding: 30px;
        margin-top: 50px;

        background: #000000;
        border-top: 1px solid #333333;

        color: #777777;
    }

    /* RESPONSIVO */

    @media (max-width: 700px) {

        nav {
            padding: 18px 20px;
            flex-direction: column;
            gap: 15px;
        }

        .menu {
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .container {
            width: 94%;
        }

        .formulario {
            padding: 25px;
        }

        .titulo h1 {
            font-size: 30px;
        }

        .botoes {
            flex-direction: column;
        }

    }

</style>

</head>

<body>

<!-- NAVBAR -->

<nav>

    <a href="index.php" class="logo">
        BOXE <span>FIGHT</span>
    </a>

    <div class="menu">

        <a href="index.php">
            Início
        </a>

        <a href="painel.php">
            Painel
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

        <a href="logout.php" class="admin">
            Sair
        </a>

    </div>

</nav>


<!-- CONTEÚDO -->

<main class="container">

    <div class="titulo">

        <h1>
            CADASTRAR <span>LUTA</span>
        </h1>

        <p>
            Área exclusiva do administrador
        </p>

    </div>


    <!-- AVISO -->

    <div class="aviso">

        <strong>ADMINISTRADOR:</strong>

        Você está cadastrando uma nova luta no sistema.

    </div>


    <?php if (!$resultado_lutadores || $resultado_lutadores->num_rows < 2): ?>

        <div class="sem-lutadores">

            <strong>Não é possível cadastrar uma luta.</strong>

            <p style="margin-top: 8px;">
                É necessário ter pelo menos 2 lutadores cadastrados no sistema.
            </p>

            <a href="cadastrar_lutador.php">
                + Cadastrar Lutador
            </a>

        </div>

    <?php else: ?>


        <!-- FORMULÁRIO -->

        <div class="formulario">

            <form action="salvar_luta.php" method="POST">


                <!-- LUTADOR 1 -->

                <div class="campo">

                    <label for="lutador1">
                        Lutador 1
                    </label>

                    <select
                        id="lutador1"
                        name="lutador1"
                        required
                    >

                        <option value="">
                            Selecione o primeiro lutador
                        </option>

                        <?php

                        $resultado_lutadores->data_seek(0);

                        while ($lutador = $resultado_lutadores->fetch_assoc()):

                        ?>

                            <option value="<?php echo htmlspecialchars($lutador["nome"]); ?>">

                                <?php echo htmlspecialchars($lutador["nome"]); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                    <p class="ajuda">
                        Apenas lutadores cadastrados no banco aparecem aqui.
                    </p>

                </div>


                <!-- LUTADOR 2 -->

                <div class="campo">

                    <label for="lutador2">
                        Lutador 2
                    </label>

                    <select
                        id="lutador2"
                        name="lutador2"
                        required
                    >

                        <option value="">
                            Selecione o segundo lutador
                        </option>

                        <?php

                        $resultado_lutadores->data_seek(0);

                        while ($lutador = $resultado_lutadores->fetch_assoc()):

                        ?>

                            <option value="<?php echo htmlspecialchars($lutador["nome"]); ?>">

                                <?php echo htmlspecialchars($lutador["nome"]); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                    <p class="ajuda">
                        Escolha outro lutador para formar o confronto.
                    </p>

                </div>


                <!-- DATA -->

                <div class="campo">

                    <label for="data_luta">
                        Data da luta
                    </label>

                    <input
                        type="date"
                        id="data_luta"
                        name="data_luta"
                        required
                    >

                </div>


                <!-- RESULTADO -->

                <div class="campo">

                    <label for="resultado">
                        Resultado
                    </label>

                    <select
                        id="resultado"
                        name="resultado"
                        required
                    >

                        <option value="">
                            Selecione o resultado
                        </option>

                        <option value="Vitória do Lutador 1">
                            Vitória do Lutador 1
                        </option>

                        <option value="Vitória do Lutador 2">
                            Vitória do Lutador 2
                        </option>

                        <option value="Empate">
                            Empate
                        </option>

                        <option value="Sem resultado">
                            Sem resultado
                        </option>

                    </select>

                </div>


                <!-- MÉTODO -->

                <div class="campo">

                    <label for="metodo">
                        Método da vitória
                    </label>

                    <select
                        id="metodo"
                        name="metodo"
                        required
                    >

                        <option value="">
                            Selecione o método
                        </option>

                        <option value="Nocaute (KO)">
                            Nocaute (KO)
                        </option>

                        <option value="Nocaute Técnico (TKO)">
                            Nocaute Técnico (TKO)
                        </option>

                        <option value="Decisão dos Juízes">
                            Decisão dos Juízes
                        </option>

                        <option value="Decisão Unânime">
                            Decisão Unânime
                        </option>

                        <option value="Decisão Dividida">
                            Decisão Dividida
                        </option>

                        <option value="Desistência">
                            Desistência
                        </option>

                        <option value="Desqualificação">
                            Desqualificação
                        </option>

                        <option value="Empate">
                            Empate
                        </option>

                    </select>

                </div>


                <!-- BOTÕES -->

                <div class="botoes">

                    <a
                        href="lutas.php"
                        class="btn btn-voltar"
                    >
                        Voltar
                    </a>

                    <button
                        type="submit"
                        class="btn btn-salvar"
                    >
                        CADASTRAR LUTA
                    </button>

                </div>

            </form>

        </div>

    <?php endif; ?>

</main>


<!-- FOOTER -->

<footer>

    <p>
        &copy; 2026 Boxe Fight - Sistema de Boxe
    </p>

</footer>


</body>

</html>

<?php
$conexao->close();
?>
