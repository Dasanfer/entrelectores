<?php
require_once __DIR__.'/../../../../wp-load.php';
require_once __DIR__.'/../../../../wp-admin/includes/image.php';
require_once 'symfony-db.php';
//require_once 'book_thread.php';
require_once 'wordpress-db.php';


function get_all_users($offset, $number){
    return get_users(array('number' => $number,'offset' => $offset));
}

function store_user($user,$conn){
    $terms = array(
        'oldid' => $user->ID,
        'username' => $user->user_login,
        'username_canonical' => $user->user_login,
        'email' => $user->user_email,
        'email_canonical' => $user->user_email,
        'password' => $user->user_pass,
        'facebookId' => get_user_meta($user->ID, 'facebook_uid', true),
        'created' => $user->user_registered
    );
    //buddypressdata
    $terms['country'] = xprofile_get_field_data('País', $user->ID);
    $terms['city'] = xprofile_get_field_data('Ciudad', $user->ID);
    $terms['gender'] = xprofile_get_field_data('Sexo', $user->ID) == 'Hombre' ? 'M' : 'F';
    $terms['birthday'] = xprofile_get_field_data('Fecha de nacimiento', $user->ID);
    $terms['cita'] = xprofile_get_field_data('Cita', $user->ID);
    $terms['name'] = xprofile_get_field_data('Nombre completo', $user->ID);
    $conn->storeUser($terms);
}

function check_if_user_exists($user,$conn){
    return $conn->findUserBy(array('oldid' => $user->ID));
}

function get_all_books($offset, $number){
    return get_posts(array('offset' => $offset,'posts_per_page' => $number, 'post_type' => 'libro', 'order' => 'ASC'));
}

function migrate_users($conn){
    $offset = 0;
    $batchSize = 1000;
    $users = get_all_users($offset*$batchSize, $batchSize);
    while(count($users) > 0){
        print_r('Users: '.($offset*$batchSize)." \n");
        foreach($users as $user){
            if(!check_if_user_exists($user,$conn)){
                store_user($user,$conn);
            }
        }
        $offset = $offset +1;
        $users = get_all_users($offset*$batchSize, $batchSize);
    }
    $conn->flushUsers();
}

function migrateFriends($conn,$wpConn){
    $offset = 0;
    $batch = 1000;
    $friends = $wpConn->getFriendEntries($offset*$batch,$batch);
    while(count($friends) > 0){
        foreach($friends as $friend){
            $user1Id = $conn->getNewUserId(array('oldId' => $friend['user1']));
            $user2Id = $conn->getNewUserId(array('oldId' => $friend['user2']));
            if(!is_null($user1Id) && !is_null($user2Id)){
                $conn->storeFollowerEntry(array('user_source' => $user1Id, 'user_target' => $user2Id));
                $conn->storeFollowerEntry(array('user_source' => $user2Id, 'user_target' => $user1Id));
            }
        }
        print_r('Friends '.$offset*$batch."\n");
        $offset = $offset + 1;
        $friends = $wpConn->getFriendEntries($offset*$batch,$batch);
    }
    $conn->flushFollowersEntries();
}

function store_book($book,$conn){
    $portadas = wp_get_attachment_image_src(get_post_meta($book->ID, 'portada', true));
    $portada = substr($portadas[0],strpos($portadas[0],'wp-content')+strlen('wp-content'));
    $terms = array(
        'oldId' => $book->ID,
        'title' => $book->post_title,
        'originalTitle' => get_post_meta($book->ID, 'titulo_original', true),
        'isbn' => get_post_meta($book->ID, 'isbn', true),
        'sinopsis' => $book->post_content,
        'imageDir' => $portada,
        'slug' => $book->post_name,
        'oldSlug' => $book->post_name,
        'year' => get_post_meta($book->ID, 'ano_publicacion', true)
    );

    $authors = wp_get_object_terms($book->ID, 'autor_libro');
    $author_id = null;

    foreach($authors as $author){
        if($author->count > 0){
            $newId = $conn->getNewAuthorId(array('oldId' => $author->term_id));
            if(!is_null($newId) && trim($newId) != ''){
                $author_id = $newId;
                break;
            }
        }
    }

    $terms['author_id'] = $author_id;

    /*if(count($authors) > 0 ){
        $author_id = $conn->getNewAuthorId(array('oldId' => $authors[0]->term_id));
        if($author_id == '' || is_null($author_id)){
            print_r('Author no encontrado ');
            print_r($authors);
        }
    } else {
        $terms['author_id'] = null;
    }*/

    $currentGenre = wp_get_post_terms($book->ID,'generos');
    if(count($currentGenre) > 0){
        $newGenreId = $conn->getNewGenreId(array('oldId' => $currentGenre[0]->term_id));
        $terms['genre_id'] = $newGenreId;
    } else {
        $terms['genre_id'] = null;
    }

    $conn->storebook($terms);
}
function migrate_book_ratings($book,$newId,$conn,$wpConn){
    $ratings = $wpConn->getBookRatings($book->ID);
    store_ratings($ratings,$conn,$newId);
}

