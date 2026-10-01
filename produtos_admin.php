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
   EXCLUIR PRODUTO
========================= */

if (
    isset($_GET["excluir"]) &&
    is_numeric($_GET["excluir"])
) {

    $id = intval($_GET["excluir"]);

    /* Buscar foto antes de excluir */

    $stmt = $conexao->prepare(
        "SELECT foto FROM produtos WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado_foto = $stmt->get_result();

    if ($resultado_foto->num_rows > 0) {

        $produto = $resultado_foto->fetch_assoc();

        /* Excluir produto */

        $stmt_delete = $conexao->prepare(
            "DELETE FROM produtos WHERE id = ?"
        );

        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();

        /*
            Exclui a imagem somente depois
            que o produto foi removido.
        */

        if (
            $stmt_delete->affected_rows > 0 &&
            !empty($produto["foto"])
        ) {

            $arquivo = "imagens/" . $produto["foto"];

            if (file_exists($arquivo)) {
                unlink($arquivo);
            }
        }

        $stmt_delete->close();
    }

    $stmt->close();

    header("Location: produtos_admin.php");
    exit;
}


/* =========================
   CADASTRAR PRODUTO
========================= */

if (
    isset($_POST["cadastrar_produto"])
) {

    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $categoria = trim($_POST["categoria"]);
    $preco = floatval($_POST["preco"]);
    $estoque = intval($_POST["estoque"]);

    $foto = "";


    /* Upload da foto */

    if (
        isset($_FILES["foto"]) &&
        $_FILES["foto"]["error"] == 0
    ) {

        $extensao = strtolower(
            pathinfo(
                $_FILES["foto"]["name"],
                PATHINFO_EXTENSION
            )
        );

        $permitidas = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];

        if (in_array($extensao, $permitidas)) {

            $foto = uniqid("produto_") . "." . $extensao;

            $destino = "imagens/" . $foto;

            move_uploaded_file(
                $_FILES["foto"]["tmp_name"],
                $destino
            );
        }
    }


    /* Inserir */

    $sql = "
        INSERT INTO produtos
        (nome, descricao, categoria, preco, estoque, foto)
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssdis",
        $nome,
        $descricao,
        $categoria,
        $preco,
        $estoque,
        $foto
    );

    $stmt->execute();

    $stmt->close();

    header("Location: produtos_admin.php");
    exit;
}


/* =========================
   EDITAR PRODUTO
========================= */

if (
    isset($_POST["editar_produto"])
) {

    $id = intval($_POST["id"]);

    $nome = trim($_POST["nome"]);
    $descricao = trim($_POST["descricao"]);
    $categoria = trim($_POST["categoria"]);
    $preco = floatval($_POST["preco"]);
    $estoque = intval($_POST["estoque"]);


    /* Buscar foto atual */

    $stmt = $conexao->prepare(
        "SELECT foto FROM produtos WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 0) {
        header("Location: produtos_admin.php");
        exit;
    }

    $produto_atual = $resultado->fetch_assoc();

    $foto = $produto_atual["foto"];

    $stmt->close();


    /* Nova foto */

    if (
        isset($_FILES["foto"]) &&
        $_FILES["foto"]["error"] == 0
    ) {

        $extensao = strtolower(
            pathinfo(
                $_FILES["foto"]["name"],
                PATHINFO_EXTENSION
            )
        );

        $permitidas = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];

        if (in_array($extensao, $permitidas)) {

            $nova_foto =
                uniqid("produto_") .
                "." .
                $extensao;

            $destino =
                "imagens/" .
                $nova_foto;

            move_uploaded_file(
                $_FILES["foto"]["tmp_name"],
                $destino
            );


            /* Apagar foto antiga */

            if (!empty($foto)) {

                $arquivo_antigo =
                    "imagens/" .
                    $foto;

                if (
                    file_exists($arquivo_antigo)
                ) {
                    unlink($arquivo_antigo);
                }
            }

            $foto = $nova_foto;
        }
    }


    /* Atualizar */

    $sql = "
        UPDATE produtos SET
            nome = ?,
            descricao = ?,
            categoria = ?,
            preco = ?,
            estoque = ?,
            foto = ?
        WHERE id = ?
    ";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "sssdisi",
        $nome,
        $descricao,
        $categoria,
        $preco,
        $estoque,
        $foto,
        $id
    );

    $stmt->execute();

    $stmt->close();

    header("Location: produtos_admin.php");
    exit;
}


