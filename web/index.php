<?php
$host = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'trabalho_db';
$user = getenv('DB_USER') ?: 'app_user';
$password = getenv('DB_PASSWORD') ?: 'app_password';
$mensagemSistema = '';
$erro = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec("CREATE TABLE IF NOT EXISTS contatos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL,
        mensagem TEXT NOT NULL,
        criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');

        if ($nome && $email && $mensagem) {
            $stmt = $pdo->prepare('INSERT INTO contatos (nome, email, mensagem) VALUES (?, ?, ?)');
            $stmt->execute([$nome, $email, $mensagem]);
            header('Location: /?salvo=1');
            exit;
        }
        $mensagemSistema = 'Preencha todos os campos antes de salvar.';
    }

    if (isset($_GET['delete'])) {
        $id = (int) $_GET['delete'];
        $stmt = $pdo->prepare('DELETE FROM contatos WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: /?removido=1');
        exit;
    }

    if (isset($_GET['salvo'])) {
        $mensagemSistema = 'Contato salvo com sucesso.';
    }
    if (isset($_GET['removido'])) {
        $mensagemSistema = 'Contato removido com sucesso.';
    }

    $contatos = $pdo->query('SELECT * FROM contatos ORDER BY criado_em DESC')->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $erro = 'Não foi possível conectar ao banco de dados: ' . $e->getMessage();
    $contatos = [];
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trabalho Docker - Sistema Web</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f8; color: #222; }
        header { background: #1f2937; color: white; padding: 24px; text-align: center; }
        main { max-width: 960px; margin: 24px auto; padding: 0 16px; }
        .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        label { display: block; font-weight: bold; margin-top: 12px; }
        input, textarea { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button, .btn { display: inline-block; margin-top: 14px; background: #2563eb; color: white; border: 0; padding: 10px 16px; border-radius: 6px; text-decoration: none; cursor: pointer; }
        .btn-danger { background: #dc2626; }
        .ok { background: #dcfce7; color: #166534; padding: 12px; border-radius: 6px; }
        .erro { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; vertical-align: top; }
        th { background: #f9fafb; }
        code { background: #eef2ff; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>
<header>
    <h1>Trabalho prático sobre contêineres</h1>
    <p>Sistema web PHP + MySQL executando com Docker Compose</p>
</header>
<main>
    <section class="card">
        <h2>Cadastro de contatos</h2>
        <p>Esta aplicação está no contêiner <code>web</code> e salva os dados no contêiner <code>db</code>.</p>

        <?php if ($mensagemSistema): ?>
            <p class="ok"><?= e($mensagemSistema) ?></p>
        <?php endif; ?>

        <?php if ($erro): ?>
            <p class="erro"><?= e($erro) ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="nome">Nome</label>
            <input id="nome" name="nome" placeholder="Ex.: José Silva" required>

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" placeholder="jose@email.com" required>

            <label for="mensagem">Mensagem</label>
            <textarea id="mensagem" name="mensagem" rows="4" placeholder="Digite uma mensagem" required></textarea>

            <button type="submit">Salvar no banco</button>
        </form>
    </section>

    <section class="card">
        <h2>Registros salvos</h2>
        <?php if (!$contatos): ?>
            <p>Nenhum contato cadastrado ainda.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Mensagem</th>
                        <th>Criado em</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contatos as $contato): ?>
                    <tr>
                        <td><?= e($contato['id']) ?></td>
                        <td><?= e($contato['nome']) ?></td>
                        <td><?= e($contato['email']) ?></td>
                        <td><?= e($contato['mensagem']) ?></td>
                        <td><?= e($contato['criado_em']) ?></td>
                        <td><a class="btn btn-danger" href="/?delete=<?= e($contato['id']) ?>" onclick="return confirm('Remover este registro?')">Excluir</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
