<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use App\AppBundle\Form\DataTransformer\EntityToIntTransformer;
/**
 * Class UserToIntTransformer
 *
 * @author wojciech przygoda
 */
class UserToIntTransformer extends EntityToIntTransformer
{
    /**
     * Constructor
     *
     * @param ObjectManager $om database manager
     */
    public function __construct(ObjectManager $om)
    {
        parent::__construct($om);
        $this->setEntityClass("App\\UserBundle\\Entity\\User");
        $this->setEntityRepository("AppUserBundle:User");
    }

}
