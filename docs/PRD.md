# PRD: Sistema de Validação de Presença v2.0

**Autor:** Luka Alves | **Versão:** 2.0 | **Data:** 29 de Agosto de 2025

## 1. Visão Geral e Problema Resolvido

O **Sistema de Validação de Presença** é uma aplicação web robusta projetada para modernizar e proteger o processo de registro de presença em eventos acadêmicos no campus da UNESPAR - Apucarana.

O sistema foi evoluído de um script de validação de IP simples para uma aplicação completa que resolve os seguintes problemas críticos:

*   **Ineficiência do Registro Manual:** Elimina a necessidade de listas de presença em papel, que são propensas a erros, perdas e demandam tempo para digitalização.
*   **Fraude de Presença:** Impede que alunos registrem presença sem estarem fisicamente no campus, garantindo a validade dos certificados de horas através de um sistema de validação tripla (IP, horário e registro único).
*   **Dependência da Equipe de TI:** Oferece um painel de administração autônomo onde a equipe de TI pode gerenciar todo o ciclo de vida dos eventos (criação, configuração de horários, associação de formulários) sem precisar de intervenção no código.
*   **Falta de Visibilidade:** Introduz um sistema de monitoramento em tempo real que permite à equipe de TI diagnosticar problemas de acesso ou erros na aplicação instantaneamente durante um evento.

## 2. Arquitetura e Tecnologias

O projeto é construído sobre uma stack de tecnologias modernas, containerizadas e alinhadas com as práticas de DevOps.

### 2.1. Aplicação Principal (`web`)

*   **Linguagem (PHP 8.1):** O backend é construído em PHP, uma linguagem robusta e amplamente utilizada para desenvolvimento web.
*   **Servidor Web (Apache):** O servidor Apache, rodando dentro do contêiner `web`, é responsável por servir a aplicação PHP e os assets estáticos.
*   **Gerenciador de Dependências (Composer):** Utilizado para gerenciar as bibliotecas do projeto, garantindo que as dependências sejam consistentes e seguras.
*   **Principais Bibliotecas:**
    *   `pecee/simple-router`: Para um roteamento MVC (Model-View-Controller) limpo, centralizando todas as requisições no `index.php` e direcionando-as para os `controllers` apropriados.
    *   `endroid/qr-code`: Para a geração dinâmica de QR Codes personalizados com o logo da instituição e alto nível de correção de erros.

### 2.2. Banco de Dados (`db`)

*   **Sistema (MySQL 8.0):** Um banco de dados relacional robusto para persistir todos os dados da aplicação.
*   **Estrutura de Dados:**
    *   `users`: Armazena as credenciais dos administradores do painel, com senhas criptografadas usando o algoritmo `password_hash()` do PHP.
    *   `events`: Tabela central que armazena todos os detalhes de cada evento (nome, slug da URL, horários de início e fim, status e o código do iframe do formulário).
    *   `presences`: Tabela que implementa a trava anti-fraude, registrando cada acesso bem-sucedido por IP e por dia. Possui uma chave única (`UNIQUE KEY` na combinação de IP e data) para impedir registros duplicados.

### 2.3. Ambiente de Execução: Docker

A aplicação inteira é orquestrada pelo **Docker Compose**, garantindo um ambiente de desenvolvimento, teste e produção consistente e isolado.

*   **Serviços:** 5 contêineres no total.
    *   `web`: A aplicação PHP com o servidor Apache.
    *   `db`: O banco de dados MySQL, com um `healthcheck` que garante que a aplicação só inicie depois que o banco de dados esteja pronto para receber conexões.
    *   `loki`, `promtail`, `grafana`: A stack de observabilidade.
*   **Rede:** Todos os serviços comunicam-se através de uma rede Docker interna (`app-net`), o que significa que apenas as portas essenciais (80 para a web, 3000 para o Grafana) são expostas ao host, aumentando a segurança.

## 3. Funcionalidades Detalhadas

### 3.1. Painel de Administração

*   **Acesso Seguro:** Protegido por um sistema de login que valida as credenciais contra a tabela `users`.
*   **CRUD de Eventos:** Interface completa para Criar, Ler, Atualizar e Excluir eventos.
*   **Validação Robusta no Backend:** Impede a inserção de dados inválidos, como slugs de URL duplicados, datas de fim anteriores às de início e campos obrigatórios em branco.
*   **Feedback ao Usuário:** Utiliza "flash messages" na sessão para fornecer feedback claro sobre o sucesso ou falha das operações (ex: "Evento salvo com sucesso!").
*   **Geração de QR Code:** Um clique gera um QR Code customizado que aponta para a URL única do evento, pronto para ser distribuído.

