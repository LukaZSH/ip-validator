# Guia de Deploy com Ansible

Este guia descreve como usar o Ansible para configurar um novo servidor e fazer o deploy da aplicação `ip-validator`.

## Pré-requisitos

O servidor de destino deve ter uma instalação limpa do Ubuntu Server (versão 22.04 ou 24.04) e estar acessível via SSH.

## Passo 1: Preparar a Máquina Local (Control Node)

Estes comandos só precisam ser executados uma vez na sua máquina local para preparar o ambiente do Ansible.

1.  **Instale o Ansible:**
    ```bash
    sudo apt update
    sudo apt install ansible -y
    ```

2.  **Instale a coleção de módulos do Docker:**
    O playbook utiliza módulos da comunidade Docker para gerenciar o Docker Compose.
    ```bash
    ansible-galaxy collection install community.docker
    ```

3.  **Configure o Acesso SSH (Recomendado):**
    Para que o Ansible se conecte ao servidor sem pedir senha, copie sua chave SSH pública para o servidor de destino. Substitua `usuario` e `ip_do_servidor` pelos dados corretos.
    ```bash
    ssh-copy-id suporte@192.168.3.2
    ```

## Passo 2: Configurar o Inventário

1.  Abra o arquivo `ansible/inventory.ini`.
2.  Garanta que o IP e o nome de usuário (`ansible_user`) estejam corretos para o seu servidor de destino.

## Passo 3: Executar o Playbook

Este é o comando que executa a automação.

1.  Navegue até a pasta `ansible` do projeto:
    ```bash
    cd /path/to/your/project/ip-validator/ansible
    ```

2.  Execute o playbook:
    ```bash
    ansible-playbook -i inventory.ini playbook.yml
    ```

O Ansible irá se conectar ao servidor e executar todas as tarefas definidas no `playbook.yml`. Ao final do processo, a aplicação estará configurada e rodando nos contêineres Docker.
