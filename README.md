<p align="right">
  <a href="README-en.md" title="English"><img src="https://flagcdn.com/w40/us.png" width="40" alt="English"></a>
  &nbsp;&nbsp;
  <a href="README.md" title="Português"><img src="https://flagcdn.com/w40/br.png" width="30" alt="Português"></a>
</p>

# 🚀 Sistema de Validação de Presença v2.0

<p align="center">
  <img src="https://go-skill-icons.vercel.app/api/icons?i=php,apache,mysql,docker,bash,linux,ubuntu,git,githubactions,grafana,ansible" />
</p>

## 📄 Visão Geral do Projeto

O **Sistema de Validação de Presença** é uma aplicação web robusta desenvolvida para modernizar e proteger o processo de registro de presença em eventos acadêmicos no campus da UNESPAR - Apucarana. O projeto evoluiu de um simples script de validação de IP para uma solução completa, demonstrando um fluxo de trabalho DevOps abrangente, desde a infraestrutura como código até o monitoramento em tempo real.

A aplicação resolve problemas críticos como o registro manual ineficiente, fraudes de presença e a dependência da equipe de TI para gerenciamento de eventos.

## 🛠️ Pilares e Tecnologias Aplicadas

| Pilar Chave | Ferramentas e Conceitos Aplicados |
|---|---|
| **Containerização** | **Docker e Docker Compose** para empacotar a aplicação PHP, o banco de dados MySQL e a stack de monitoramento, garantindo um ambiente consistente e isolado. |
| **CI/CD (Integração e Deploy Contínuo)** | **GitHub Actions** para automatizar a análise estática (`PHPStan`), auditoria de segurança (`Composer`), testes de integração, build e publicação da imagem no GitHub Container Registry (GHCR). |
| **Observabilidade** | **Stack PLG (Promtail, Loki, Grafana)** para coleta, armazenamento e visualização de logs em tempo real, permitindo o diagnóstico instantâneo de problemas. |
| **Infraestrutura como Código (IaC)** | **Ansible** para automatizar a configuração de um servidor Ubuntu do zero, instalando Docker, configurando usuários e clonando o projeto, tornando a infraestrutura totalmente reproduzível. |
| **Segurança** | Implementação de múltiplas camadas de validação (IP, horário do evento, trava anti-fraude), painel administrativo com login seguro e validação de `<iframe>` para prevenir XSS. |

---

## 🏛️ Arquitetura da Solução

A aplicação é orquestrada pelo Docker Compose e dividida nos seguintes serviços que se comunicam em uma rede interna (`app-net`):
- **`web`**: O contêiner principal com a aplicação PHP rodando em um servidor Apache.
- **`db`**: O banco de dados MySQL 8.0 para persistência de dados de usuários, eventos e presenças.
- **`loki`**, **`promtail`**, **`grafana`**: A stack de observabilidade para monitoramento de logs.

O ambiente de produção é hospedado em um servidor dedicado no campus.
<p align="center">
  <img src="https://github.com/user-attachments/assets/7268088c-2e2b-4425-b211-08b25ca4a288" alt="Servidor caseiro" width="600"/>
</p>

---

## ✨ Funcionalidades Principais (Showcase)

### Painel Administrativo Seguro
Um painel de gerenciamento protegido por login permite que a equipe de TI gerencie todo o ciclo de vida dos eventos (criar, editar, excluir) sem precisar intervir no código.

<p align="center">
  <img src="https://github.com/user-attachments/assets/379872e9-a659-411f-a7e1-4f6101f24c77" alt="Tela de Login" width="600"/>
</p>

O administrador pode atualizar dinamicamente o formulário de presença (Google Forms, etc.) e gerar QR Codes para o evento com um único clique.

<p align="center">
  <img width="1267" height="415" alt="image" src="https://github.com/user-attachments/assets/ef40e7ea-2d34-4e2e-b015-a34ac1238fb5" />
</p>


### Fluxo de Validação do Aluno
Para garantir a integridade do registro, o aluno passa por uma cadeia de validações:
1.  **Validação de IP**: Verifica se o acesso vem da rede Wi-Fi do campus.
2.  **Validação de Horário**: Confere se o registro está sendo feito dentro da janela de tempo do evento.
3.  **Trava Anti-Fraude**: Impede que um mesmo aluno registre presença mais de uma vez no mesmo dia.

---

## 🔄 Fluxo de Trabalho DevOps

### Pipeline de CI/CD com GitHub Actions
A pipeline é acionada a cada push na branch `master` e executa uma série de verificações para garantir a qualidade e a segurança do código antes de publicar a nova versão da imagem Docker.

<img width="1007" height="394" alt="image" src="https://github.com/user-attachments/assets/49bdcae5-7816-4b59-ae54-6aea6ab39ca0" />


### Monitoramento com Grafana
A stack de observabilidade permite visualizar e pesquisar os logs de todos os contêineres em tempo real através de um dashboard no Grafana, essencial para a depuração durante os eventos.

### Dashboard com estatísticas da Aplicação

<img width="1919" height="1079" alt="Print do Grafana configurado" src="https://github.com/user-attachments/assets/8b6efcbc-26ea-4d18-b910-fa1b0e2f68b9" />

---
### Dashboard com estatísticas da Infraestrutura do servidor

<img width="1915" height="889" alt="Print do Grafana - Infra" src="https://github.com/user-attachments/assets/0495b455-55ae-45d9-b99c-f2d72009c11e" />


### Infraestrutura como Código com Ansible
A configuração do servidor de produção é totalmente automatizada com Ansible. O playbook prepara um servidor Ubuntu limpo, instala todas as dependências e faz o deploy da aplicação.
