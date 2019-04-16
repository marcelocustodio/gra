INSTALAÇÃO
==========


O presente projeto foi desenvoldio na plataforma Linux Debian 9.

Assumem-se como instalados:

- PHP 7.2 ou superior
- Servidor Apache HTTPD
- Composer
- Postman para as requisições contra a API
- Git


Assumime-se como pasta raiz do projeto "/var/www/html/goldenraspberryawards"


0) Baixa-se o projeto do repositório 


1) posiciona-se o arquivo movielist.csv na raiz do projeto


2) Instalação do banco em memória SQLite3

sudo apt-get install sqlite3
sudo apt-get install php7.2-sqlite

Remover o comentário (";") dessas duas linhas do arquivo php.ini ( /etc/php/7.2/cli/php.ini ) :
extension=pdo_sqlite
extension=sqlite3

no caso do Windows : extension=php_pdo_sqlite.dll

reiniciar o servidor Apache ( /etc/init.d/apache2 restart )


3) Instalação do PHPUnit para os testes unitários

Antes, deve-se instalar a biblioteca:

sudo apt-get update
sudo apt-get install php7.2-xml

Agora, não como usuário root, proceder à instalação do PHPUnit como uma dependência ao projeto. Dentro da pasta raiz do mesmo:

composer require phpunit/phpunit

Será criado o arquivo composer.json


4) Instalação do Guzzle para se testar as requisições da API em cojunto com o PHPUnit

Igualmente, não como usuário root, deve-se instalar o Guzzle via composer:

composer require guzzlehttp/guzzle

composer update


Com as dependências no lugar, agora podemos configurar o PHPUnit para usar o Guzzle. Para isso, dizemos devemos informar ao PHPUnit onde o arquivo autoload do Composer bem como os teste estão. Podemos fazer isso criando o arquivo phpunit.xml na raiz do projeto:

<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="REST API Test Suite">
            <directory suffix=".php">./tests/</directory>
        </testsuite>
    </testsuites>
</phpunit>


Criamos a pasta "tests" na raiz onde posicionaremos o arquivo com os testes UserAgentTest.php



TESTES
======

Para se testar com PHPunit e Guzzle, basta entrar na pasta raiz e digitar no prompt:

./vendor/bin/phpunit --bootstrap vendor/autoload.php tests/UserAgentTest.php

Você verá algo como :

PHPUnit 8.1.2 by Sebastian Bergmann and contributors.

......                                                              6 / 6 (100%)

Time: 532 ms, Memory: 4.00 MB

OK (6 tests, 7 assertions)




REQUISIÇÕES NO POSTMAN
======================


1) Obter o(s) vencedor(es), informando um ano:

http://localhost/goldenraspberryawards/obterVencedoresDoAno.php?year=1986


2) Obter os anos que tiveram mais de um vencedor:

http://localhost/goldenraspberryawards/obterAnosQueTiveramMaisDeUmVencedor.php


3) Obter a lista de estúdios, ordenada pelo número de premiações:

http://localhost/goldenraspberryawards/obterListaDeEstudiosOrdenadaPeloNumeroDePremiacoes.php


4) Obter o produtor com maior intervalo entre dois prêmios, e o que obteve dois prêmios mais rápido:

http://localhost/goldenraspberryawards/obterListaDeEstudiosOrdenadaPeloNumeroDePremiacoes.php


5) Excluir um filme. Não deve permitir excluir vencedores:

http://localhost/goldenraspberryawards/excluirUmFilme.php?movie_title=Mommie Dearest

ou

http://localhost/goldenraspberryawards/excluirUmFilme.php?movie_title=Endless Love



