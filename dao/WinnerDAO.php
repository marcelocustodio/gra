<?php

class WinnerDAO {

    private $memory_db;

    public function __construct($memory_db) {
        $this->memory_db = $memory_db;
    }

    public function insertWinner($id_movie, $id_goldenraspberryaward) {

        $ultimoID = 0;

        // Set default timezone
        date_default_timezone_set('UTC');

        try {

            $insert = "INSERT INTO winner(id_goldenraspberryaward, id_movie) "
                    . "VALUES (:id_goldenraspberryaward, :id_movie)";
            $stmt = $this->memory_db->prepare($insert);
            $stmt->bindParam(':id_goldenraspberryaward', $id_goldenraspberryaward);
            $stmt->bindParam(':id_movie', $id_movie);

            $resultado = $stmt->execute();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $ultimoID;
    }

    function findWinnerByYear($year) {

        $result = $this->memory_db->query(
                'SELECT * FROM winner JOIN goldenraspberryaward gra '
                . 'ON id_goldenraspberryaward = id_winner '
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

}
