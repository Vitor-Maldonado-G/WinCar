-- Execute uma vez no banco wincar existente. Nao apaga contas ou agendamentos.
CREATE TABLE IF NOT EXISTS recuperacao_senha (
    id_cliente INT NOT NULL PRIMARY KEY,
    token_hash CHAR(64) NOT NULL UNIQUE,
    solicitado_em DATETIME NOT NULL,
    expira_em DATETIME NOT NULL,
    usado_em DATETIME NULL,
    CONSTRAINT fk_recuperacao_cliente FOREIGN KEY (id_cliente)
        REFERENCES cliente(id_cliente) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