function store_ratings($ratings,$conn,$newId){
    foreach($ratings as $rating){
        $newUserId = $conn->getNewUserId(array('oldId' => $rating['idUsuario']));
        if(!is_null($newUserId) && !$conn->findRating($newId,$newUserId))
            $conn->storeRating(array('book_id' => $newId,'user_id' => $newUserId, 'value' => $rating['rate'],'created' => $rating['fechaVoto']));
    }
}

function check_if_book_exists($book,$conn){
    return $conn->findBookBy(array('oldId' => $book->ID));
}

function migrate_resenas($book,$newId,$conn){
    $resenas = get_posts(array('posts_per_page' => $number, 'post_type' => 'resena','post_parent' => $book->ID));
    foreach($resenas as $resena){
        if(!$conn->findReview($resena->ID)){
            $newAuthorId = $conn->getNewUserId(array('oldId' => $resena->post_author));
            if(is_null($newAuthorId) || $newAuthorId == ''){
                print_r('NO AUTOR |'.$newAuthorId.'| '.$resena->post_author."\n");
            } else {
                $metas = get_post_meta($resena->ID);
                $spoiler = $metas['spoiler'];

                if(!is_null($spoiler) && is_array($spoiler))
                    $spoiler = $spoiler[0];
                else
                    $spoiler = false;

                $conn->storeResena(array(
                    'user_id' => $newAuthorId,
                    'book_id' => $newId,
                    'title' => is_null($resena->post_title) ? '':$resena->post_title,
                    'text' => $resena->post_content,
                    'created' => $resena->post_date,
                    'oldId' => $resena->ID,
                    'spoiler' => $spoiler
                ));
            }
        }
    }
}

function migrate_books($conn, $wpConn){
    $offset = 0;
    $batchSize = 1000;
    $books = get_all_books($offset*$batchSize, $batchSize);
    while(count($books) > 0){
        print_r(($offset*$batchSize)." books \n");
        foreach($books as $book){
            if(!check_if_book_exists($book,$conn)){
                store_book($book,$conn);
            }
        }
        $offset = $offset +1;
        $books = get_all_books($offset*$batchSize, $batchSize);
    }

    $conn->flushBooks();
}

function migrate_all_ratings_resenas($conn, $wpConn){
    $offset = 0;
    $batchSize = 200;
    $books = get_all_books($offset*$batchSize, $batchSize);
    while(count($books) > 0){
        print_r(($offset*$batchSize)." books ratings \n");
        foreach($books as $book){
            $newId = $conn->getNewBookId(array('oldId' => $book->ID));
            migrate_book_ratings($book,$newId, $conn,$wpConn);
            //migrate_resenas($book,$newId,$conn);
        }
        $offset = $offset +1;
        $books = get_all_books($offset*$batchSize, $batchSize);
    }

    $conn->flushReviews();
    $conn->flushRatings();
}

function import_genres($conn){
    $genres = get_terms(array('generos'));
    foreach($genres as $genre){
        if(!$conn->findGenreBy(array('oldId' => $genre->term_id)))
            $newId = $conn->storeGenre(
                array(
                    'oldId' => $genre->term_id,
                    'slug' => $genre->slug,
                    'name' => $genre->name,
                    'oldSlug' => $genre->slug
                )
            );
    }
}

function get_author_info($authorId){
    $posts = get_posts(
        array('posts_per_page' => 1, 'post_type' => 'autor'),
        array('tax_query' => array('taxonomy' => 'autor','field' => 'term_id','terms'=> $authorId))
    );

    if(count($posts) > 0){
        return $posts[0]->post_content;
    }
    else
        return null;

}

function import_authors($conn){
    $offset = 0;
    $batchSize = 1000;
    $authors = get_terms(array('autor_libro'),array('number' => $batchSize, 'offset' => $offset*$batchSize));

    while(count($authors) > 0){
        print_r(($offset*$batchSize)." ");
        foreach($authors as $author){
            if(!$conn->findAuthorBy(array('oldId' => $author->term_id))){
                $info = $author->description;
                if(is_null($info) || $info == ''){
                    $info = get_author_info($author->term_id);
                }

                $newId = $conn->storeAuthor(
                    array(
                        'oldId' => $author->term_id,
                        'slug' => $author->slug,
                        'name' => $author->name,
                        'oldSlug' => $author->slug,
                        'info' => $author->description
                    )
                );
            }
        }
        $offset = $offset +1;
        $authors = get_terms(array('autor_libro'),array('number' => $batchSize, 'offset' => $offset*$batchSize));
    }
    $conn->flushAuthors();
}

