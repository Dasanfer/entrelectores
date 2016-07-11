<?php
namespace AppBundle\Services\Users;
use AppBundle\Entity\FollowEvent;
use AppBundle\Entity\BookList;
use AppBundle\Services\Timeline\TimelineEvent;
use Doctrine\ORM\Query\ResultSetMapping;

class TimelineService extends \Twig_Extension
{
    private $doctrine;
    private $eventDispatcher;

    public function __construct($doctrine,$eventDispatcher){
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getFunctions()
    {
        return array(
            'doesUserFollow' => new \Twig_Function_Method($this, 'doesUserFollow'),
            'numFollowers' => new \Twig_Function_Method($this, 'numFollowers'),
            'numFollows' => new \Twig_Function_Method($this, 'numFollows')
        );
    }

    public function userTimeline($user,$offset = 0, $limit = 30){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('e')->from('AppBundle\Entity\FollowEvent','e')
            ->where($qb->expr()->orX(
                $qb->expr()->in('e.createdBy', ':users'),
                $qb->expr()->in('e.book', ':books'),
                $qb->expr()->in('e.list', ':lists'),
                $qb->expr()->in('e.author', ':authors')
            ))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->neq('e.createdBy',':user'),
                    $qb->expr()->eq('e.type', '\''.FollowEvent::status.'\'')
                )
            )
            ->orderBy('e.created', 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );

        $lists = array_merge($this->getGlobalLists(),$user->getListsFollowed()->toArray());
        $users = array_merge($this->getGlobalUsers(),$user->getUsersFollowed()->toArray());
        $qb->setParameters([
            'books' => $user->getBooksFollowed()->toArray(),
            'users' => $users,
            'authors' => $user->getAuthorsFollowed()->toArray(),
            'lists' => $lists,
            'user' => $user
        ]);

        $query = $qb->getQuery();
        return $query->getResult();
    }


    public function getGlobalUsers(){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('u')->from('AppBundle\Entity\User','u')
            ->where($qb->expr()->eq('u.publicProfile',true))
            ->andWhere($qb->expr()->eq('u.globalFollow',true))
            ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getGlobalLists(){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('l')->from('AppBundle\Entity\BookList','l')
            ->where($qb->expr()->eq('l.publicFlag',true))
            ->andWhere($qb->expr()->gte('l.globalFollow',BookList::READ_PUBLIC))
            ;

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function userTimelineCount($user){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('count(e.id)')->from('AppBundle\Entity\FollowEvent','e')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('e.user', ':user'),
                $qb->expr()->in('e.user', ':users'),
                $qb->expr()->in('e.book', ':books'),
                $qb->expr()->in('e.list', ':lists'),
                $qb->expr()->in('e.author', ':authors')
            ))
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->neq('e.createdBy',':user'),
                    $qb->expr()->eq('e.type', '\''.FollowEvent::status.'\'')
                )
            );

        $qb->setParameters([
            'books' => $user->getBooksFollowed()->toArray(),
            'users' => $user->getUsersFollowed()->toArray(),
            'authors' => $user->getAuthorsFollowed()->toArray(),
            'lists' => $user->getListsFollowed()->toArray(),
            'user' => $user
        ]);

        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    public function userActivity($user,$offset = 0, $limit = 30){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();

        $qb->select('e')->from('AppBundle\Entity\FollowEvent','e')
            ->where($qb->expr()->eq('e.createdBy',':user'))
            ->orderBy('e.created', 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );

        $qb->setParameters([
            'user' => $user
        ]);

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function doesUserFollow($element,$type,$user){

        if(is_null($user))
            return false;

        $collection = null;

        switch($type){
            case 'book';
                $collection = $user->getBooksFollowed();
                break;
            case 'author';
                $collection = $user->getAuthorsFollowed();
                break;
            case 'list';
                $collection = $user->getListsFollowed();
                break;
            case 'user';
                $collection = $user->getUsersFollowed();
                break;
        }

        if(is_null($collection))
            return false;

        return $collection->contains($element);
    }

    public function followBook($user,$book){
        if(!$user->getBooksFollowed()->contains($book)){
            $user->getBooksFollowed()->add($book);
            $book->getFollowers()->add($user);
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->persist($book);
            $event = new TimelineEvent($user,FollowEvent::follow_book);
            $event->setBook($book);
            $this->eventDispatcher->dispatch('app.timeline_event',$event);
        }
    }

    public function unfollowBook($user,$book){
        if($user->getBooksFollowed()->contains($book)){
            $user->getBooksFollowed()->removeElement($book);
            $this->doctrine->getManager()->persist($user);
        }
    }

    public function followUser($user,$toFollow){
        if(!$user->getUsersFollowed()->contains($toFollow) && $user != $toFollow){
            $user->getUsersFollowed()->add($toFollow);
            $toFollow->getFollowers()->add($user);
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->persist($toFollow);
            $event = new TimelineEvent($user,FollowEvent::follow_user);
            $event->setUser($toFollow);
            $this->eventDispatcher->dispatch('app.timeline_event',$event);
        }
    }

    public function unfollowUser($user,$toUnfollow){
        if($user->getUsersFollowed()->contains($toUnfollow)){
            $user->getUsersFollowed()->removeElement($toUnfollow);
            $this->doctrine->getManager()->persist($user);
        }
    }

    public function followAuthor($user,$author){
        if(!$user->getAuthorsFollowed()->contains($author)){
            $user->getAuthorsFollowed()->add($author);
            $author->getFollowers()->add($user);
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->persist($author);
            $event = new TimelineEvent($user,FollowEvent::follow_author);
            $event->setAuthor($author);
            $this->eventDispatcher->dispatch('app.timeline_event',$event);
        }
    }

    public function unfollowAuthor($user,$author){
        if($user->getAuthorsFollowed()->contains($author)){
            $user->getAuthorsFollowed()->removeElement($author);
            $this->doctrine->getManager()->persist($user);
        }
    }

    public function followList($user,$list){
        if(!$user->getListsFollowed()->contains($list)){
            $user->getListsFollowed()->add($list);
            $list->getFollowers()->add($user);
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->persist($list);
            $event = new TimelineEvent($user,FollowEvent::follow_list);
            $event->setList($list);
            $this->eventDispatcher->dispatch('app.timeline_event',$event);
        }
    }

    public function unfollowList($user,$list){
        if($user->getListsFollowed()->contains($list)){
            $user->getListsFollowed()->removeElement($list);
            $list->getFollowers()->removeElement($user);
            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->persist($list);
        }
    }

    public function numFollows($user){
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('num','num');
        $query = $this->doctrine->getManager()->createNativeQuery('SELECT count(*) as num FROM user_follower WHERE user_source = :user', $rsm);
        $query->setParameter('user', $user->getId());
        return $query->getSingleScalarResult();
    }

    public function numFollowers($user){
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('num','num');
        $query = $this->doctrine->getManager()->createNativeQuery('SELECT count(*) as num FROM user_follower WHERE user_target = :user', $rsm);
        $query->setParameter('user', $user->getId());
        return $query->getSingleScalarResult();
    }

    public function getName(){
        return 'app.user.timeline';
    }
}
