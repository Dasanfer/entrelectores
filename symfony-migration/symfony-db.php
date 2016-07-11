<?php

class symfonyDb {

    public $connection;
    private $storedSql = array();

    private $userIdCache = array();
    private $bookIdCache = array();
    private $authorIdCache = array();
    private $genreIdCache = array();
    private $reviewIdCache = array();
    private $ratingIdCache = array();
    private $listIdCache = array();

    private $storeUserCache = array();
    private $storeAuthorCache = array();
    private $storeBookCache = array();
    private $storeReviewCache = array();
    private $storeListCache = array();
    private $storeRelationCache = array();
    private $storeListEntryCache = array();
    private $storeFollowerEntriesCache = array();

    public function __construct($server, $user, $pass, $db){
        $this->connection = new mysqli($server, $user, $pass, $db);
        if ($this->connection->connect_errno) {
            printf("Connect failed: %s\n", $mysqli->connect_error);
            exit();
        }

        $this->connection->set_charset("utf8");
    }

    public function loadUserCache(){
        $this->userIdCache = array();
        $result = $this->findInBy('fos_user',array());
        while ($row = $result->fetch_assoc()) {
            $this->userIdCache[$row['oldId']] = $row['id'];
        }
        print_r('User cache: '.count($this->userIdCache)."\n");
    }

    public function loadBookCache(){
        $this->bookIdCache = array();
        $result = $this->findInBy('Book',array());
        while ($row = $result->fetch_assoc()) {
            $this->bookIdCache[$row['oldId']] = $row['id'];
        }
        print_r('Book cache: '.count($this->bookIdCache)."\n");
    }

    public function loadAuthorCache(){
        $this->authorIdCache = array();
        $result = $this->findInBy('Author',array());
        while ($row = $result->fetch_assoc()) {
            $this->authorIdCache[$row['oldId']] = $row['id'];
        }
    }

    public function loadGenreCache(){
        $this->genreIdCache = array();
        $result = $this->findInBy('Genre',array());
        while ($row = $result->fetch_assoc()) {
            $this->genreIdCache[$row['oldId']] = $row['id'];
        }
    }

    public function loadReviewCache(){
        $this->reviewIdCache = array();
        $result = $this->findInBy('Review',array());
        while ($row = $result->fetch_assoc()) {
            $this->reviewIdCache[$row['oldId']] = $row['id'];
        }
    }

    public function loadRatingCache(){
        $this->ratingIdCache = array();
        $result = $this->findInBy('BookRating',array());
        while ($row = $result->fetch_assoc()) {
            $this->ratingIdCache[$row['book_id'].$row['user_id']] = $row['id'];
        }
    }

    public function loadListCache(){
        $this->listIdCache = array();
        $result = $this->findInBy('BookList',array());
        while ($row = $result->fetch_assoc()) {
            $this->listIdCache[$row['user_id'].'-'.$row['oldId']] = $row['id'];
        }
        print_r('List cache: '.count($this->listIdCache)."\n");
    }

    public function loadListEntryCache(){
        $this->listEntryIdCache = array();
        $result = $this->findInBy('lists_books',array());
        while ($row = $result->fetch_assoc()) {
            $this->listEntryIdCache[$row['book_id'].'-'.$row['booklist_id']] = $row['id'];
        }

        print_r('List entry cache: '.count($this->listEntryIdCache)."\n");
    }

    public function flushUsers(){
        $result = $this->storeArraySet('fos_user',$this->storeUserCache);
        $this->storeUserCache = array();
        $this->loadUserCache();
    }

    public function flushAuthors(){
        $result = $this->storeArraySet('Author',$this->storeAuthorCache);
        $this->storeAuthorCache = array();
        $this->loadAuthorCache();
    }

    public function flushBooks(){
        $result = $this->storeArraySet('Book',$this->storeBookCache);
        $this->storeBookCache = array();
        $this->loadBookCache();
    }

    public function flushReviews(){
        $result = $this->storeArraySet('Review',$this->storeReviewCache);
        $this->storeReviewCache = array();
    }

    public function flushRatings(){
        $result = $this->storeArraySet('BookRating',$this->storeRatingCache);
        $this->storeRatingCache = array();
    }

    public function flushLists(){
        $result = $this->storeArraySet('BookList',$this->storeListCache);
        $this->storeListCache = array();
    }

