# Projeto - Autenticador de IP via QRcode
Projeto desenvolvido durante meu estágio no departamento de TI da UNESPAR - Apucarana.

Essa aplicação tem como principal objetivo permitir que apenas as pessoas conectadas ao Wi-Fi do campus de Apucarana consigam acessar os formulários de validação de presença. Esses formulários são normalmente distribuídos via QR Code ao final de eventos e palestras para a obtenção de Certificados de Participação, e a ferramenta visa restringir o acesso de pessoas que não estão fisicamente presentes no evento.

## Partes do projeto

### 1º parte - Lógica de Validação de Rede
O núcleo da aplicação utiliza PHP para identificar o endereço de IP do usuário que está fazendo o acesso. Em seguida, o sistema compara este IP com a faixa de IPs designada para a rede Wi-Fi do campus da UNESPAR (de `192.168.3.47` até `192.168.8.255`). Caso o IP do usuário esteja dentro dessa faixa, o acesso ao formulário de presença é concedido. Caso contrário, o acesso é bloqueado.

### 2º parte - Hospedagem e Testes
Inicialmente testado em um ambiente `localhost` com XAMPP, o projeto foi migrado para um servidor dedicado (Ubuntu Server) para simular um ambiente de produção real. O deployment no servidor é gerenciado com Docker, garantindo que a aplicação funcione em um contêiner isolado com todas as suas dependências, o que facilita a portabilidade e a manutenção.

![Servidor caseiro](https://github.com/user-attachments/assets/7268088c-2e2b-4425-b211-08b25ca4a288)

### 3º parte - Melhorias e Funcionalidades Adicionais

A versão inicial do projeto foi aprimorada com funcionalidades cruciais para torná-la uma ferramenta robusta, segura e de fácil manutenção pela equipe de TI.

#### Painel Administrativo com Login Seguro
Foi desenvolvido um painel administrativo protegido por um sistema de login e senha. Apenas usuários autenticados (a equipe de TI) podem acessar a área de gerenciamento, garantindo que somente pessoas autorizadas possam realizar alterações no sistema.

![login](https://github.com/user-attachments/assets/379872e9-a659-411f-a7e1-4f6101f24c77)


#### Atualização Dinâmica do Formulário
Através do painel de admin, é possível atualizar o formulário de presença de forma dinâmica. O administrador pode simplesmente colar o novo código `<iframe>` (do Google Forms ou Microsoft Forms) em uma caixa de texto e salvar. O sistema atualiza um arquivo de configuração central, e o novo formulário passa a ser exibido para os usuários imediatamente, sem a necessidade de alterar o código-fonte ou fazer um novo deploy da aplicação.

![atualizar iframe](https://github.com/user-attachments/assets/11f38536-75aa-459c-85f9-d52aa67fce26)

#### Validação de Segurança do Iframe
Para prevenir a inserção de códigos maliciosos (ataques de XSS), o sistema valida no servidor todo `<iframe>` submetido. Ele verifica se o código contém de fato uma tag `<iframe>` e se sua origem (o atributo `src`) pertence a um dos provedores permitidos (Google Forms e Microsoft Forms). Qualquer código que não passe nessa validação é rejeitado.

#### URL Amigável com DNS Local
Para profissionalizar o acesso, o antigo endereço baseado em IP (`http://192.168.3.2/ip-validator/`) foi substituído por um nome de domínio local e amigável (`http://presenca.unespar.local/ip-validator/`). Isso foi alcançado através da configuração de um "Host Override" no servidor DNS da rede (pfSense), tornando o acesso mais fácil de lembrar e compartilhar.

### Tecnologias Utilizadas no Projeto

<a href="https://skillicons.dev">
<img src="https://skillicons.dev/icons?i=git,php,vscode,bash,linux,ubuntu,docker,html,css,javascript"/>
