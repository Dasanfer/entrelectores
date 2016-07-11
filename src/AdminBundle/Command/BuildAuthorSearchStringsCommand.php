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

class BuildAuthorSearchStringsCommand extends ContainerAwareCommand
{
    public $authorRepo;
    public $bookRepo;

    protected function configure()
    {
        $this->setName('entrelectores:search:author_strings');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupAuthors($output);
    }

    private function setupAuthors($output){
        $doctrine = $this->getContainer()->get('doctrine');
        $appSearch = $this->getContainer()->get('app.search');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('b')->from('AppBundle:Author','b');
        $entries = $qb->getQuery()->iterate();
        $count = 0;
        $results = 100;
        foreach($entries as $row){
            $author = $row[0];
            $searchString = $appSearch->sanitizeString($author->getName());
            $author->setSearchString($searchString);
            $count++;
            $em->persist($author);

            if($count % $results == 0){
                $em->flush();
                $em->clear();
                $doctrine->resetManager();
                $cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
                $cacheDriver->deleteAll();
                $output->writeLn($count.' authors memory:'.memory_get_usage(true));
            }
        }
        $em->flush();
    }
}
