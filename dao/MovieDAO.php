<?php

class MovieDAO {
    
    private $memory_db;
    
    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }
    
    public function insertMovie($year, $title) {

        $ultimoID = 0;
        
        // Set default timezone
        date_default_timezone_set('UTC');

        try {

            $insert = "INSERT INTO movie (year, title) VALUES (:year, :title)";
            $stmt = $this->memory_db->prepare($insert);
            $stmt->bindParam(':title', addslashes($title));
            $stmt->bindParam(':year', $year);

            $resultado = $stmt->execute();
            
            $ultimoID = $this->findMovieByTitle($title);
            $ultimoID = $ultimoID['id'];
            
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $ultimoID;
        
    }
    
    function findMovieByTitle($title) {
        
        $title = addslashes($title);
        
        $result = $this->memory_db->query('SELECT * FROM movie WHERE title="' . $title . '"');
        
        $id = 0;
        $title = '';
        $year = 0;
        
        foreach ($result as $row) {
            $id = $row['id'];
            $title = $row['title'];
            $year = $row['year'];
        }
        
        return [
                'id' => $id,
                'title' => $title,
                'year' => $year
                ];
    }
    
    function deleteMovie($movie_title) {
        
        $movie_title = addslashes($movie_title);
        
        $query = 'SELECT '
                . 'm.id as m_id, m.title as m_title '
                . 'FROM movie m JOIN winner w '
                . 'ON m.id = w.id_movie JOIN goldenraspberryaward gra '
                . 'ON gra.id = w.id_goldenraspberryaward '
                . 'WHERE m_title = "' . $movie_title . '"';
        
        $result = $this->memory_db->query( $query );

        $temPremio = FALSE;
        $movie_id = 0;
        foreach ($result as $row) {
            $movie_id = $row['m_id'];
            $temPremio = TRUE;
            break;
        }
        
        if ($temPremio == FALSE) {
            $query = 'DELETE FROM movie WHERE movie.id = (:movie_id)';
            $stmt = $this->memory_db->prepare($query);
            $stmt->bindParam(':movie_id', $movie_id);
            $r = $stmt->execute();
        }
        
        return !$temPremio;
        
    }

    
    
}