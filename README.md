# Trabalho prático sobre contêineres

Ambiente desenvolvido com Docker Compose contendo:
- Aplicação web em PHP + Apache disponível em `http://localhost:8080`.
- Banco de dados MySQL em outro contêiner, sem porta publicada para acesso externo.
- Adminer para administração do banco em `http://localhost:8081`.
- Portainer para gerenciamento gráfico dos contêineres em `https://localhost:9443`.

## Estrutura
```text
trabalho-docker/
├── docker-compose.yml
├── web/
│   ├── Dockerfile
│   └── index.php
└── db/
    └── init.sql
```

## Como executar
No terminal, dentro da pasta do projeto:
```bash
docker compose up -d --build
```

Verifique os contêineres:
```bash
docker compose ps
```

Acesse:
- Sistema web: `http://localhost:8080`
- Adminer: `http://localhost:8081`
- Portainer: `https://localhost:9443`

## Dados para acessar o Adminer
- Sistema: `MySQL`
- Servidor: `db`
- Usuário: `app_user`
- Senha: `app_password`
- Banco: `trabalho_db`
