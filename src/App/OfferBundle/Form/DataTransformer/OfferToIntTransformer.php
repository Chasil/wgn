<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use App\AppBundle\Form\DataTransformer\EntityToIntTransformer;

/**
 * Class OfferToIntTransformer
 *
 * @author wojciech przygoda
 */
class OfferToIntTransformer extends EntityToIntTransformer
{
    /**
     * Constructor
     * 
     * @param ObjectManager $om database manager
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("App\\OfferBundle\\Entity\\Offer");
        $this->setEntityRepository("AppOfferBundle:Offer");
    }

}
