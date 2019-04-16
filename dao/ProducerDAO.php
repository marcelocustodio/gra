<?php

class ProducerDAO {

    private $memory_db;

    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }

    public function insertProducer($producer_name) {

        $ultimoID = 0;

        // Set default timezone
        date_default_timezone_set('UTC');
        $producer = NULL;

        try {

            $id_producer = $this->findProducerByName($producer_name);

            if ($id_producer === 0) {

                $insert = "INSERT INTO producer (producer) VALUES (:producer)";
                $stmt = $this->memory_db->prepare($insert);
                $stmt->bindParam(':producer', addslashes($producer_name));

                $resultado = $stmt->execute();

                $ultimoID = $this->findProducerByName($producer_name);
            } else {
                $ultimoID = $id_producer;
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $ultimoID;
    }

    function findProducerByName($producer_name) {

        $producer_name = addslashes($producer_name);

        $result = $this->memory_db->query(
                'SELECT id, producer '
                . 'FROM producer '
                . 'WHERE producer = "' . $producer_name . '"'
        );

        $id = 0;
        $producer = '';

        foreach ($result as $row) {
            $id = $row['id'];
            $producer = $row['producer'];
        }

        return $id;
    }

    function findAwardingMaxMinInterval() {

        $query = 'SELECT '
                . 'p.producer as p_producer, '
                . 'mp.id_producer as mp_id_producer, '
                . 'mp.id_movie as mp_id_movie, '
                . 'm.title as m_title, '
                . 'gra.year as gra_year '
                . 'FROM producer p JOIN movie_producer mp '
                . 'ON p.id = mp.id_producer JOIN movie m '
                . 'ON m.id = mp.id_movie JOIN winner w '
                . 'ON w.id_movie = m.id JOIN goldenraspberryaward gra '
                . 'ON gra.id = w.id_goldenraspberryaward ';

        $result = $this->memory_db->query($query);

        $todosDadosDosProdutoresVencedores = array();
        foreach ($result as $row) {
            $todosDadosDosProdutoresVencedores[] = array(
                'p_producer' => $row['p_producer'],
                'mp_id_producer' => $row['mp_id_producer'],
                'mp_id_movie' => $row['mp_id_movie'],
                'm_title' => $row['m_title'],
                'p_producer' => $row['p_producer'],
                'gra_year' => $row['gra_year']
            );
        }


        $nomeEAnosVitoriasDosProdutores = array();

        $tamanho = count($todosDadosDosProdutoresVencedores);
        for ($i = 0; $i < $tamanho; ++$i) {

            if (!in_array($todosDadosDosProdutoresVencedores[$i]['gra_year'], 
                    $nomeEAnosVitoriasDosProdutores[$todosDadosDosProdutoresVencedores[$i]['p_producer']])) {
                $nomeEAnosVitoriasDosProdutores[$todosDadosDosProdutoresVencedores[$i]['p_producer']][] = $todosDadosDosProdutoresVencedores[$i]['gra_year'];
            }

            for ($j = i + 1; $j < $tamanho; ++$j) {
                //
                if (($i != $j) && ($todosDadosDosProdutoresVencedores[$i]['p_producer'] === $todosDadosDosProdutoresVencedores[$j]['p_producer'])) {
                    if (!in_array($todosDadosDosProdutoresVencedores[$j]['gra_year'], 
                            $nomeEAnosVitoriasDosProdutores[$todosDadosDosProdutoresVencedores[$i]['p_producer']])) {
                        $nomeEAnosVitoriasDosProdutores[$todosDadosDosProdutoresVencedores[$i]['p_producer']][] = $todosDadosDosProdutoresVencedores[$j]['gra_year'];
                    }
                }
            }
        }

        // Procurar o maior intervalo entre dois prêmios
        $maiorIntervaloEntreTodosProdutores = 0;
        $produtorDoMaiorIntervalo = '';
        $previousWinDesteProdutor = 0;
        $followingWinDesteProdutor = 0;
        
        foreach($nomeEAnosVitoriasDosProdutores as $nome=>$anosDeVitoria) {
            $tam = count($anosDeVitoria);
            $maiorIntervaloParaEsteProdutor = 0;
            $previousWin = 0;
            $followingWin = 0;
            for($x=0; $x<($tam-1); ++$x) {
                $intervalo = $anosDeVitoria[$x+1] - $anosDeVitoria[$x];
                if ($intervalo > $maiorIntervaloParaEsteProdutor) {
                    $maiorIntervaloParaEsteProdutor = $intervalo;
                    $previousWin = $anosDeVitoria[$x];
                    $followingWin = $anosDeVitoria[$x+1];
                }
            }
            
            if ($maiorIntervaloParaEsteProdutor > $maiorIntervaloEntreTodosProdutores) {
                $produtorDoMaiorIntervalo = $nome;
                $previousWinDesteProdutor = $previousWin;
                $followingWinDesteProdutor = $followingWin;
                $maiorIntervaloEntreTodosProdutores = $maiorIntervaloParaEsteProdutor;
            }
        }
        
        
        $arranjoComMaiorEMenorIntervalo = array();
        $arranjoComMaiorEMenorIntervalo['max'] = array(
            'producer' => $produtorDoMaiorIntervalo,
            'interval' => ($followingWinDesteProdutor - $previousWinDesteProdutor),
            'previousWin' => $previousWinDesteProdutor,
            'followingWin' => $followingWinDesteProdutor
        );
        
        // Procurar o menor intervalo entre dois prêmios
        $menorIntervaloEntreTodosProdutores = 1000;
        $produtorDoMenorIntervalo = '';
        $previousWinDesteProdutor = 0;
        $followingWinDesteProdutor = 0;
        
        foreach($nomeEAnosVitoriasDosProdutores as $nome=>$anosDeVitoria) {
            $tam = count($anosDeVitoria);
            $menorIntervaloParaEsteProdutor = 100;
            $previousWin = 0;
            $followingWin = 0;
            
            if ($tam>1) {
            for($x=0; $x<($tam-1); ++$x) {
                $intervalo = $anosDeVitoria[$x+1] - $anosDeVitoria[$x];
                if ($intervalo>0 && $intervalo < $menorIntervaloParaEsteProdutor) {
                    $menorIntervaloParaEsteProdutor = $intervalo;
                    $previousWin = $anosDeVitoria[$x];
                    $followingWin = $anosDeVitoria[$x+1];
                }
            }
            
            if ($menorIntervaloParaEsteProdutor < $menorIntervaloEntreTodosProdutores) {
                $produtorDoMenorIntervalo = $nome;
                $previousWinDesteProdutor = $previousWin;
                $followingWinDesteProdutor = $followingWin;
                $menorIntervaloEntreTodosProdutores = $menorIntervaloParaEsteProdutor;
            }
            }
        }
        
        $arranjoComMaiorEMenorIntervalo['min'] = array(
            'producer' => $produtorDoMenorIntervalo,
            'interval' => ($followingWinDesteProdutor - $previousWinDesteProdutor),
            'previousWin' => $previousWinDesteProdutor,
            'followingWin' => $followingWinDesteProdutor
        );
        
        return $arranjoComMaiorEMenorIntervalo;
    }

}
