<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

function readCSV($csvFile) {
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle)) {
        $line_of_text[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);
    return $line_of_text;
}

$csvFile = 'movielist.csv';

$csv = readCSV($csvFile);

require_once './SQLiteManager.php';
require_once 'dao/MovieDAO.php';
require_once 'dao/StudioDAO.php';
require_once 'dao/MovieStudioDAO.php';
require_once 'dao/GoldenRaspberryAwardDAO.php';
require_once 'dao/WinnerDAO.php';
require_once 'dao/ProducerDAO.php';
require_once 'dao/MovieProducerDAO.php';

$sqliteManager = new SQLiteManager();

$movieDAO = new MovieDAO($sqliteManager->getMemoryDB());
$studioDAO = new StudioDAO($sqliteManager->getMemoryDB());
$movieStudioDAO = new MovieStudioDAO($sqliteManager->getMemoryDB());
$goldenRaspberryAwardDAO = new GoldenRaspberryAwardDAO($sqliteManager->getMemoryDB());
$winnerDAO = new WinnerDAO($sqliteManager->getMemoryDB());
$producerDAO = new ProducerDAO($sqliteManager->getMemoryDB());
$movieProducerDAO = new MovieProducerDAO($sqliteManager->getMemoryDB());


foreach ($csv as $numeroLinha => $linhaDeDados) {

    if ($numeroLinha === 0)
        continue;

    $resultado = '';
    $partes = count($linhaDeDados);

    $tam = count($linhaDeDados);
    $contador = 1;
    foreach ($linhaDeDados as $numeroDado => $d) {
        
        if ($contador > 1) $resultado .= ',' . $d;
        else $resultado .= $d;

        $contador++;
    }
    
    if (empty($resultado)) continue;
    
    $dados = explode(';', $resultado);
    
    $studios = explode(', ', $dados[2]);
    $producers = explode(', ', $dados[3]);
    
    $movieLastID = $movieDAO->insertMovie($dados[0], $dados[1]);
    foreach($studios as $studio_name) {
        $studioLastID = $studioDAO->insertStudio($studio_name);
        $movieStudioDAO->insertMovieStudio($movieLastID, $studioLastID);
    }
    
    foreach ($producers as $producer_name) {
        if (mb_strpos($producer_name, ' and ') > 0) {
            $p_name = explode(' and ', $producer_name);
            $first_producer_name = $p_name[0];
            $second_producer_name = $p_name[1];
            
            $producerLastID = $producerDAO->insertProducer($first_producer_name);
            $movieProducerDAO->insertMovieProducer($movieLastID, $producerLastID);
            
            $producerLastID = $producerDAO->insertProducer($second_producer_name);
            $movieProducerDAO->insertMovieProducer($movieLastID, $producerLastID);
        } else {
            $producerLastID = $producerDAO->insertProducer($producer_name);
            $movieProducerDAO->insertMovieProducer($movieLastID, $producerLastID);
        }
    }

    $graLastID = $goldenRaspberryAwardDAO->insertGoldenRaspberryAward($dados[0]);
    if ($dados[4]) {
        $winnerDAO->insertWinner($movieLastID, $graLastID);
    }
    
}

$movie_title = $_GET['movie_title'];

$r = $movieDAO->deleteMovie($movie_title);

http_response_code(200);
echo json_encode(
                    array('excluido'=>$r)
                );
