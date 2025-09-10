-- Script SQL para criar usuário MySQL e database
-- Execute este script como root no MySQL

-- Criar o usuário MySQL se não existir
CREATE USER IF NOT EXISTS 'validator_user'@'%' IDENTIFIED BY '1n&$p@r';

-- Conceder permissões completas no banco de dados da aplicação
GRANT ALL PRIVILEGES ON ip_validator_db.* TO 'validator_user'@'%';

-- Aplicar as mudanças
FLUSH PRIVILEGES;

-- Verificar se o usuário foi criado
SELECT User, Host FROM mysql.user WHERE User = 'validator_user';