<?php

class BookThread extends Thread {
    private $symfConn;
    private $wpConn;
    private $count;
    private $offset;
    public $result = false;
    public function __construct($symfConn,$wpdbConn,$count,$offset){
        $this->symfConn = $symfConn;
        $this->wpConn = $wpdbConn;
        $this->count = $count;
        $this->offset = $offset;

    }
    public function closeDbs(){
        $this->symfConn->close();
        $this->wpConn->close;
    }

    private function check_if_book_exists($book,$conn){
        return $conn->findBookBy(array('oldid' => $book->ID));
    }

    public function run() {
        require_once __DIR__.'/../../../../wp-load.php';
        $wp = new WP();
        $wp->main();

        try {
            $books = get_all_books($offset, $count);
            foreach($books as $book){
                if(!check_if_book_exists($book,$this->symfConn)){
                    $newId = store_book($book,$this->symfConn);
                    migrate_book_ratings($book,$newId, $this->symfConn,$this->wpConn);
                    migrate_resenas($book,$newId,$this->symfConn);
                }
            }
            $this->result = count($books) > 0;
        } catch(Exception $ex){
            $this->result = false;
            print_r($ex);
        }
    }
}
