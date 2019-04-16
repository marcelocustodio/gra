<?php

class GoldenRaspberryAwardDAO {
    
    private $memory_db;
    
    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }
    
    public function insertGoldenRaspberryAward($year) {

        $ultimoID = 0;

        // Set default timezone
        date_default_timezone_set('UTC');

        try {

            $gra = $this->findGRAByYear($year);
            
            if (!isset($gra) || empty($gra['year'])) {

                $insert = "INSERT INTO goldenraspberryaward (year) VALUES (:year)";
                $stmt = $this->memory_db->prepare($insert);
                $stmt->bindParam(':year', $year);

                $resultado = $stmt->execute();

                $ultimoID = $this->findGRAByYear($year);
                $ultimoID = $ultimoID['id'];
                
            } else {
                $ultimoID = $gra['id'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $ultimoID;
    }
    
    function findWinnersInYear($year) {
        
        $query = 'SELECT '
                . 'm.title as m_title,'
                . 'gra.year as gra_year '
                . 'FROM goldenraspberryaward gra JOIN winner w '
                . 'ON gra.id = w.id_goldenraspberryaward JOIN movie m '
                . 'ON m.id = w.id_movie '
                . 'WHERE gra.year = ' . $year;
        
        $result = $this->memory_db->query( $query );

        $r = array();
        foreach ($result as $row) {
            $r[] = $row['m_title'];
        }
        
        return $r;
    }
    
    function findGRAByYear($year) {

        $result = $this->memory_db->query(
                'SELECT * FROM goldenraspberryaward gra '
                . 'WHERE gra.year=' . $year
        );

        $id = 0;
        $year = 0;

        foreach ($result as $row) {
            $id = $row['id'];
            $year = $row['year'];
        }

        return [
            'id' => $id,
            'year' => $year
        ];
    }
    
    function findYearsWithMoreThanOneWinner() {

        $result = $this->memory_db->query(
                'SELECT gra.id as gra_id,'
                . 'gra.year as gra_year,'
                . 'w.id as w_id,'
                . 'w.id_goldenraspberryaward as w_id_goldenraspberryaward,'
                . 'w.id_movie as w_id_movie '
                . 'FROM goldenraspberryaward gra JOIN winner w '
                . 'ON w.id_goldenraspberryaward = gra.id ' );
        
        $years = array();

        foreach ($result as $row) {
            $years[] = $row['gra_year'];
        }
        
        $duplicated = array();
        $tamanho = count($years);
        for($i=0; $i<$tamanho; ++$i) {
            $achou_duplicado = FALSE;
            for($j=0; $j<$tamanho; ++$j) {
                if ($i==$j) continue;
                if ($years[$i] == $years[$j]){
                    $duplicated[] = $years[$i];
                    break;
                }
            }
        }
        
        $duplicated  = array_unique($duplicated);

        return $duplicated;
        
    }
}