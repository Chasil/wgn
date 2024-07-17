<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class EntityToIntTransformer
 *
 * @author wojciech przygoda
 */
class EntityToIntTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager database manager
     */
    private $om;
    /**
     *
     * @var Entity entity class
     */
    private $entityClass;

    /**
     *
     * @var EntityRepository entity repository class
     */
    private $entityRepository;

    /**
     * Constructor
     *
     * @param ObjectManager $om database manager
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transform entity
     *
     * @param mixed $entity entity object
     *
     * @return integer
     */
    public function transform($entity)
    {

        if (null === $entity ||!$entity instanceof $this->entityClass) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * Reverse transform entity
     *
     * @param int $id entity id
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed|object
     */
    public function reverseTransform($id)
    {

        if (!$id) {
            return null;
        }

        $entity = $this->om->getRepository($this->entityRepository)->findOneBy(array("id" => $id));

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                'A %s with id "%s" does not exist!',
                $this->entityClass,
                $id
            ));
        }

        return $entity;
    }

    /**
     * Set entity class
     *
     * @param Entity $entityClass  entity class
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * Set entity repository class
     *
     * @param EntityRepository $entityRepository  entity repository class
     */
    public function setEntityRepository($entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

}

