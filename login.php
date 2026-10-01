<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Boxe Fight - Login</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background-color: #111111;
            color: #ffffff;
            min-height: 100vh;
        }

        /* MENU */

        nav {
            background-color: #000000;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e10600;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
        }

        .logo span {
            color: #e10600;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            text-decoration: none;
            color: #dddddd;
            font-weight: bold;
            transition: 0.3s;
        }

        nav ul li a:hover {
            color: #e10600;
        }

        /* ÁREA DO LOGIN */

        .login-container {
            min-height: calc(100vh - 80px);

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 40px 20px;
        }

        .login-box {
            width: 100%;
            max-width: 430px;

            background-color: #1c1c1c;

            padding: 40px;

            border-radius: 10px;

            border: 1px solid #333333;

            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .login-box h1 {
            text-align: center;

            font-size: 32px;

            margin-bottom: 10px;

            color: #ffffff;
        }

        .login-box h1 span {
            color: #e10600;
        }

        .subtitulo {
            text-align: center;

            color: #999999;

            margin-bottom: 30px;
        }

        /* FORMULÁRIO */

        .campo {
            margin-bottom: 20px;
        }

        .campo label {
            display: block;

            margin-bottom: 8px;

            color: #dddddd;

            font-weight: bold;
        }

        .campo input {
            width: 100%;

            padding: 13px;

            background-color: #111111;

            border: 1px solid #444444;

            border-radius: 5px;

            color: #ffffff;

            font-size: 15px;

            outline: none;

            transition: 0.3s;
        }

        .campo input:focus {
            border-color: #e10600;
        }

        /* BOTÃO */

        .botao {
            width: 100%;

            padding: 14px;

            border: none;

            border-radius: 5px;

            background-color: #e10600;

            color: #ffffff;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }

        .botao:hover {
            background-color: #b80500;
        }

        /* VOLTAR */

        .voltar {
            text-align: center;

            margin-top: 25px;
        }

        .voltar a {
            color: #bbbbbb;

            text-decoration: none;

            transition: 0.3s;
        }

        .voltar a:hover {
            color: #e10600;
        }

        /* RESPONSIVO */

        @media (max-width: 600px) {

            nav {
                flex-direction: column;

                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;

                justify-content: center;
            }

            .login-box {
                padding: 30px 20px;
            }

        }

    </style>

</head>

<body>


    <!-- MENU -->

    <nav>

        <div class="logo">
            BOXE <span>FIGHT</span>
        </div>

        <ul>

            <li>
                <a href="index.php">Início</a>
            </li>

            <li>
                <a href="lutadores.php">Lutadores</a>
            </li>

            <li>
                <a href="lutas.php">Lutas</a>
            </li>

            <li>
                <a href="login.php">Login</a>
            </li>

        </ul>

    </nav>


    <!-- LOGIN -->

    <div class="login-container">

        <div class="login-box">

            <h1>
                BOXE <span>FIGHT</span>
            </h1>

            <p class="subtitulo">
                Entre na sua conta
            </p>


            <form action="autenticar.php" method="POST">


                <div class="campo">

                    <label for="usuario">
                        Usuário
                    </label>

                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        placeholder="Digite seu usuário"
                        required
                    >

                </div>


                <div class="campo">

                    <label for="senha">
                        Senha
                    </label>

                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                </div>


                <button type="submit" class="botao">
                    ENTRAR
                </button>

            </form>


            <div class="voltar">

                <a href="index.php">
                    ← Voltar para o início
                </a>

            </div>

        </div>

    </div>

</body>

</html>

