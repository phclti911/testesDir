# Como rodar o projeto

Você pode rodar o backend de duas formas:

**Usando Docker (recomendado):**

```
docker compose up -d
```

ou para subir novamente se já estiver parado:

```
docker compose start
```

**Rodando localmente (apenas PHP, sem Docker):**

```
php -S localhost:8000 -t public
```

Acesse em: http://localhost:8000 ou http://localhost:8123 (se usar Docker)

---

# Documentação do Projeto Backend PHP MVC - AVP2

## Visão Geral

Este projeto é um backend em PHP puro, seguindo o padrão MVC, sem frameworks ou bibliotecas externas, com persistência em MySQL. O ambiente é orquestrado via Docker Compose, mas também pode ser integrado ao MySQL do XAMPP.

## Estrutura de Pastas

- `public/` — Document root do Apache, contém o `index.php` (roteador).
- `app/Controllers/` — Controllers das rotas (Vaga, Pessoa, Candidatura, Ranking, Home).
- `app/Models/` — Models (exemplo didático).
- `app/Views/` — Views (apenas para a home, não usadas nas rotas de API).
- `config/` — Configurações do banco e schema SQL.

## Banco de Dados

- Nome do banco: `avp2_teste1`
- Tabelas: `vagas`, `pessoa`, `candidaturas`
- O banco pode ser criado via script em `config/schema.sql`.
- O backend se conecta ao MySQL configurado em `config/database.php`.

## Comandos SQL iniciais para o banco

### 1. Permitir acesso externo ao MySQL (necessário para Docker/XAMPP)

```sql
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root';
FLUSH PRIVILEGES;
```

> Execute esses comandos no MySQL para permitir que o backend PHP (em Docker) acesse o banco do XAMPP.

### 2. Script completo para criar as tabelas

```sql
CREATE TABLE IF NOT EXISTS vagas (
    id CHAR(36) PRIMARY KEY, -- UUID
    empresa VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    localizacao CHAR(1) NOT NULL,
    nivel INT NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pessoa (
    id CHAR(36) PRIMARY KEY, -- UUID
    nome VARCHAR(255) NOT NULL,
    profissao VARCHAR(255) NOT NULL,
    localizacao CHAR(1) NOT NULL,
    nivel INT NOT NULL
);

CREATE TABLE IF NOT EXISTS candidaturas (
    id CHAR(36) PRIMARY KEY, -- UUID
    id_vaga CHAR(36) NOT NULL,
    id_pessoa CHAR(36) NOT NULL,
    score INT NOT NULL,
    data_candidatura DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vaga) REFERENCES vagas(id),
    FOREIGN KEY (id_pessoa) REFERENCES pessoa(id),
    UNIQUE (id_vaga, id_pessoa)
);
```

> Execute esse script no banco `avp2_teste1` para criar todas as tabelas necessárias.

## Rotas da API

### 1. Criar Vaga

- **POST** `/vagas`
- **Body JSON:**
  ```json
  {
    "empresa": "Empresa Exemplo",
    "titulo": "Desenvolvedor Backend",
    "descricao": "Vaga para backend PHP",
    "localizacao": "A",
    "nivel": 2
  }
  ```
- **Resposta:**
  ```json
  {
    "mensagem": "Vaga cadastrada com sucesso. Consulte o banco para validar o registro.",
    "id": "uuid-gerado"
  }
  ```

### 2. Criar Pessoa (Candidato)

- **POST** `/pessoas`
- **Body JSON:**
  ```json
  {
    "nome": "Maria Souza",
    "profissao": "Analista de Sistemas",
    "localizacao": "B",
    "nivel": 3
  }
  ```
- **Resposta:**
  ```json
  {
    "mensagem": "Pessoa cadastrada com sucesso. Consulte o banco para validar o registro.",
    "id": "uuid-gerado"
  }
  ```

### 3. Criar Candidatura

- **POST** `/candidaturas`
- **Body JSON:**
  ```json
  {
    "id_vaga": "id-da-vaga-existente",
    "id_pessoa": "id-da-pessoa-existente"
  }
  ```
- **Resposta:**
  ```json
  {
    "mensagem": "Candidatura cadastrada com sucesso. Consulte o banco para validar o registro.",
    "id": "uuid-gerado",
    "score": 123
  }
  ```

### 4. Ranking de Candidatos por Vaga

- **GET** `/vagas/{id}/candidaturas/ranking`
- **Resposta:**
  ```json
  [
    {
      "id": "uuid-pessoa",
      "nome": "Maria Souza",
      "profissao": "Analista de Sistemas",
      "localizacao": "B",
      "nivel": 3,
      "score": 123
    }
  ]
  ```

## Exemplos de Rotas e JSON

### Criar vaga

POST /vagas

```json
{
  "empresa": "Empresa Exemplo",
  "titulo": "Desenvolvedor Backend",
  "descricao": "Vaga para backend PHP",
  "localizacao": "A",
  "nivel": 2
}
```

### Criar pessoa

POST /pessoas

```json
{
  "nome": "Maria Souza",
  "profissao": "Analista de Sistemas",
  "localizacao": "B",
  "nivel": 3
}
```

### Criar candidatura

POST /candidaturas

```json
{
  "id_vaga": "<id-da-vaga>",
  "id_pessoa": "<id-da-pessoa>"
}
```

### Ranking de candidatos de uma vaga

GET /vagas/<id-da-vaga>/candidaturas/ranking

**Resposta:**

```json
[
  {
    "id": "<id-da-pessoa>",
    "nome": "Maria Souza",
    "profissao": "Analista de Sistemas",
    "localizacao": "B",
    "nivel": 3,
    "score": 123
  }
]
```

## Regras de Negócio

- **UUID:** Todos os ids são UUID v4. Se não enviados, são gerados automaticamente.
- **Score da candidatura:** Calculado conforme a lógica fornecida (nível e distância entre localizações).
- **Validações:**
  - Campos obrigatórios validados em cada rota.
  - Não permite duplicidade de candidatura (mesmo id_vaga e id_pessoa).
  - Retorna status HTTP e mensagens claras para cada situação.

## Como rodar

1. Certifique-se de ter Docker e Docker Compose instalados.
2. Execute:
   ```bash
   docker-compose up
   ```
3. O backend estará disponível em http://localhost:8123
4. O MySQL pode ser acessado conforme configurado em `config/database.php`.

## Portas utilizadas no XAMPP

- **MySQL Database:** 3307
- **Apache Web Server:** 81

> Se estiver usando o MySQL e Apache do XAMPP, configure o backend para acessar:
>
> - MySQL: host `172.17.0.1` (ou `localhost` se rodar fora do Docker), porta `3307`
> - Apache: http://localhost:81 (XAMPP) ou http://localhost:8123 (Docker)

## Observações

- O projeto não utiliza nenhum framework ou biblioteca externa.
- O roteamento é feito manualmente no `public/index.php`.
- O arquivo `.htaccess` garante o roteamento correto das URLs para o `index.php`.
- O projeto é compatível com MySQL do XAMPP, bastando ajustar o host e porta no `config/database.php`.

---

Dúvidas ou sugestões? Consulte o código ou entre em contato com o responsável pelo projeto.
