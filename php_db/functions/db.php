<?php

function getConnection(){
    static $db = null;
    if (is_null($db))
        try {
            $db = new PDO("pgsql:host=localhost;dbname=Blog_HW;port=5433", 'postgres', 'DR11052001');
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
        }
        catch(PDOException $e) {
            echo $e->getMessage();
            die();
        }
    return $db;




    //{
       // $db = new PDO ("pgsql:host=localhost;dbname=Blog_HW;port=5433", 'postgres', 'DR11052001',
          //  [
           //     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
           // ]);
   // }
   // return $db;
}

