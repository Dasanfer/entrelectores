<?php
namespace AppBundle\Services\Polls;
use AppBundle\Entity\PollAnswer;

class PollService extends \Twig_Extension

{
    private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            'hasVoted' => new \Twig_Function_Method($this, 'hasVoted'),
            'totalAnswers' => new \Twig_Function_Method($this, 'totalAnswers')
        );
    }


    public function hasVoted($poll,$ip){

        $em = $this->doctrine->getManager();

        $query = $em->createQuery('SELECT count(a) FROM AppBundle\Entity\PollAnswer a join a.option o join o.poll p WHERE p.id = :pollId and a.ipHash = :ipHash');

        $query->setParameters(array(
            'pollId'=> $poll->getId(),
            'ipHash' => sha1($ip)
        ));

        return $query->getSingleScalarResult() > 0;
    }

    public function voteOption($optionId,$ip){
        $optionRepo =  $this->doctrine->getRepository('AppBundle:Entity:PollOption');

        $option = $optionRepo->findOneById($optionId);

        if(is_null($option))
            return false;

        if($this->hasVoted($option->getPoll(),$ip))
            return false;

        $pollAnswer = new PollAnswer();
        $pollAnswer->setOption($option);
        $pollAnswer->setIpHash(sha1($ip));

        $this->doctrine->getManager()->persist($pollAnswer);

        return true;
    }

    public function totalAnswers($poll){
        $total = 0;
        foreach($poll->options as $option)
            $total += $option->getAnswers()->count();

        return $total;
    }

    public function getLastPoll(){
        $query = $this->doctrine->getManager()->createQuery('SELECT p FROM AppBundle\Entity\Poll p where p.trivia != true and p.active = true order by p.created DESC');
        $results = $query->setMaxResults(1)->getResult();

        if(count($results) > 0)
            return $results[0];
        else
            return null;
    }

    public function getLastTrivia(){
        $query = $this->doctrine->getManager()->createQuery('SELECT p FROM AppBundle\Entity\Poll p where p.trivia = true  and p.active = true order by p.created DESC');
        $results = $query->setMaxResults(1)->getResult();

        if(count($results) > 0)
            return $results[0];
        else
            return null;
    }

    public function getPreviousTrivia(){
         $query = $this->doctrine->getManager()->createQuery('SELECT p FROM AppBundle\Entity\Poll p where p.trivia = true order by p.created DESC');
        $results = $query->setMaxResults(1)->setFirstResult(1)->getResult();

        if(count($results) > 0)
            return $results[0];
        else
            return null;
    }

    public function vote($optionId,$ip){
        $ipHash = sha1($ip);
        $option = $this->doctrine->getRepository('AppBundle:PollOption')->findOneById($optionId);

        if(is_null($option))
            return;

        $query = $this->doctrine->getManager()->createQuery('SELECT count(a) FROM AppBundle\Entity\PollAnswer a join a.option o join o.poll p where p.id = :pollId and a.ipHash = :ip');

        $query->setParameters(['pollId' => $option->getPoll()->getId(),
            'ip' => $ipHash]);

        $count = $query->getSingleScalarResult();

        if($count > 0)
            return $option->getPoll();

        $answer = new PollAnswer();
        $answer->setOption($option);
        $answer->setIpHash($ipHash);

        $this->doctrine->getManager()->persist($answer);
        return $option->getPoll();
    }

    public function getName()
    {
        return 'app_poll';
    }
}
