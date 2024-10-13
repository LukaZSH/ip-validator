## Projeto - Autenticador de IP via QRcode
Esse projeto foi desenvolvido por iniciativa minha como estagiário no departamento de T.I. da UNESPAR campus de Apucarana.

Essa aplicação tem como seu principal objetivo permitir que apenas as pessoas conectadas ao Wi-Fi do campus de Apucarana consigam acessar os formulários de validação de presença via QRcode que são fornecidos aos finais de eventos e palestras para obter os Certificados de Participação, visando tentar restringir o acesso de pessoas que não estão no evento/palestra em questão.

<h2> Partes do projeto </h2>

## 1º parte - Codificar a lógica do problema de acordo com a Arquitetura de Rede da UNESPAR
Depois de algumas tentativas falhas utilizando a linguagem de programação Python e seus recursos, um amigo sugeriu utilizar a linguagem PHP que serve muito bem para esse tipo de aplicação web com foco em Back-end. Basicamente temos que autorizar apenas os endereços de IP que o servidor da Unespar atribui aos usuários, no caso, de 1922.168.3.47 até 192.168.8.255.

Dessa forma, o código pega o IP do usuário que está acessando e compara com o range de IP's permitidos, caso esteja, acesso liberado, caso contrário, não acessa.

![Range de IP](https://github.com/user-attachments/assets/3c918f2e-16fa-458e-afbf-98dee0f677b3)

## 2º parte - Teste em Localhost e Hospedar a Aplicação web em algum servidor Back-end</h4>

Como ainda não obtive autorização para colocar essa aplicação no servidor da Universidade, então tive que utilizar outros meios para que eu pudesse fazer os testes.

Teste localhost: Utilizando o Xampp e o Apache para criar um servidor web localhost no meu computador, hospedei o projeto para ir testando até ficar tudo funcional.

OBS: No meu próprio computador não funcionava por conta do IP localhost ser 127.0.0.1, então o código estava funcionado corretamente :laughing:

![image](https://github.com/user-attachments/assets/15f17b19-adee-489c-8964-e1646c000eb9)


Teste em servidor: Utilizando um Ubuntu Server em um desktop, consegui hospedar o projeto para testar/simular se funcionaria em outro local além do meu computador.

![forms indexado](https://github.com/user-attachments/assets/d64de7cf-1dcb-4f7c-9db3-e11917c7d8d0)



## 3º parte - Estrutura do servidor "caseiro" montado para hospedar o projeto

Para montar esse servidor "caseiro", utilizei um computador de mesa (desktop) e instalei um SO de servidor, o `Ubuntu Server` , para que ele funcionasse como um servidor. Após a instalação do SO, instalei o `Docker` e todos os seus recursos. Em seguida baixei o proejto no servidor, fiz toda adaptação e configuração para o projeto funcionar corretvia contêiner Doker. O motivo de ter colocado no Docker foi mais para treinar e conhecer melhor os benefícios dessa tecnologia. Uma das vantagens do Docker é funcionar em um abiente isolado do Sistema Operacional, que são chamados de contêiner, e como o meu projeto está funcionando corrtamente no ambiente, então posso utilizar a imagem do contêiner em outro sistema que tenha o Docker instalado e o meu projeto funcionará normalmente, já que dentro do contêiner tem todas as bibliotecas, dependências e configurações necessárias para funcionar.

(imagem do Docker ou do servidor sla)

## Melhorias feitas
Já que uma vez que a pessoa acesse a página do Google forms para validar a presença dela, ela pode obter o link do forms e divulgar para outras pessoas, dessa forma, pensei em colocar o formulário dentro de outra página para "esconder" o link do forms para que as pessoas não consigam divulgar, assim o formulário vai estar em uma página que eu criei, que só será acessada se a pessoa estiver no Wi-Fi do campus.

![image](https://github.com/user-attachments/assets/c5733c20-6ebe-4d45-86fe-9bf1ba02ffde)



## Melhorias futuras
Implementar um método de trocar automaticamente o link do forms dentro do código, já que atualmente precisa trocar manualmente no código para que a página do formulário mostre-o na internet.


## :heavy_check_mark: Tecnologias utilizadas no projeto
<a href="https://skillicons.dev">
<img src="https://skillicons.dev/icons?i=git,php,vscode,bash,linux,ubuntu,docker"/>