### 3.2. Fluxo do Aluno (Página Pública do Evento)

O coração do sistema, que executa uma cadeia de validações para garantir a integridade do registro:

1.  **Acesso:** O aluno escaneia o QR Code e acessa uma URL única (ex: `.../evento/palestra-ia`).
2.  **Barreira 1: Validação de IP:** O sistema verifica se o IP do aluno pertence à faixa de IPs da rede interna do campus.
3.  **Barreira 2: Validação de Tempo:** O sistema verifica se a hora atual está dentro da janela de início e fim definida para aquele evento.
4.  **Barreira 3: Trava Anti-Fraude:** O sistema tenta inserir o IP do aluno na tabela `presences` para o dia atual.
    *   **Sucesso:** Se a inserção for bem-sucedida (primeiro acesso do dia), o formulário do evento é exibido.
    *   **Falha (IP Duplicado):** Se a inserção falhar, o sistema assume que o aluno já se registrou e exibe a mensagem de "presença já registrada", bloqueando o acesso para evitar duplicidade.

## 4. Fluxo de Trabalho de DevOps

### 4.1. CI/CD com GitHub Actions

O arquivo `.github/workflows/pipeline-CI-CD.yaml` define uma pipeline de integração e deploy contínuo que garante a qualidade e segurança do código.

*   **Gatilho Inteligente:** A pipeline é acionada a cada `push` na `main`, mas ignora alterações em arquivos de documentação (`.md`, etc.) para economizar recursos.
*   **Etapa 1: Análise Estática e de Segurança:**
    *   `PHPStan` (nível 5): Analisa o código em busca de erros lógicos e de tipagem.
    *   `composer audit`: Verifica as dependências em busca de vulnerabilidades de segurança conhecidas.
*   **Etapa 2: Testes, Build e Scan de Imagem:**
    *   Um contêiner MySQL temporário é iniciado para os testes.
    *   O script `tests/DatabaseConnectionTest.php` valida a conectividade com o banco de dados.
    *   A imagem Docker da aplicação é construída.
    *   `Trivy` escaneia a imagem recém-construída em busca de vulnerabilidades no sistema operacional e nas bibliotecas.
*   **Etapa 3: Publicação:**
    *   Se todas as etapas anteriores forem bem-sucedidas, a imagem final é publicada de forma segura no **GitHub Container Registry (GHCR)**.

### 4.2. Deploy em Produção

*   **Script de Automação (`atualizar-imagem-GHCR.sh`):** Um script no servidor simplifica o processo de deploy para um único comando, que puxa a imagem mais recente do GHCR e reinicia os contêineres.

## 5. Observabilidade (Stack PLG)

*   **Coleta (Promtail):** É o agente que descobre e coleta logs de todos os contêineres Docker automaticamente e os envia para o Loki.
*   **Armazenamento (Loki):** É o banco de dados de logs, otimizado para armazenar e indexar logs de forma eficiente, com base em labels.
*   **Visualização (Grafana):** É a plataforma de visualização (acessível em `http://192.168.3.2:3000`) que é provisionada automaticamente com o Loki como fonte de dados, permitindo a pesquisa e visualização em tempo real de todos os logs da aplicação.

## 6. Infraestrutura como Código com Ansible

A configuração do servidor de produção foi **implementada** e é totalmente automatizada utilizando Ansible, tornando a infraestrutura resiliente e reproduzível.

*   **Componentes:**
    *   `ansible/inventory.ini`: Arquivo de inventário que define os hosts de produção e suas variáveis de conexão (usuário, IP).
    *   `ansible/playbook.yml`: O playbook que contém a sequência de tarefas para provisionar o servidor.
*   **Fluxo de Automação:**
    1.  Conecta-se ao servidor Ubuntu.
    2.  Atualiza os pacotes do sistema.
    3.  Instala as dependências necessárias: `git`, `curl`, etc.
    4.  Adiciona o repositório oficial do Docker e instala o Docker Engine e o Docker Compose.
    5.  Adiciona o usuário de deploy ao grupo `docker`.
    6.  Clona o repositório da aplicação do GitHub.
    7.  Inicia a aplicação executando `docker-compose up`.
*   **Guia de Execução:** As instruções detalhadas para rodar o playbook estão no arquivo **[ANSIBLE_GUIDE.md](ANSIBLE_GUIDE.md)**.