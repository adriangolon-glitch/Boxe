<?php

session_start();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Criar Conta - Boxe Fight</title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #111111;
            color: white;
            min-height: 100vh;
        }

        /* NAVBAR */

        nav {
            background: #000000;
            padding: 20px 7%;

            display: flex;
            justify-content: space-between;
            align-items: center;

            border-bottom: 2px solid #e10600;
        }

        .logo {
            color: #e10600;
            font-size: 25px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-weight: bold;
        }

        nav a:hover {
            color: #e10600;
        }

        /* CONTAINER */

        .container {
            width: 90%;
            max-width: 500px;
            margin: 60px auto;
        }

        .titulo {
            text-align: center;
            margin-bottom: 30px;
        }

        .titulo h1 {
            color: #e10600;
            font-size: 35px;
            margin-bottom: 10px;
        }

        .titulo p {
            color: #bbbbbb;
        }

        /* FORMULÁRIO */

        .formulario {
            background: #1c1c1c;
            padding: 35px;
            border-radius: 10px;
            border: 1px solid #333333;
        }

        .campo {
            margin-bottom: 20px;
        }

        .campo label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .campo input {
            width: 100%;
            padding: 14px;

            background: #111111;
            color: white;

            border: 1px solid #444444;
            border-radius: 6px;

            outline: none;
        }

        .campo input:focus {
            border-color: #e10600;
        }

        .btn {
            width: 100%;
            padding: 14px;

            border: none;
            border-radius: 6px;

            background: #e10600;
            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        .btn:hover {
            background: #b80500;
        }

        .login {
            text-align: center;
            margin-top: 25px;
            color: #bbbbbb;
        }

        .login a {
            color: #e10600;
            text-decoration: none;
            font-weight: bold;
        }

        .login a:hover {
            text-decoration: underline;
        }

        /* RESPONSIVO */

        @media (max-width: 600px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            nav a {
                margin: 5px;
            }

            .formulario {
                padding: 25px;
            }

        }

    </style>

</head>


<body>


    <!-- NAVBAR -->

    <nav>

        <div class="logo">
            BOXE FIGHT
        </div>

        <div>

            <a href="index.php">
                Início
            </a>

            <a href="lutadores.php">
                Lutadores
            </a>

            <a href="lutas.php">
                Lutas
            </a>

            <a href="login.php">
                Login
            </a>

        </div>

    </nav>


    <!-- CADASTRO -->

    <div class="container">

        <div class="titulo">

            <h1>CRIAR CONTA</h1>

            <p>
                Crie sua conta para acessar conteúdos exclusivos.
            </p>

        </div>


        <div class="formulario">

            <form action="salvar_usuario.php" method="POST">


                <div class="campo">

                    <label>
                        Nome de usuário
                    </label>

                    <input
                        type="text"
                        name="usuario"
                        placeholder="Digite seu usuário"
                        required
                    >

                </div>


                <div class="campo">

                    <label>
                        Senha
                    </label>

                    <input
                        type="password"
                        name="senha"
                        placeholder="Digite sua senha"
                        required
                    >

                </div>


                <div class="campo">

                    <label>
                        Confirmar senha
                    </label>

                    <input
                        type="password"
                        name="confirmar_senha"
                        placeholder="Digite a senha novamente"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn"
                >
                    CRIAR MINHA CONTA
                </button>


            </form>


            <div class="login">

                Já possui uma conta?

                <a href="login.php">
                    Fazer login
                </a>

            </div>


        </div>

    </div>


</body>

</html>
