<?php
$conn = new mysqli("localhost", "u839226731_cztuap", "Meu6595869Trator", "u839226731_meutrator");

$nome   = $_GET['nome']   ?? '';
$cpf    = $_GET['cpf']    ?? '';
$escola = $_GET['escola'] ?? '';
$turma  = $_GET['turma']  ?? '';
$page   = $_GET['page']   ?? 1;
$limit  = 10; // registros por página
$offset = ($page - 1) * $limit;

// Query base
$sqlBase = "FROM lista_espera l
            JOIN escolas e ON l.escola_id = e.id
            JOIN turmas t ON l.turma_id = t.id
            WHERE 1=1";

if ($nome)   $sqlBase .= " AND l.nome LIKE '%$nome%'";
if ($cpf)    $sqlBase .= " AND l.cpf LIKE '%$cpf%'";
if ($escola) $sqlBase .= " AND e.id = $escola";
if ($turma)  $sqlBase .= " AND t.id = $turma";

// Conta total de registros filtrados
$countResult = $conn->query("SELECT COUNT(*) AS total $sqlBase");
$totalRows   = $countResult->fetch_assoc()['total'];
$totalPages  = ceil($totalRows / $limit);

// Busca registros da página atual
$sql = "SELECT l.id, l.nome, l.cpf, e.nome AS escola, t.nome AS turma 
        $sqlBase 
        ORDER BY e.nome, t.nome, l.id ASC 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Espera</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>/* Estilo geral */
/* Estilo geral */
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
    color: #333;
}

.container {
    max-width: 1000px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Títulos */
h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #007BFF;
}

/* Formulário de filtros */
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.filter-form input, 
.filter-form select, 
.filter-form button {
    flex: 1;
    min-width: 180px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
}

.filter-form button {
    background: #007BFF;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background 0.3s;
}

.filter-form button:hover {
    background: #0056b3;
}

/* Tabela */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th, table td {
    padding: 14px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 15px;
}

table th {
    background: #007BFF;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

table tr:nth-child(even) {
    background: #f9f9f9;
}

/* Mensagens */
.success {
    color: green;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

.error {
    color: red;
    font-weight: bold;
    text-align: center;
    margin-bottom: 15px;
}

/* Paginação numerada */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 25px;
    flex-wrap: wrap;
}

.pagination a, .pagination span {
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 15px;
    transition: all 0.3s;
}

.pagination a {
    background: #007BFF;
    color: #fff;
}

.pagination a:hover {
    background: #0056b3;
}

.pagination .current {
    background: #333;
    color: #fff;
    font-weight: bold;
}

/* Responsividade */
@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
    }

    .filter-form input, 
    .filter-form select, 
    .filter-form button {
        min-width: 100%;
    }

    table th, table td {
        font-size: 14px;
        padding: 10px;
    }

    .pagination {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 6px;
    }
}

@media (max-width: 480px) {
    h2 {
        font-size: 20px;
    }

    table th, table td {
        font-size: 13px;
        padding: 8px;
    }

    .container {
        padding: 15px;
    }

    .pagination a, .pagination span {
        font-size: 13px;
        padding: 6px 10px;
    }
}

</style>
<div class="container">
    <h2>Lista de Espera</h2>

    <!-- Formulário de filtros -->
    <form method="GET" class="filter-form">
        <input type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($nome) ?>">
        <input type="text" name="cpf" placeholder="CPF" value="<?= htmlspecialchars($cpf) ?>">

        <select name="escola">
            <option value="">Todas as Escolas</option>
            <?php
            $resEscolas = $conn->query("SELECT * FROM escolas");
            while ($row = $resEscolas->fetch_assoc()) {
                $selected = ($escola == $row['id']) ? "selected" : "";
                echo "<option value='{$row['id']}' $selected>{$row['nome']}</option>";
            }
            ?>
        </select>

        <select name="turma">
            <option value="">Todas as Turmas</option>
            <?php
            $resTurmas = $conn->query("SELECT * FROM turmas");
            while ($row = $resTurmas->fetch_assoc()) {
                $selected = ($turma == $row['id']) ? "selected" : "";
                echo "<option value='{$row['id']}' $selected>{$row['nome']}</option>";
            }
            ?>
        </select>

        <button type="submit">Filtrar</button>
    </form>

    <!-- Tabela -->
    <table>
        <thead>
            <tr>
                <th>Posição</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Escola</th>
                <th>Turma</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $posicoes = [];
            while ($row = $result->fetch_assoc()):
                $grupo = $row['escola'] . "-" . $row['turma'];
                if (!isset($posicoes[$grupo])) {
                    $posicoes[$grupo] = $offset + 1; // posição começa no offset+1
                } else {
                    $posicoes[$grupo]++;
                }
            ?>
                <tr>
                    <td><?= $posicoes[$grupo] ?></td>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['cpf']) ?></td>
                    <td><?= htmlspecialchars($row['escola']) ?></td>
                    <td><?= htmlspecialchars($row['turma']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=1&nome=<?= urlencode($nome) ?>&cpf=<?= urlencode($cpf) ?>&escola=<?= $escola ?>&turma=<?= $turma ?>">« Primeira</a>
        <a href="?page=<?= $page-1 ?>&nome=<?= urlencode($nome) ?>&cpf=<?= urlencode($cpf) ?>&escola=<?= $escola ?>&turma=<?= $turma ?>">‹ Anterior</a>
    <?php endif; ?>

    <?php
    $range = 2; // quantidade de páginas vizinhas
    for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++):
        if ($i == $page) {
            echo "<span class='current'>$i</span>";
        } else {
            echo "<a href='?page=$i&nome=" . urlencode($nome) . "&cpf=" . urlencode($cpf) . "&escola=$escola&turma=$turma'>$i</a>";
        }
    endfor;
    ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page+1 ?>&nome=<?= urlencode($nome) ?>&cpf=<?= urlencode($cpf) ?>&escola=<?= $escola ?>&turma=<?= $turma ?>">Próxima ›</a>
        <a href="?page=<?= $totalPages ?>&nome=<?= urlencode($nome) ?>&cpf=<?= urlencode($cpf) ?>&escola=<?= $escola ?>&turma=<?= $turma ?>">Última »</a>
    <?php endif; ?>
</div>

</div>
</body>
</html>
