<?php

namespace AppBundle\Forms\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Forms\DataTransformer\EntityToIntTransformer;

class BookToIntTransformer extends EntityToIntTransformer
{
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("AppBundle:Entity:Book");
        $this->setEntityRepository("AppBundle:Book");
        $this->setEntityType("book");
    }

}