/* =========================
   PRODUTO PARA EDITAR
========================= */

$produto_editar = null;

if (
    isset($_GET["editar"]) &&
    is_numeric($_GET["editar"])
) {

    $id_editar = intval($_GET["editar"]);

    $stmt = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $stmt->bind_param(
        "i",
        $id_editar
    );

    $stmt->execute();

    $resultado_editar =
        $stmt->get_result();

    if (
        $resultado_editar->num_rows > 0
    ) {

        $produto_editar =
            $resultado_editar->fetch_assoc();
    }

    $stmt->close();
}


/* =========================
   BUSCAR PRODUTOS
========================= */

$resultado = $conexao->query("
    SELECT *
    FROM produtos
    ORDER BY id DESC
");


/* =========================
   ESTATÍSTICAS
========================= */

$consulta = $conexao->query("
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(estoque), 0) AS estoque_total
    FROM produtos
");

$estatisticas =
    $consulta->fetch_assoc();

$total_produtos =
    $estatisticas["total"];

$estoque_total =
    $estatisticas["estoque_total"];


/* Produtos sem estoque */

$consulta = $conexao->query("
    SELECT COUNT(*) AS total
    FROM produtos
    WHERE estoque <= 0
");

$sem_estoque =
    $consulta->fetch_assoc()["total"];


/* Categorias */

$consulta = $conexao->query("
    SELECT COUNT(DISTINCT categoria) AS total
    FROM produtos
");

$total_categorias =
    $consulta->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">
<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Produtos | Boxe Fight</title>

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
    grid-template-columns: repeat(4, 1fr);

    gap: 15px;

    margin-bottom: 35px;
}

.stat {
    background: #151515;
    border: 1px solid #292929;

    border-radius: 9px;

    padding: 23px;

    transition: .3s;
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

.vermelho {
    color: #e10600;
}

.amarelo {
    color: #f0c000;
}


/* =========================
   FORMULÁRIO
========================= */

.formulario {
    background: #151515;

    border: 1px solid #292929;

    border-radius: 10px;

    padding: 30px;

    margin-bottom: 35px;
}

.formulario h2 {
    font-size: 21px;

    text-transform: uppercase;

    margin-bottom: 5px;
}

.formulario h2 span {
    color: #e10600;
}

.formulario p {
    color: #777;

    font-size: 13px;

    margin-bottom: 25px;
}

.form-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 18px;
}

.campo {
    display: flex;
    flex-direction: column;
}

.campo-completo {
    grid-column: 1 / -1;
}

.campo label {
    color: #aaa;

    font-size: 12px;
    font-weight: bold;

    margin-bottom: 7px;
}

input,
textarea,
select {
    width: 100%;

    background: #0d0d0d;

    color: #fff;

    border: 1px solid #333;

    border-radius: 5px;

    padding: 12px;

    outline: none;

    font-family: Arial, Helvetica, sans-serif;
}

input:focus,
textarea:focus,
select:focus {
    border-color: #e10600;
}

textarea {
    min-height: 100px;

    resize: vertical;
}

input[type="file"] {
    padding: 9px;
}

input[type="file"]::file-selector-button {
    background: #e10600;

    color: #fff;

    border: none;

    padding: 8px 12px;

    border-radius: 4px;

    margin-right: 10px;

    cursor: pointer;
}


/* =========================
   BOTÕES
========================= */

.botoes {
    display: flex;

    gap: 10px;

    margin-top: 5px;
}

.btn {
    display: inline-block;

    border: none;

    background: #e10600;

    color: #fff;

    text-decoration: none;

    padding: 11px 17px;

    border-radius: 5px;

    font-size: 12px;

    font-weight: bold;

    cursor: pointer;

    transition: .3s;
}

.btn:hover {
    background: #b80500;
}

.btn-cinza {
    background: #292929;
}

.btn-cinza:hover {
    background: #3c3c3c;
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
    background: #111;

    border-bottom: 1px solid #292929;

    padding: 20px;

    display: flex;

    justify-content: space-between;

    align-items: center;
}

.tabela-topo strong {
    font-size: 15px;

    text-transform: uppercase;
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
    background: #0f0f0f;

    color: #777;

    text-align: left;

    padding: 15px;

    font-size: 10px;

    text-transform: uppercase;

    border-bottom: 1px solid #292929;
}

.tabela td {
    padding: 15px;

    border-bottom: 1px solid #242424;

    font-size: 13px;

    vertical-align: middle;
}

.tabela tr:last-child td {
    border-bottom: none;
}

.tabela tr:hover td {
    background: #191919;
}


/* =========================
   PRODUTO
========================= */

.produto {
    display: flex;

    align-items: center;

    gap: 13px;
}

.produto-img {
    width: 55px;
    height: 55px;

    border-radius: 6px;

    object-fit: cover;

    background: #0d0d0d;

    border: 1px solid #292929;
}

.sem-imagem {
    width: 55px;
    height: 55px;

    border-radius: 6px;

    background: #0d0d0d;

    border: 1px solid #292929;

    display: flex;

    justify-content: center;
    align-items: center;

    color: #555;

    font-weight: bold;
}

.produto strong {
    display: block;

    margin-bottom: 4px;
}

.produto small {
    color: #777;
}


/* =========================
   PREÇO
========================= */

.preco {
    color: #fff;

    font-weight: bold;

    font-size: 14px;
}


/* =========================
   ESTOQUE
========================= */

.estoque {
    font-weight: bold;
}

.estoque-ok {
    color: #35d46a;
}

.estoque-baixo {
    color: #f0c000;
}

.estoque-zero {
    color: #e10600;
}


/* =========================
   AÇÕES
========================= */

.acoes {
    display: flex;

    gap: 7px;

    flex-wrap: wrap;
}

.editar {
    background: #292929;
}

.editar:hover {
    background: #444;
}

.excluir {
    background: transparent;

    border: 1px solid #e10600;

    color: #e10600;
}

.excluir:hover {
    background: #e10600;

    color: #fff;
}


/* =========================
   VOLTAR
========================= */

.voltar {
    margin-top: 25px;
}

.voltar a {
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

@media (max-width: 1000px) {

    .stats {
        grid-template-columns: repeat(2, 1fr);
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

    .form-grid {
        grid-template-columns: 1fr;
    }

    .campo-completo {
        grid-column: auto;
    }

    .topo h1 {
        font-size: 36px;
    }

}

@media (max-width: 500px) {

    .stats {
        grid-template-columns: 1fr;
    }

    .formulario {
        padding: 20px;
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

        <a
            href="produtos_admin.php"
            class="ativo"
        >
            Produtos
        </a>

        <a href="pedidos.php">
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
            Administração da loja
        </span>

        <h1>
            Gerenciar <span>Produtos</span>
        </h1>

        <p>
            Cadastre produtos, altere preços, controle o estoque
            e gerencie as imagens da loja Boxe Fight.
        </p>

    </section>


    <!-- =========================
         ESTATÍSTICAS
    ========================= -->

    <section class="stats">

        <div class="stat">

            <strong>
                <?php echo $total_produtos; ?>
            </strong>

            <span>
                Produtos cadastrados
            </span>

        </div>


        <div class="stat">

            <strong class="vermelho">
                <?php echo $estoque_total; ?>
            </strong>

            <span>
                Itens em estoque
            </span>

        </div>


        <div class="stat">

            <strong class="amarelo">
                <?php echo $sem_estoque; ?>
            </strong>

            <span>
                Sem estoque
            </span>

        </div>


        <div class="stat">

            <strong>
                <?php echo $total_categorias; ?>
            </strong>

            <span>
                Categorias
            </span>

        </div>

    </section>


    <!-- =========================
         FORMULÁRIO
    ========================= -->

    <section class="formulario">

        <?php if ($produto_editar): ?>

            <h2>
                Editar <span>produto</span>
            </h2>

            <p>
                Altere as informações do produto selecionado.
            </p>

            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $produto_editar["id"]; ?>"
                >

                <div class="form-grid">


                    <div class="campo">

                        <label>
                            Nome do produto
                        </label>

                        <input
                            type="text"
                            name="nome"
                            required
                            value="<?php
                            echo htmlspecialchars(
                                $produto_editar["nome"]
                            );
                            ?>"
                        >

                    </div>


                    <div class="campo">

                        <label>
                            Categoria
                        </label>

                        <input
                            type="text"
                            name="categoria"
                            required
                            value="<?php
                            echo htmlspecialchars(
                                $produto_editar["categoria"]
                            );
                            ?>"
                        >

                    </div>


                    <div class="campo campo-completo">

                        <label>
                            Descrição
                        </label>

                        <textarea
                            name="descricao"
                            required
                        ><?php
                        echo htmlspecialchars(
                            $produto_editar["descricao"]
                        );
                        ?></textarea>

                    </div>


                    <div class="campo">

                        <label>
                            Preço
                        </label>

                        <input
                            type="number"
                            name="preco"
                            step="0.01"
                            min="0"
                            required
                            value="<?php
                            echo $produto_editar["preco"];
                            ?>"
                        >

                    </div>


                    <div class="campo">

                        <label>
                            Estoque
                        </label>

                        <input
                            type="number"
                            name="estoque"
                            min="0"
                            required
                            value="<?php
                            echo $produto_editar["estoque"];
                            ?>"
                        >

                    </div>


                    <div class="campo campo-completo">

                        <label>
                            Nova foto
                        </label>

                        <input
                            type="file"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>


                    <div class="botoes campo-completo">

                        <button
                            type="submit"
                            name="editar_produto"
                            class="btn"
                        >
                            Salvar alterações
                        </button>

                        <a
                            href="produtos_admin.php"
                            class="btn btn-cinza"
                        >
                            Cancelar
                        </a>

                    </div>

                </div>

            </form>


        <?php else: ?>


            <h2>
                Novo <span>produto</span>
            </h2>

            <p>
                Cadastre um novo produto para aparecer na loja.
            </p>

            <form
                method="POST"
                enctype="multipart/form-data"
            >

                <div class="form-grid">


                    <div class="campo">

                        <label>
                            Nome do produto
                        </label>

                        <input
                            type="text"
                            name="nome"
                            placeholder="Ex: Luva de Boxe Pro"
                            required
                        >

                    </div>


                    <div class="campo">

                        <label>
                            Categoria
                        </label>

                        <input
                            type="text"
                            name="categoria"
                            placeholder="Ex: Luvas"
                            required
                        >

                    </div>


                    <div class="campo campo-completo">

                        <label>
                            Descrição
                        </label>

                        <textarea
                            name="descricao"
                            placeholder="Descrição do produto..."
                            required
                        ></textarea>

                    </div>


                    <div class="campo">

                        <label>
                            Preço
                        </label>

                        <input
                            type="number"
                            name="preco"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                        >

                    </div>


                    <div class="campo">

                        <label>
                            Estoque
                        </label>

                        <input
                            type="number"
                            name="estoque"
                            placeholder="Quantidade"
                            min="0"
                            required
                        >

                    </div>


                    <div class="campo campo-completo">

                        <label>
                            Foto do produto
                        </label>

                        <input
                            type="file"
                            name="foto"
                            accept=".jpg,.jpeg,.png,.webp"
                        >

                    </div>


                    <div class="botoes campo-completo">

                        <button
                            type="submit"
                            name="cadastrar_produto"
                            class="btn"
                        >
                            + Cadastrar produto
                        </button>

                    </div>

                </div>

            </form>

        <?php endif; ?>

    </section>


    <!-- =========================
         LISTA
    ========================= -->

    <section class="tabela-area">

        <div class="tabela-topo">

            <strong>
                Produtos cadastrados
            </strong>

            <span>
                <?php echo $total_produtos; ?> produto(s)
            </span>

        </div>


        <?php if ($resultado && $resultado->num_rows > 0): ?>

            <table class="tabela">

                <thead>

                    <tr>

                        <th>
                            Produto
                        </th>

                        <th>
                            Categoria
                        </th>

                        <th>
                            Preço
                        </th>

                        <th>
                            Estoque
                        </th>

                        <th>
                            Ações
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($produto = $resultado->fetch_assoc()): ?>

                        <?php

                        $estoque =
                            intval(
                                $produto["estoque"]
                            );

                        if ($estoque <= 0) {
                            $classe_estoque =
                                "estoque-zero";
                        }

                        elseif ($estoque <= 5) {
                            $classe_estoque =
                                "estoque-baixo";
                        }

                        else {
                            $classe_estoque =
                                "estoque-ok";
                        }

                        ?>

                        <tr>


                            <!-- PRODUTO -->

                            <td>

                                <div class="produto">

                                    <?php if (
                                        !empty(
                                            $produto["foto"]
                                        )
                                    ): ?>

                                        <img
                                            src="imagens/<?php
                                            echo htmlspecialchars(
                                                $produto["foto"]
                                            );
                                            ?>"
                                            class="produto-img"
                                            alt="<?php
                                            echo htmlspecialchars(
                                                $produto["nome"]
                                            );
                                            ?>"
                                        >

                                    <?php else: ?>

                                        <div class="sem-imagem">
                                            ?
                                        </div>

                                    <?php endif; ?>


                                    <div>

                                        <strong>

                                            <?php
                                            echo htmlspecialchars(
                                                $produto["nome"]
                                            );
                                            ?>

                                        </strong>

                                        <small>
                                            ID #<?php
                                            echo $produto["id"];
                                            ?>
                                        </small>

                                    </div>

                                </div>

                            </td>


                            <!-- CATEGORIA -->

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $produto["categoria"]
                                );
                                ?>

                            </td>


                            <!-- PREÇO -->

                            <td>

                                <span class="preco">

                                    R$

                                    <?php
                                    echo number_format(
                                        $produto["preco"],
                                        2,
                                        ",",
                                        "."
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- ESTOQUE -->

                            <td>

                                <span
                                    class="estoque <?php
                                    echo $classe_estoque;
                                    ?>"
                                >

                                    <?php
                                    echo $estoque;
                                    ?>

                                    <?php if ($estoque <= 0): ?>

                                        — SEM ESTOQUE

                                    <?php elseif ($estoque <= 5): ?>

                                        — BAIXO

                                    <?php endif; ?>

                                </span>

                            </td>


                            <!-- AÇÕES -->

                            <td>

                                <div class="acoes">

                                    <a
                                        href="produto.php?id=<?php
                                        echo $produto["id"];
                                        ?>"
                                        class="btn"
                                        target="_blank"
                                    >
                                        Ver
                                    </a>


                                    <a
                                        href="produtos_admin.php?editar=<?php
                                        echo $produto["id"];
                                        ?>"
                                        class="btn editar"
                                    >
                                        Editar
                                    </a>


                                    <a
                                        href="produtos_admin.php?excluir=<?php
                                        echo $produto["id"];
                                        ?>"
                                        class="btn excluir"
                                        onclick="return confirm(
                                            'Tem certeza que deseja excluir este produto?'
                                        );"
                                    >
                                        Excluir
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div style="
                padding: 70px 20px;
                text-align: center;
                color: #777;
            ">

                <h2 style="
                    color: #fff;
                    margin-bottom: 10px;
                ">
                    Nenhum produto cadastrado
                </h2>

                <p>
                    Cadastre o primeiro produto usando o formulário acima.
                </p>

            </div>

        <?php endif; ?>

    </section>


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
        Gerenciamento da loja Boxe Fight.
    </p>

</footer>


</body>