function migrateLists($conn,$wpConn){
    $offset = 0;
    $batch = 1000;
    $lists = $wpConn->getLists($offset*$batch,$batch);
    while(count($lists) > 0){
        foreach($lists as $list){
            if($list['id_lista'] > 2){
                $oldUserId = $list['id_usuario'];
                $newUserId = $conn->getNewUserId(array('oldId' => $oldUserId));
                if(!is_null($newUserId)
                    && !$conn->findListBy($newUserId.'-'.$list['id_lista'])){

                    $conn->storeList(array(
                        'name' => $list['name'],
                        'user_id' => $newUserId,
                        'oldId' => $list['id_lista'],
                        'slug' => $newUserId.'-'.$list['id_lista'],
                        'publicFlag' => 0,
                        'globalFollow' => 0,
                        'created' => "0000-00-00 00:00:00",
                        'updated' => "0000-00-00 00:00:00"
                        )
                    );
                }
            }
        }

        $offset = $offset + 1;
        print_r('lists '.$offset*$batch."\n");
        $lists = $wpConn->getLists($offset*$batch,$batch);
    }

    $conn->flushLists();
}

function migrateListsEntries($conn,$wpConn){
    $offset = 0;
    $batch = 5000;
    $lists = $wpConn->getListsEntries($offset*$batch,$batch);
    while(count($lists) > 0){
        foreach($lists as $list){

            $oldUserId = $list['id_usuario'];
            $oldBookId = $list['id_libro'];
            $oldId = $list['id_lista'];

            $newUserId = $conn->getNewUserId(array('oldId' => $oldUserId));
            $newBookId = $conn->getNewBookId(array('oldId' => $oldBookId));
            if(!is_null($newUserId) && !is_null($newBookId)){
                if($oldId == 1){
                    $conn->storeRelation(
                        array(
                            'user_id' => $newUserId,
                            'book_id' => $newBookId,
                            'beginRead' => "0000-00-00 00:00:00",
                            'endRead' => "0000-00-00 00:00:00",
                            'want' => null,
                            'created' => "0000-00-00 00:00:00",
                            'updated' => "0000-00-00 00:00:00"
                        )
                    );
                } else if($oldId == 2){
                    $conn->storeRelation(
                        array(
                            'user_id' => $newUserId,
                            'book_id' => $newBookId,
                            'beginRead' => null,
                            'endRead' => null,
                            'want' => "0000-00-00 00:00:00",
                            'created' => "0000-00-00 00:00:00",
                            'updated' => "0000-00-00 00:00:00"
                        )
                    );
                } else {
                    $newListId = $conn->getNewListId($newUserId, $oldId);
                    if($newListId && !$conn->findListEntry($newBookId,$newListId)){
                        $conn->storeListEntry(array(
                            'booklist_id' => $newListId,
                            'book_id' => $newBookId,
                            )
                        );
                    }
                }
            }
        }
        $offset = $offset + 1;
        print_r('list entries '.$offset*$batch."\n");
        $lists = $wpConn->getListsEntries($offset*$batch,$batch);
    }

    $conn->flushRelations();
    $conn->flushListsEntries();
}

if(php_sapi_name() == 'cli' || true){
    print_r('Conectando a BBDD'."\n");
    $conn = new symfonyDb('127.0.0.1','root','*963./8520', 'entelectores-sym-test');
    $wpConn = new wordpressDb('127.0.0.1','root','*963./8520', 'entrelectores-wp');

   /* $conn->loadUserCache();
    print_r('Migrando usuarios --------------------'."\n");
    migrate_users($conn);

    $conn->loadUserCache();
    print_r('Migrando followers ---------------------'."\n");
    migrateFriends($conn,$wpConn);

    print_r('Migrando generos ---------------------'."\n");
    $conn->loadGenreCache();
    import_genres($conn);
    print_r('Migrando autores ---------------------'."\n");
    $conn->loadAuthorCache();
    import_authors($conn);

    print_r('Cache de libros ----------------------'."\n");
    $conn->loadAuthorCache();
    $conn->loadBookCache();
    $conn->loadGenreCache();
    print_r('Migrando libros ----------------------'."\n");
    migrate_books($conn, $wpConn);

    print_r('Cache de votos y reseñas ----------------------'."\n");
    $conn->loadBookCache();
    $conn->loadUserCache();
    $conn->loadRatingCache();
    $conn->loadReviewCache();
    print_r('Migrando votos y reseñas ----------------------'."\n");
    migrate_all_ratings_resenas($conn,$wpConn);
    */
    $conn->loadBookCache();
    $conn->loadUserCache();
    $conn->loadListCache();
    print_r('Cache loaded: migrating lists -----------------'."\n");
    migrateLists($conn,$wpConn);

    $conn->loadBookCache();
    $conn->loadUserCache();
    $conn->loadListCache();
    $conn->loadListEntryCache();
    print_r('Cache loaded: migrating list entries------------------'."\n");
    migrateListsEntries($conn,$wpConn);


    $conn->close();
    $wpConn->close();
}
