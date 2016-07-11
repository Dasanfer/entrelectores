<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AdminBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Command\Sitemap;

class SearchDuplicatesCommand extends ContainerAwareCommand
{
    private $out;
    private $fileName;
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('entrelectores:admin:getduplicates');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('b')->from('AppBundle\Entity\Book','b');
        $books = $qb->getQuery()->iterate();

        foreach($books as $row){
            $book = $row[0];
            $similar = $this->getContainer()->get('mapado.simstring.book_reader')->find($book->getTitle(),0.95);
            foreach($similar as $similarBook){
                if($book->getId() != $similarBook->getValue()->getId())
                    $output->writeLn($book->getTitle().' '.$book->getId().' => '.$similarBook->getValue()->getTitle().' '.$similarBook->getValue()->getId());
            }
        }
    }
}
