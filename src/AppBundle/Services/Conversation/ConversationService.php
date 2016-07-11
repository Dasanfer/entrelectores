<?php
namespace AppBundle\Services\Conversation;

class ConversationService  extends \Twig_Extension {

    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }
    public function getFunctions()
    {
        return array(
            'mutualFollowers' => new \Twig_Function_Method($this, 'checkMutualFollowers')
        );
    }

    public function getBookConversations($book,$offset = 0,$limit = 20){
        $qb = $this->getBasicQuery($offset,$limit);
        $qb->andWhere($qb->expr()->eq('b',':book'));
        $qb->setParameters([
            'book' => $book
        ]);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getAuthorConversations($author,$offset = 0,$limit = 20){
        $qb = $this->getBasicQuery($offset,$limit);
        $qb->andWhere($qb->expr()->eq('a',':author'));
        $qb->setParameters([
            'author' => $author
        ]);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getListConversations($list,$offset = 0,$limit = 20){
        $qb = $this->getBasicQuery($offset,$limit);
        $qb->andWhere($qb->expr()->eq('l',':list'));
        $qb->setParameters([
            'list' => $list
        ]);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function getConversationChildren($conversation,$offset=0,$limit=20){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c')->from('AppBundle\Entity\Comment','c')
            ->leftJoin('c.parent','parent')
            ->where($qb->expr()->eq('parent',':parent'))
            ->orderBy('c.created', 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );

        $qb->setParameters([
            'parent' => $conversation
        ]);
        $query = $qb->getQuery();
        return $query->getResult();
    }

    private function getBasicQuery($offset,$limit){
        $em = $this->doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('c, child, (CASE WHEN (child.id is null) THEN c.created ELSE child.created END) as hidden OrderByComm')->from('AppBundle\Entity\Comment','c')
            ->leftJoin('c.book','b')
            ->leftJoin('c.list','l')
            ->leftJoin('c.author','a')
            ->leftJoin('c.children','child')
            ->where($qb->expr()->isNull('c.parent'))
            ->distinct()
            ->orderBy('OrderByComm', 'DESC')
            ->setFirstResult( $offset )
            ->setMaxResults( $limit );
        return $qb;
    }

    public function checkMutualFollowers($user1, $user2){
        return $user1->getFollowers()->contains($user2) && $user2->getFollowers()->contains($user1);

    }

    public function getName(){
        return 'app.conversation';
    }
}
