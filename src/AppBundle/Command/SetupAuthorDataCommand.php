<?php
/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AppBundle\Entity\FollowEvent;
use AppBundle\Entity\User;

class SetupAuthorDataCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('entrelectores:authors:updateData');
    }
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(b)')->from('AppBundle\Entity\Author', 'b');

        $total = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select('b')->from('AppBundle\Entity\Author','b');

        $offset = 0;
        $results = 100;
        $authors = $qb->getQuery()->iterate();
        $count = 0;

        foreach($authors as $row){
            $author = $row[0];
            $author->setUpdated(new \DateTime());
            $em->persist($author);
            $count = $count +1;
            if($count % $results == 0){
                $em->flush();
                $em->clear();
                $doctrine->resetManager();
                $output->writeLn($count.' of '.$total.' '.memory_get_usage(true));
            }
        }

        $em->flush();
    }

}
