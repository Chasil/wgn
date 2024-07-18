<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use App\AppBundle\Form\DataTransformer\EntityToIntTransformer;

/**
 * Class OfficeToIntTransformer
 *
 * @author wojciech przygoda
 */
class OfficeToIntTransformer extends EntityToIntTransformer
{
    /**
     * Constructor
     * 
     * @param ObjectManager $om database manager
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("App\\OfficeBundle\\Entity\\Office");
        $this->setEntityRepository("AppOfficeBundle:Office");
    }

}
