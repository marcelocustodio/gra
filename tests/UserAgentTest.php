<?php

use PHPUnit\Framework\TestCase;

class UserAgentTest extends TestCase {

    private $http;
    
    public function testObterVencedoresDoAno() {

        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->request(
                'GET',
                'http://localhost/goldenraspberryawards/obterVencedoresDoAno.php?year=1986'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        $this->assertEquals($string, '{"ganhadores":["Howard the Duck","Under the Cherry Moon"]}');
        
    }
    
    public function testObterAnosQueTiveramMaisDeUmVencedor() {

        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->request(
                'GET',
                'http://localhost/goldenraspberryawards/obterAnosQueTiveramMaisDeUmVencedor.php'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        $json = json_decode($string);
        
        $anos = array(
            "0" => "1986",
            "2" => "1990",
            "4" => "2015"
        );
        $array = (array) $json->anos;
        
        $equals = $anos == $array;
        
        $this->assertEquals(true, $equals);
    }
    
    
    public function testObterListaDeEstudiosOrdenadaPeloNumeroDePremiacoes() {

        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->request(
                'GET',
                'http://localhost/goldenraspberryawards/obterListaDeEstudiosOrdenadaPeloNumeroDePremiacoes.php'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        $json = json_decode($string);
        
        $this->assertEquals(6, $json->studios->{'Columbia Pictures'});
    }
    
    public function testObterProdutorComMaiorIntervaloEntreDoisPremios() {
        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->request(
                'GET',
                'http://localhost/goldenraspberryawards/obterListaDeEstudiosOrdenadaPeloNumeroDePremiacoes.php'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        $json = json_decode($string);
        
        $this->assertEquals(6, $json->studios->{'Columbia Pictures'});
    }
    
    public function testObterProdutorComMaiorEMenorIntervaloEntreDoisPremios() {
        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->request(
                'GET',
                'http://localhost/goldenraspberryawards/obterProdutorComMaiorEMenorIntervaloEntreDoisPremios.php'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        
        $this->assertEquals($string, 
                '{"produtores":{"max":{"producer":"Matthew Vaughn","interval":13,"previousWin":"2002","followingWin":"2015"},"min":{"producer":"Joel Silver","interval":1,"previousWin":"1990","followingWin":"1991"}}}'
                );
    }
    
    public function testExcluirUmFilme() {
        
        $guzzle = new \GuzzleHttp\Client();
        
        $response = $guzzle->request(
                'DELETE',
                'http://localhost/goldenraspberryawards/excluirUmFilme.php?movie_title=Mommie Dearest'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        $this->assertEquals($string, '{"excluido":false}');
        
        
        $response = $guzzle->request(
                'DELETE',
                'http://localhost/goldenraspberryawards/excluirUmFilme.php?movie_title=Endless Love'
                );
        $body = $response->getBody();
        $string = $body->getContents();
        $this->assertEquals($string, '{"excluido":true}');
        
    }
    
}
