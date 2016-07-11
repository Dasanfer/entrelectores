<?php

namespace AppBundle\Forms\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Forms\DataTransformer\EntityToIntTransformer;

class UserToIntTransformer extends EntityToIntTransformer
{
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("AppBundle:Entity:User");
        $this->setEntityRepository("AppBundle:User");
        $this->setEntityType("user");
    }

}
