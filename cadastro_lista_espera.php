<?php
$conn = new mysqli("localhost", "u839226731_cztuap", "Meu6595869Trator", "u839226731_meutrator");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $escola_id = $_POST['escola'];
    $turma_id = $_POST['turma'];

    $sql = "INSERT INTO lista_espera (nome, cpf, escola_id, turma_id) 
            VALUES ('$nome', '$cpf', '$escola_id', '$turma_id')";
    if ($conn->query($sql)) {
        echo "<p class='success'>Aluno cadastrado na lista de espera com sucesso!</p>";
    } else {
        echo "<p class='error'>Erro ao cadastrar: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Lista de Espera</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 30px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

.filter-form, .form-cadastro {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.form-cadastro label {
    flex: 1 1 100%;
    margin-top: 10px;
    font-weight: bold;
}

.filter-form input, 
.filter-form select, 
.filter-form button,
.form-cadastro input,
.form-cadastro select,
.form-cadastro button {
    flex: 1;
    min-width: 150px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

button {
    background: #007BFF;
    color: #fff;
    border: none;
    cursor: pointer;
}

button:hover {
    background: #0056b3;
}

.success {
    color: green;
    font-weight: bold;
    text-align: center;
}

.error {
    color: red;
    font-weight: bold;
    text-align: center;
}

/* Responsividade */
@media (max-width: 600px) {
    .filter-form, .form-cadastro {
        flex-direction: column;
    }

    table th, table td {
        font-size: 14px;
        padding: 8px;
    }
}
</style>
    <div class="container">
        <h2>Cadastrar na Lista de Espera</h2>

        <form method="POST" class="form-cadastro">
            <label>Nome:</label>
            <input type="text" name="nome" required>

            <label>CPF:</label>
            <input type="text" name="cpf" required placeholder="000.000.000-00">

            <label>Escola:</label>
            <select name="escola" required>
                <option value="">Selecione</option>
                <?php
                $resEscolas = $conn->query("SELECT * FROM escolas");
                while ($row = $resEscolas->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                }
                ?>
            </select>

            <label>Turma:</label>
            <select name="turma" required>
                <option value="">Selecione</option>
                <?php
                $resTurmas = $conn->query("SELECT * FROM turmas");
                while ($row = $resTurmas->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                }
                ?>
            </select>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>
</html>
