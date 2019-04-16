<?php

class MovieStudioDAO {
    
    private $memory_db;
    
    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }
    
    public function insertMovieStudio($id_movie, $id_studio) {

        $ultimoID = 0;
        
        date_default_timezone_set('UTC');

        try {

            $insert = "INSERT INTO movie_studio (id_movie, id_studio) VALUES (:id_movie, :id_studio)";
            $stmt = $this->memory_db->prepare($insert);
            $stmt->bindParam(':id_movie', $id_movie);
            $stmt->bindParam(':id_studio', $id_studio);

            $resultado = $stmt->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $ultimoID;
        
    }
}