    public function flushRelations(){
        $result = $this->storeArraySet('BookUserRelation',$this->storeRelationCache);
        $this->storeRelationCache = array();
    }

    public function flushListsEntries(){
        $result = $this->storeArraySet('lists_books',$this->storeListEntryCache);
        $this->storeListCache = array();
    }

    public function flushFollowersEntries(){
        $result = $this->storeArraySet('user_follower',$this->storeFollowerEntriesCache);
        $this->storeFollowerEntriesCache = array();
    }

    public function storeUser($terms){
        $terms['enabled'] = 1;
        $terms['salt'] = sha1(uniqid());
        $terms['locked'] = 0;
        $terms['expired'] = 0;
        $terms['roles'] = serialize(array('ROLE_USER'));
        $terms['credentials_expired'] = 0;

        $this->storeUserCache[] = $terms;

        if(count($this->storeUserCache) > 1000){
            $result = $this->storeArraySet('fos_user',$this->storeUserCache);
            $this->storeUserCache = array();
            $this->loadUserCache();
        }
    }

    public function storeBook($terms){
        $this->storeBookCache[] = $terms;
        if(count($this->storeBookCache) > 200){
            $result = $this->storeArraySet('Book',$this->storeBookCache);
            $this->storeBookCache = array();
            //$this->loadBookCache();
        }
    }

    public function storeGenre($terms){
        return $this->storeSet('Genre', $terms);
    }

    public function storeAuthor($terms){
        $this->storeAuthorCache[] = $terms;
        if(count($this->storeAuthorCache) > 2000){
            $result = $this->storeArraySet('Author',$this->storeAuthorCache);
            $this->storeAuthorCache = array();
            $this->loadAuthorCache();
        }
    }

    public function storeResena($terms){
        $this->storeReviewCache[] = $terms;
        if(count($this->storeReviewCache) > 1000){
            $result = $this->storeArraySet('Review',$this->storeReviewCache);
            $this->storeReviewCache = array();
        }
    }

    public function storeRating($terms){
        $this->storeRatingCache[] = $terms;
        if(count($this->storeRatingCache) > 1000){
            $result = $this->storeArraySet('BookRating',$this->storeRatingCache);
            $this->storeRatingCache = array();
        }
    }

    public function storeList($term){
        $this->storeListCache[] = $term;
        if(count($this->storeListCache) > 1000){
            $result = $this->storeArraySet('BookList',$this->storeListCache);
            $this->storeListCache = array();
        }
    }

    public function storeRelation($term){
        $this->storeRelationCache[] = $term;
        if(count($this->storeRelationCache) > 1000){
            $result = $this->storeArraySet('BookUserRelation',$this->storeRelationCache);
            $this->storeRelationCache = array();
        }
    }

    public function storeListEntry($term){
        $this->storeListEntryCache[] = $term;
        if(count($this->storeListEntryCache) > 500){
            $result = $this->storeArraySet('lists_books',$this->storeListEntryCache);
            $this->storeListEntryCache = array();
            //$this->loadListEntryCache(); // we reload because theres a lot of duplicates
        }
    }

    public function storeFollowerEntry($term){
        $this->storeFollowerEntriesCache[] = $term;
        if(count($this->storeFollowerEntriesCache) > 1000){
            $result = $this->storeArraySet('user_follower',$this->storeFollowerEntriesCache);
            $this->storeFollowerEntriesCache = array();
            //$this->loadListEntryCache(); // we reload because theres a lot of duplicates
        }
    }

    private function storeArraySet($table,$arraySet,$oneAtTime = false){

        if(count($arraySet) == 0)
            return;

        $terms = $arraySet[0];
        $totalSql = ' insert ignore into `'.$table.'` (';
        foreach($terms as $key => $value){
            $totalSql .= '`'.$this->escape($key).'`,';
        }

        $totalSql = trim($totalSql,',').') VALUES ';

        foreach($arraySet as $set){
            $totalSql .= " (";
            foreach($set as $key => $value){
                if(is_array($value)){
                    print_r('WRONG VALUE '.$key);
                    print_r($value);
                } else if(is_null($value)){
                    $totalSql .= 'null,';
                } else {
                    $totalSql .= is_numeric($value) ? $value.',' : '\''.$this->escape(trim($value)).'\',';
                }
            }
            $totalSql = trim($totalSql,',').'),';
        }
        $totalSql = trim($totalSql,',').';';

        if(!$result = $this->connection->query($totalSql)){
            error_log('There was an error running big query [' . $this->connection->error . ']'."\n\n");
            foreach($arraySet as $set){
                $this->storeSet($table,$set,false);
            }
        }
    }

