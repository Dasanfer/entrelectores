<?php

namespace AdminBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Command\SimpleImage;
use AppBundle\Entity\Book;
use AppBundle\Entity\Author;
use Gedmo\Sluggable\Util as Sluggable;
use AdminBundle\Command\PONIpar as PONIPar;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class BuildSearchStringsCommand extends ContainerAwareCommand
{
    public $authorRepo;
    public $bookRepo;

    protected function configure()
    {
        $this->setName('entrelectores:search:book_strings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupBooks($output);
    }

    private function setupBooks($output){
        $doctrine = $this->getContainer()->get('doctrine');
        $appSearch = $this->getContainer()->get('app.search');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle:Book','b');
        $books = $qb->getQuery()->iterate();
        $count = 0;
        $results = 100;
        foreach($books as $row){
            $book = $row[0];
            $searchString = $appSearch->sanitizeString($book->getTitle());
            $book->setSearchString($searchString);
            $count++;
            $em->persist($book);

            if($count % $results == 0){
                $em->flush();
                $em->clear();
                $doctrine->resetManager();
                $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
                $cacheDriver->deleteAll();
                $output->writeLn($count.' books memory:'.memory_get_usage(true));
            }
        }
        $em->flush();
    }
}
