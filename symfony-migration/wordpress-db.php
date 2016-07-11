<?php

class wordpressDb {

    public $connection;
    private $storedSql;
    public function __construct($server, $user, $pass, $db){
        $this->connection = new mysqli($server, $user, $pass, $db);
        if ($this->connection->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }
        $this->connection->set_charset("utf8");
    }

    public function getBookRatings($bookId){
        $sql = 'SELECT * from wp_rate_rate where idLibro =  '.$bookId.';';

        if(!$result = $this->connection->query($sql)){
            print_r('There was an error running the query [' . $this->connection->error . ']'."\n\n".$sql."\n\n");
        }

        for ($res = array(); $tmp = $result->fetch_assoc();) $res[] = $tmp;
        return $res;
    }

    public function getLists($offset, $number){
        $sql ='SELECT lb.id_lista as id_lista, lb.id_usuario as id_usuario, l.name as name FROM  wp_listalibros lb join wp_listas l on lb.id_lista = l.id  WHERE 1 GROUP BY lb.id_usuario, lb.id_lista LIMIT '.$offset.' , '.$number;

        if(!$result = $this->connection->query($sql)){
            print_r('There was an error running the query [' . $this->connection->error . ']'."\n\n".$sql."\n\n");
        }

        for ($res = array(); $tmp = $result->fetch_assoc();) $res[] = $tmp;

        return $res;
    }

    public function getListsEntries($offset, $number){
        $sql ='SELECT lb.id_lista as id_lista, lb.id_libro as id_libro, lb.id_usuario as id_usuario  FROM  wp_listalibros lb WHERE 1 LIMIT '.$offset.' , '.$number;

        if(!$result = $this->connection->query($sql)){
            print_r('There was an error running the query [' . $this->connection->error . ']'."\n\n".$sql."\n\n");
        }

        for ($res = array(); $tmp = $result->fetch_assoc();) $res[] = $tmp;

        return $res;
    }

    public function getFriendEntries($offset, $number){
        $sql ='SELECT f.initiator_user_id as user1, f.friend_user_id as user2 FROM wp_bp_friends f WHERE is_confirmed = 1 LIMIT '.$offset.' , '.$number;

        if(!$result = $this->connection->query($sql)){
            print_r('There was an error running the query [' . $this->connection->error . ']'."\n\n".$sql."\n\n");
        }

        for ($res = array(); $tmp = $result->fetch_assoc();) $res[] = $tmp;

        return $res;
    }

    public function close(){
        $this->connection->close();
    }

    private function escape($str){
        return mysqli_real_escape_string($this->connection,$str);
    }
}
