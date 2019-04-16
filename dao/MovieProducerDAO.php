<?php

class MovieProducerDAO {
    
    private $memory_db;
    
    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }
    
    public function insertMovieProducer($id_movie, $id_producer) {

        $ultimoID = 0;
        
        date_default_timezone_set('UTC');

        try {

            $insert = "INSERT INTO movie_producer (id_movie, id_producer) VALUES (:id_movie, :id_producer)";
            $stmt = $this->memory_db->prepare($insert);
            $stmt->bindParam(':id_movie', $id_movie);
            $stmt->bindParam(':id_producer', $id_producer);

            $resultado = $stmt->execute();
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $ultimoID;
        
    }
}