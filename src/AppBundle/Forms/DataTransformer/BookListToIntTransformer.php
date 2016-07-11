<?php

namespace AppBundle\Forms\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Forms\DataTransformer\EntityToIntTransformer;

class BookListToIntTransformer extends EntityToIntTransformer
{
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("AppBundle:Entity:BookList");
        $this->setEntityRepository("AppBundle:BookList");
        $this->setEntityType("booklist");
    }

}