    private function storeSet($table,$terms,$getSql = false){
        $sql = ' insert into `'.$table.'` (';
        foreach($terms as $key => $value){
            $sql .= '`'.$this->escape($key).'`,';
        }
        $sql = trim($sql,',').') VALUES (';

        foreach($terms as $key => $value){
            if(is_array($value)){
                    print_r('WRONG VALUE '.$key);
                    print_r($value);
            } else if(is_null($value)){
                $sql .= 'null,';
            } else {
                 $sql .= is_numeric($value) ? $value.',' : '\''.$this->escape(trim($value)).'\',';
             }
        }

        $sql = trim($sql,',').');';
        if($getSql){
            return $sql;
        }
        if(!$result = $this->connection->query($sql)){
            error_log('There was an error running the query [' . $this->connection->error . ']'."\n\n".$sql."\n\n");
        }

        return $this->connection->insert_id;

    }

    public function findRating($bookId,$userId){
        return array_key_exists($bookId.$userId,$this->ratingIdCache);
    }

    public function findUserBy($terms){
        return array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->userIdCache);
    }

    public function findAuthorBy($terms){
        return array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->authorIdCache);
    }

    public function findListBy($oldSlug){
        return array_key_exists($oldSlug,$this->listIdCache);
    }

    public function findListEntry($bookId,$listId){
        return array_key_exists($bookId.'-'.$listId,$this->listEntryIdCache);
    }

    public function getNewUserId($terms){
        if(array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->userIdCache))
            return $this->userIdCache[$terms['oldId']];

        return null;
        //return $this->getNewId('fos_user',$terms);
    }

    public function getNewAuthorId($terms){
        if(array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->authorIdCache))
            return $this->authorIdCache[$terms['oldId']];

        return $this->getNewId('Author',$terms);
    }

    public function getNewBookId($terms){
        if(array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->bookIdCache))
            return $this->bookIdCache[$terms['oldId']];

        return null;
        return $this->getNewId('Book',$terms);
    }

    public function getNewGenreId($terms){
        return $this->genreIdCache[$terms['oldId']];
    }

    public function getNewListId($userId, $listId){
        if(array_key_exists($userId.'-'.$listId,$this->listIdCache))
            return $this->listIdCache[$userId.'-'.$listId];
        else
            return false;
    }

    public function listExists($listId,$userId){
        return array_key_exists($listId.'-'.$userId,$this->listIdCache);
    }

    public function listEntryExists($bookId,$listId){
        return array_key_exists($bookId.'-'.$listId,$this->listEntryIdCache);
    }

    private function getNewId($table,$terms){
        $result = $this->findInBy($table,$terms);
        if($result->num_rows > 0 ){
            $row = $result->fetch_assoc();
            return $row['id'];
        }
        return null;
    }

    public function findBookBy($terms){
        return array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->bookIdCache);
    }

    public function findGenreBy($terms){
        return array_key_exists('oldId',$terms) && array_key_exists($terms['oldId'],$this->genreIdCache);
    }

    public function findReview($oldId){
        return array_key_exists($oldId,$this->reviewIdCache);
    }


    private function findInBy($table, $terms){
        $sql = 'SELECT * FROM `'.$table.'`';
        if(count($terms) > 0){
            $sql .=' WHERE ';
            foreach($terms as $key => $value){
                $sql .= ' '.$key.'='.(is_numeric($value) ? $value : '\''.$this->escape($value).'\' ');
                $sql .= ' and ';
            }
            $sql = substr($sql,0,-5);
        }
        $sql .= ';';

        if(!$result = $this->connection->query($sql)){
            error_log('There was an error running the query [' . $this->connection->error . ']'."\n\n".$sql."\n\n");
        }

        return $result;
    }

    public function close(){
        $this->connection->close();
    }

    private function escape($str){
        if(is_null($str))
            return null;

        return mysqli_real_escape_string($this->connection,$str);
    }
}
