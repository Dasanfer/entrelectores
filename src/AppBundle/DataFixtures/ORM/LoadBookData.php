<?php
// src/Acme/HelloBundle/DataFixtures/ORM/LoadUserData.php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use AppBundle\Entity\Book;
use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;

class LoadBookData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $genre1 = new Genre();
        $genre1->setName('genre1');
        $genre1->setInfo('genero 1');
        $genre1->setDescription('genero 1');
        $manager->persist($genre1);

        $author1 = new Author();
        $author1->setName('author1');
        $author1->setSlug('author1');
        $author1->setPopular(true);
        $author1->setOldSlug('OLLDDDDDDDDD');
        $manager->persist($author1);
        $this->addReference('author1', $author1);

        $author2 = new Author();
        $author2->setName('author2');
        $author2->setSlug('author2');
        $this->addReference('author2', $author2);
        $manager->persist($author2);

        $book = new Book();
        $book->setGenre($genre1);
        $book->setTitle('test1');
        $book->setPopular(true);
        $this->addReference('book1', $book);
        $book->setSlug('test1');
        $book->setOldSlug('test1OLDDDD');
        $book->setOldId('123123');
        $book->setAuthor($author1);
        $manager->persist($book);

        $book = new Book();
        $book->setGenre($genre1);
        $book->setTitle('test2');
        $book->setAuthor($author2);
        $this->addReference('book2', $book);
        $manager->persist($book);

        $book = new Book();
        $book->setGenre($genre1);
        $book->setTitle('test3');
        $book->setAuthor($author2);
        $this->addReference('book3', $book);
        $manager->persist($book);


        $book = new Book();
        $book->setGenre($genre1);
        $book->setTitle('promoted1');
        $book->setPromoted(true);
        $book->setAuthor($author1);
        $manager->persist($book);

        $book = new Book();
        $book->setGenre($genre1);
        $book->setTitle('promoted2');
        $book->setPromoted(true);
        $book->setAuthor($author2);
        $manager->persist($book);

        $manager->flush();

        //now we load a bunch of books and authors
        for($i = 0; $i < 500; $i++){
            $author = new Author();
            $author->setName('author'.$i*100);
            $manager->persist($author);

            $book = new Book();
            $book->setGenre($genre1);
            $book->setTitle('bunch_book'.$i*100);
            $book->setAuthor($author);
            $manager->persist($book);

        }
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
