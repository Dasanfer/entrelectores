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

class TimelineEventsPopulateCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('entrelectoers:users:populateevents')
            ->setDescription('Creates a good bunch of events')
            ->addArgument(
                'username',
                InputArgument::OPTIONAL,
                'Who do you want to follow things'
            );
    }
    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $userToSet = null;
        $username = $input->getArgument('username');

        if(!is_null($username))
            $userToSet =  $doctrine->getRepository('AppBundle\Entity\User')->findOneByUsername($username);

        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('u'))
            ->from('AppBundle\Entity\User', 'u');
        $query = $qb->getQuery();
        $usersCount = $query->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select($qb->expr()->count('u'))
            ->from('AppBundle\Entity\Book', 'u');
        $query = $qb->getQuery();
        $booksCount = $query->getSingleScalarResult();

        $output->writeLn('Users: '.$usersCount.' Books: '.$booksCount);

        $offset = rand(0,$usersCount-2000);
        $users = $doctrine->getRepository('AppBundle\Entity\User')->findBy(array(),null,2000,$offset);

        $offset = rand(0,$booksCount-500);
        $books = $doctrine->getRepository('AppBundle\Entity\Book')->findBy(array(),null,500,$offset);

        $count = 10;
        $output->writeLn('Begin loop');
        foreach($books as $book){
            foreach($users as $user){

                if($count < 20 && !is_null($userToSet)){
                    if(95 < rand(0,100)){
                        if(!$userToSet->getUsersFollowed()->contains($user))
                            $userToSet->addUsersFollowed($user);
                    } else {
                        if(!$userToSet->getBooksFollowed()->contains($book))
                            $userToSet->addBooksFollowed($book);
                    }
                }

                $event = new FollowEvent();
                $event->setCreatedBy($user);
                $event->setBook($book);

                $rand = rand(0,3);

                switch($rand){
                case 0:
                    $event->setType(FollowEvent::comment);
                    break;

                case 1:
                    $event->setType(FollowEvent::rating);
                    break;

                case 2:
                    $event->setType(FollowEvent::review);
                    break;

                case 3:
                    $event->setType(FollowEvent::comment);
                    break;

                case 4:
                    $event->setType(FollowEvent::follow);
                    break;
                }

                $count = $count + 1;
                $em->persist($event);
                if($count%1000 == 0 ){
                    $output->writeLn('Loop: '.$count);
                    $em->flush();
                }
            }
        }

        $em->flush();
    }
}
