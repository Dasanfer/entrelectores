<?php
namespace AppBundle\Services\Books;
use AppBundle\Entity\BookRating;
use AppBundle\Entity\ReviewRating;
use AppBundle\Services\Timeline\TimelineEvent;
use AppBundle\Entity\FollowEvent;

class SearchService
{
    private $doctrine;
    private $simstringBook;
    private $simstringAuthor;
    private static $replaceArray = array(
        'el','la','los','las','un', 'uno','una','unos',
        ',', '.', ';', ':','?','¿','!'
    );

    public function __construct($doctrine, $simstringBook, $simstringAuthor){
        $this->doctrine = $doctrine;
        $this->simstringBook = $simstringBook;
        $this->simstringAuthor = $simstringAuthor;
    }

    public function sanitizeString($str){
        $newStr = strtolower($str);
        str_replace(self::$replaceArray,' ',$str);
        $str = preg_replace('/\s+/', ' ',$str);
        return $str;
    }

    public function getBookSearchQuery($term){
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('b')->from('AppBundle:Book', 'b')
            ->where($qb->expr()->like('b.searchString',':term'));
        $qb->setParameters(array('term' => '%'.$term.'%'));

        return $qb->getQuery();
    }

    public function getAuthorSearchQuery($term){
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('a')->from('AppBundle:Author', 'a')
            ->where($qb->expr()->like('a.searchString',':term'));
        $qb->setParameters(array('term' => '%'.$term.'%'));

        return $qb->getQuery();
    }

    public function getSearchArray($term){
        return $this->mergeResult($this->searchBooks($term),$this->searchAuthors($term));
    }

    public function searchAuthors($term){
        $delta = 0.7;
        $sanTerm = $this->sanitizeString($term);
        $authors = array();
        while(count($authors) < 20 && $delta > 5){
            $delta = $delta - 0.05;
            $authors = $this->simstringAuthor->find($sanTerm,$delta);
        }

        foreach($authors as $key => $author){
            $authors[$key] = $author->getValue();
        }

        if(count($authors) < 20)
            $authors = array_merge($authors,$this->getAuthorSearchQuery($term)->setMaxResults(100)->getResult());

        return $authors;
    }

    public function searchBooks($term){
        $sanTerm = $this->sanitizeString($term);
        $delta = 0.9;
        $books = array();

        while(count($books) < 1 && $delta > 0.5){
            $delta = $delta - 0.05;
            $books = $this->simstringBook->find($sanTerm,$delta);
        }

        foreach($books as $key => $book){
            $books[$key] = $book->getValue();
        }

        if(count($books) < 20)
            $books = array_merge($books,$this->getBookSearchQuery($term)->setMaxResults(100)->getResult());

        return $books;
    }

    private function mergeResult($arr1,$arr2){
        $newArray = array();
        $mi = new \MultipleIterator(\MultipleIterator::MIT_NEED_ANY);
        $mi->attachIterator(new \ArrayIterator($arr1));
        $mi->attachIterator(new \ArrayIterator($arr2));
        foreach($mi as $details) {
            $newArray = array_merge(
                $newArray,
                array_filter($details)
            );
        }

        return $newArray;
    }
}
