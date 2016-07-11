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

class SetupListSlugsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('entrelectores:lists:setupslugs');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('count(u)')->from('AppBundle\Entity\BookList', 'u');
        $total = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select('u')->from('AppBundle\Entity\BookList','u');

        $lists = $qb->getQuery()->iterate();
        $count = 0;
        $results = 100;
        $date = new \DateTime();

        foreach($lists as $row){
            $list = $row[0];
            $list->setUpdated($date);
            $list->setSlug($list->getName());
            $em->persist($list);
            $count = $count +1;
            if($count % $results == 0){
                $em->flush();
                $em->clear();
                $output->writeLn($count.' of '.$total);
            }

        }
        $em->flush();
    }
}
