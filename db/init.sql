CREATE TABLE IF NOT EXISTS contatos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  mensagem TEXT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO contatos (nome, email, mensagem)
VALUES ('Exemplo Docker', 'docker@local', 'Registro inicial criado automaticamente pelo init.sql.');
