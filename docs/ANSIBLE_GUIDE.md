# Guia de Execução do Ansible

Este guia detalha os passos para provisionar um novo servidor Ubuntu para rodar a aplicação **Sistema de Validação de Presença** usando o playbook Ansible contido neste repositório.

## Pré-requisitos

1.  **Ansible Instalado:** Você precisa ter o Ansible instalado na sua máquina local (a máquina que irá controlar o servidor).
    *   Para instalar no macOS: `brew install ansible`
    *   Para instalar em sistemas baseados em Debian/Ubuntu: `sudo apt-get install ansible`

2.  **Acesso SSH ao Servidor:** Você deve ter acesso SSH ao servidor de destino usando uma chave pública para não precisar digitar a senha. Certifique-se de que sua chave pública (`~/.ssh/id_rsa.pub`) está no arquivo `~/.ssh/authorized_keys` do usuário de deploy no servidor.

3.  **Servidor Ubuntu Limpo:** O playbook foi projetado para rodar em uma instalação fresca do Ubuntu 20.04 ou 22.04.

## Configuração

1.  **Edite o Inventário (`inventory.ini`)**

    Abra o arquivo `ansible/inventory.ini` e configure as seguintes variáveis:

    ```ini
    [producao]
    # Substitua pelo IP ou hostname do seu servidor
    servidor_unespar ansible_host=192.168.3.2

    [producao:vars]
    # Substitua pelo nome do usuário que você usa para o deploy via SSH
    ansible_user=suporte
    # Opcional: se sua chave privada não estiver no local padrão
    # ansible_ssh_private_key_file=~/.ssh/outra_chave
    ```

2.  **Verifique a Conectividade**

    Antes de rodar o playbook, teste se o Ansible consegue se conectar ao seu servidor:

    ```bash
    ansible producao -m ping
    ```

    A saída esperada é um `SUCCESS` em verde, indicando que a conexão foi bem-sucedida:

    ```json
    servidor_unespar | SUCCESS => {
        "changed": false,
        "ping": "pong"
    }
    ```

## Execução do Playbook

Com a configuração validada, execute o playbook principal para provisionar o servidor. O comando deve ser executado de dentro da pasta `ansible/`.

```bash
cdd ansible/
ansible-playbook playbook.yml
```

O Ansible irá executar todas as tarefas definidas no `playbook.yml`:

1.  Atualizar os pacotes do sistema (`apt update` e `apt upgrade`).
2.  Instalar dependências essenciais (`git`, `curl`, etc.).
3.  Adicionar o repositório oficial do Docker.
4.  Instalar o Docker Engine e o Docker Compose.
5.  Adicionar o `ansible_user` ao grupo `docker` para que ele possa executar comandos do Docker sem `sudo`.
6.  Clonar o repositório da aplicação do GitHub para o `home` do usuário.
7.  Iniciar todos os serviços da aplicação usando `docker-compose up -d`.

Ao final da execução, a aplicação estará rodando e acessível no IP do servidor.