<?php

class SQLiteManager {

    private $memory_db;

    public function __construct() {
        
        try {
            // Create new database in memory
            $this->memory_db = new PDO('sqlite::memory:');
            // Set errormode to exceptions
            $this->memory_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            /**************************************
             * Create tables                      *
             **************************************/
            $this->memory_db->exec(
                    "CREATE TABLE movie (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        year INTEGER, 
                                        title TEXT
                      )"
            );
            
            $this->memory_db->exec(
                    "CREATE TABLE studio (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        studio_name TEXT
                      )"
            );
            
            $this->memory_db->exec(
                    "CREATE TABLE movie_studio (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        id_movie INTEGER,
                                        id_studio INTEGER,
                                        FOREIGN KEY (id_movie) REFERENCES movie(id),
                                        FOREIGN KEY (id_studio) REFERENCES studio(id)
                      )"
            );
            
            $this->memory_db->exec(
                    "CREATE TABLE producer (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        producer TEXT
                      )"
            );
            
            $this->memory_db->exec(
                    "CREATE TABLE movie_producer (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        id_movie INTEGER,
                                        id_producer INTEGER,
                                        FOREIGN KEY (id_movie) REFERENCES movie(id),
                                        FOREIGN KEY (id_producer) REFERENCES producer(id)
                      )"
            );
            
            $this->memory_db->exec(
                    "CREATE TABLE goldenraspberryaward (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        year INTEGER
                      )"
            );
            
            $this->memory_db->exec(
                    "CREATE TABLE winner (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT, 
                                        id_goldenraspberryaward INTEGER,
                                        id_movie INTEGER,
                                        FOREIGN KEY (id_goldenraspberryaward) REFERENCES goldenraspberryaward(id),
                                        FOREIGN KEY (id_movie) REFERENCES movie(id)
                      )"
            );
            
        } catch (PDOException $ex) {
            echo $ex;
        }
    }

    public function __destruct() {
        try {
            $this->memory_db->exec("DROP TABLE movie");
            
            $this->memory_db->exec("DROP TABLE goldenraspberryaward");

            $this->memory_db = null;
        } catch (PDOException $ex) {
            echo $ex;
        }
    }

    public function getMemoryDB() {
        return $this->memory_db;
    }
    
}
