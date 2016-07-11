<?php
namespace AppBundle\Services\Message;

class MessageService extends \Twig_Extension {
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            'messageRights' => new \Twig_Function_Method($this, 'checkMessageRights')
        );
    }

    public function checkMessageRights($user1,$user2){
        if($user1->getFollowers()->contains($user2) && $user2->getFollowers()->contains($user1))
            return true;
        else if($user1->getGlobalFollow() || $user2->getGlobalFollow())
            return true;
        else
            return false;
    }

    public function getUserUnreadedMessages($user){
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT m FROM AppBundle\Entity\Message m
            where (m.from = :user1 OR m.to = :user1)
            and m.readed IS NULL
            and m.created =
                (
                    SELECT  MAX(n.created) time
                    FROM    AppBundle\Entity\Message n
                    WHERE   m.uniqueid = n.uniqueid
                )
            group by m.uniqueid
            order by m.created DESC
        ');

        $query->setParameters([
            'user1' => $user
        ]);

        return $query->getResult();
    }

    public function getUserReadedMessages($user){
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT m FROM AppBundle\Entity\Message m
            where (m.from = :user1 OR m.to = :user1)
            and m.readed IS NOT NULL
            and m.created =
                (
                    SELECT  MAX(n.created) time
                    FROM    AppBundle\Entity\Message n
                    WHERE   m.uniqueid = n.uniqueid
                )
            group by m.uniqueid
            order by m.readed DESC
        ');

        $query->setParameters([
            'user1' => $user
        ]);

        return $query->getResult();
    }

    public function getMessagesBetweenAndSetReaded($me, $friend, $offset, $limit){

        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT m FROM AppBundle\Entity\Message m
            where (m.from = :user1 AND m.to = :user2) OR (m.from = :user2 AND m.to = :user1)
            order by m.created DESC
        ');

        $query->setParameters(array(
            'user1'=> $me,
            'user2'=> $friend
        ));

        $query->setFirstResult( $offset )
                ->setMaxResults( $limit );

        $messages = $query->getResult();

        foreach($messages as $message)
        {
	  if (!$message->getReaded() && $message->getTo()==$me)
            {
                $message->setReaded(new \DateTime());
                $em->persist($message);
            }
        }

        $em->flush();

        return $messages;
    }

    public function getMutualFollowers($user){
        $follows = $user->getUsersFollowed();
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u');
        $qb->from('AppBundle:User','u');
        $qb->join('u.usersFollowed','f');
        $qb->where($qb->expr()->eq('f','?1'));
        $qb->andWhere($qb->expr()->in('u','?2'));

        $qb->setParameters(array(
            1 => $user,
            2 => $follows->toArray()
        ));

        $users = $query = $qb->getQuery()->getResult();

        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('u');
        $qb->from('AppBundle:User','u');
        $qb->where($qb->expr()->eq('u.globalFollow',true));

        return array_merge($users,$qb->getQuery()->getResult());
    }

    /* TWIG */
    public function getName()
    {
      return 'message_extension';
    }

    public function getFilters()
    {
      return array(
		   new \Twig_SimpleFilter('unreadedMessages', array($this, 'unreadedMessages')),
		   );
    }

    public function unreadedMessages($user)
    {
        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT m FROM AppBundle\Entity\Message m
            where m.to = :user1
            and m.readed IS NULL
            and m.created =
                (
                    SELECT  MAX(n.created) time
                    FROM    AppBundle\Entity\Message n
                    WHERE   m.uniqueid = n.uniqueid
                )
            group by m.uniqueid
            order by m.created DESC
        ');

        $query->setParameters([
            'user1' => $user
        ]);

        return count($query->getResult());
    }
}
