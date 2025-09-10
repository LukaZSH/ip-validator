# Relatório de Diagnóstico: Aplicação `ip-validator`

**Data:** 02 de Setembro de 2025
**Autor do Relatório:** Gemini
**Proprietário do Sistema:** Luka Alves (LukaZSH)

## 1. Resumo Executivo

O objetivo deste relatório é documentar o processo de diagnóstico e as ações corretivas aplicadas ao servidor da aplicação `ip-validator`.

*   **Problema Inicial:** A aplicação retornava um erro `403 Forbidden`, impedindo qualquer acesso.
*   **Diagnóstico Final:** Após uma investigação exaustiva, concluiu-se que o erro persistente é causado por um **fator ambiental no servidor host** que força o contêiner a se comportar como se um "bind mount" estivesse ativo, mesmo quando a configuração do Docker Compose não o declara. Isso leva a um erro de `DocumentRoot` no Apache.
*   **Estado Atual:** Os arquivos de configuração do projeto (`Dockerfile`, `docker-compose.yml`, etc.) foram corrigidos e agora seguem as melhores práticas, mas o problema ambiental impede o funcionamento correto.

## 2. Histórico do Problema e da Investigação (Cronologia)

A investigação seguiu um processo de eliminação de hipóteses, detalhado abaixo.

### Hipótese 1: `DocumentRoot` Incorreto na Imagem
*   **Sintoma:** Erro 403/500, com logs do Apache indicando `Cannot serve directory /var/www/html/`.
*   **Análise:** Este log indicava que o `DocumentRoot` do Apache estava incorreto, apontando para a raiz do projeto em vez de `/var/www/html/public`.
*   **Ações Corretivas:**
    1.  O `Dockerfile` foi modificado para alterar o arquivo de configuração do Apache, setando o `DocumentRoot` para `/var/www/html/public`.
    2.  Após falhas de sintaxe, a configuração foi reescrita usando a sintaxe `heredoc`, que é mais robusta.
    3.  Foi adicionado o comando `a2ensite 000-default.conf` para garantir que a configuração do site fosse efetivamente habilitada pelo Apache.
*   **Resultado:** O erro persistiu, indicando que as alterações na imagem não estavam sendo aplicadas no contêiner em execução.

### Hipótese 2: Problema de Build (Cache/Dependências)
*   **Sintoma:** Em um ponto, surgiu um erro fatal do PHP indicando a ausência de um arquivo do `phpstan` (uma dependência de desenvolvimento).
*   **Análise:** A causa era o comando `COPY . .` no `Dockerfile` que sobrescrevia a pasta `vendor` de produção (instalada com `--no-dev`) com a pasta `vendor` do ambiente de build, que continha referências a pacotes de desenvolvimento.
*   **Ações Corretivas:**
    1.  Um arquivo `.dockerignore` foi criado para excluir a pasta `vendor` do processo de cópia.
    2.  Para garantir uma solução definitiva, o `Dockerfile` foi reestruturado para um **formato multi-stage**, que isola completamente a instalação de dependências, uma melhor prática para builds de produção.
*   **Resultado:** A estrutura do `Dockerfile` tornou-se mais robusta e correta, eliminando a causa de erros de dependência.

### Hipótese 3: "Bind Mount" Incorreto em Produção
*   **Sintoma:** O erro 403 original persistia. A inspeção do contêiner (`ls -la`) revelou que os arquivos do host estavam espelhados dentro do contêiner, ignorando o conteúdo da imagem.
*   **Análise:** A causa para todos os sintomas era um "bind mount" ativo, que ignorava o conteúdo da imagem e as permissões definidas no `Dockerfile`.
*   **Ações Corretivas:**
    1.  A diretiva `volumes: - .:/var/www/html` foi removida do `docker-compose.yml`.
*   **Resultado:** O erro persistiu, levando à conclusão de que o "bind mount" estava sendo forçado por um mecanismo desconhecido.

### Hipótese 4: Problema de Permissões no Host
*   **Sintoma:** Erros de "dubious ownership" e "permission denied" no Git.
*   **Ação:** Aplicação da estrutura de permissões padrão para servidores web (`suporte:www-data`, `775`/`664`).
*   **Resultado:** Corrigiu os erros do Git, mas o erro 403 do Apache retornou.

## 3. Estado Atual e Diagnóstico Final Detalhado

*   **A Prova Definitiva (`docker inspect`):** O comando `docker inspect` mostrou que **não há** bind mounts configurados para o contêiner (`"Mounts": []`).
*   **O Paradoxo:** Existe uma contradição central: o `docker inspect` diz que não há bind mount, mas os logs do Apache e a listagem de arquivos dentro do contêiner provam que ele se comporta como se houvesse.
*   **Conclusão:** A causa raiz é um fator externo ao projeto, no ambiente do servidor host, que está forçando o Docker a montar o volume.

## 4. Configuração Recomendada (Estado Atual do Projeto)

Os seguintes arquivos de configuração foram corrigidos e representam o estado ideal para o projeto:

*   **`Dockerfile`:** Utiliza uma build multi-stage para criar uma imagem de produção limpa e segura.
*   **`docker-compose.yml`:** Configurado para usar a imagem do GHCR, sem o "bind mount" para o código da aplicação.
*   **`.dockerignore`:** Presente para evitar a cópia de arquivos desnecessários para a imagem.
*   **Permissões no Host:** O diretório `/var/www/ip-validator` está com as permissões `suporte:www-data` e `775`/`664`, permitindo o gerenciamento pelo usuário e o acesso pelo servidor web.

## 5. Próximos Passos Recomendados para Investigação

A investigação deve focar no ambiente do servidor host:

1.  **Analisar Logs de Segurança:** Verificar os logs de auditoria do sistema para encontrar possíveis bloqueios por módulos de segurança como AppArmor ou SELinux.
    *   Comandos sugeridos: `sudo journalctl -u apparmor`, `sudo cat /var/log/audit/audit.log`
2.  **Testar em Ambiente Limpo:** Fazer o deploy do projeto em uma nova máquina virtual limpa para validar que os arquivos do projeto estão, de fato, funcionais.
3.  **Inspecionar Configuração do Docker:** Revisar a configuração do serviço (daemon) do Docker no servidor (`/etc/docker/daemon.json`) em busca de diretivas globais que possam afetar a criação de volumes.
