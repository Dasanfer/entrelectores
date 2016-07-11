<?php

namespace AppBundle\Forms\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Forms\DataTransformer\EntityToIntTransformer;

class AuthorToIntTransformer extends EntityToIntTransformer
{
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("AppBundle:Entity:Author");
        $this->setEntityRepository("AppBundle:Author");
        $this->setEntityType("author");
    }

}
