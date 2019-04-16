<?php

class StudioDAO {

    private $memory_db;

    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }

    public function insertStudio($studio_name) {

        $ultimoID = 0;

        // Set default timezone
        date_default_timezone_set('UTC');

        try {

            $studio = $this->findStudioByName($studio_name);
            if (!isset($studio) || empty($studio['studio_name'])) {

                $insert = "INSERT INTO studio (studio_name) VALUES (:studio_name)";
                $stmt = $this->memory_db->prepare($insert);
                $stmt->bindParam(':studio_name', addslashes($studio_name));

                $resultado = $stmt->execute();

                $ultimoID = $this->findStudioByName($studio_name);
                $ultimoID = $ultimoID['id'];
            } else {
                $ultimoID = $studio['id'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $ultimoID;
    }

    function findStudioByName($studio_name) {

        $studio_name = addslashes($studio_name);

        $result = $this->memory_db->query('SELECT * FROM studio WHERE studio_name="' . $studio_name . '"');

        $id = 0;
        $studio_name = '';

        foreach ($result as $row) {
            $id = $row['id'];
            $studio_name = $row['studio_name'];
        }

        return [
            'id' => $id,
            'studio_name' => $studio_name
        ];
    }
    
    function findAllStudios() {

        $result = $this->memory_db->query(
                'SELECT * FROM studio'
                );
        
        $id = 0;
        $studio_name = '';

        $studios = array();
        
        foreach ($result as $row) {
            $id = $row['id'];
            $studio_name = $row['studio_name'];
            $studios[] = array(
                'id' => $id,
                'studio_name' => $studio_name
            );
        }

        return $studios;
    }
    
    function findAllStudiosOrdered() {
        
        $query = 'SELECT '
                . 'ms.id_movie as ms_id_movie, '
                . 'ms.id_studio as ms_id_studio, '
                . 'm.title as m_title, '
                . 's.studio_name as s_studio_name,'
                . 'w.id_goldenraspberryaward as w_id_goldenraspberryaward,'
                . 'gra.year '
                . 'FROM movie_studio ms JOIN '
                . 'movie m ON m.id = ms.id_movie JOIN '
                . 'studio s ON ms.id_studio = s.id JOIN '
                . 'winner w ON w.id_movie = m.id JOIN '
                . 'goldenraspberryaward gra ON gra.id = w.id_goldenraspberryaward ';
        
        $result = $this->memory_db->query( $query );
        
        $teste = array();
        foreach ($result as $r) {
            ++$teste[$r['s_studio_name']];
        }
        
        arsort($teste);

        return $teste;
    }

}
