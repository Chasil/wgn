<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use App\OfferBundle\Entity\Offer;

/**
 * Class OfferToSignatureTransformer
 *
 * @author wojciech przygoda
 */
class OfferToSignatureTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager database manager
     */
    private $om;

    /**
     * Constructor
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transform
     *
     * @param mixed $entity
     *
     * @return integer
     */
    public function transform($entity)
    {

        if (null === $entity ||!$entity instanceof Offer) {
            return '';
        }

        return $entity->getSignature();
    }

    /**
     * Reverse transform
     *
     * @param mixed $signature
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return mixed|object
     */
    public function reverseTransform($signature)
    {

        if (!$signature) {
            return null;
        }

        $entity = $this->om->getRepository('AppOfferBundle:Offer')->findOneBy(array("signature" => $signature));

        if (null === $entity) {
            throw new TransformationFailedException(sprintf(
                'A %s with id "%s" does not exist!',
                'Offer',
                $signature
            ));
        }

        return $entity;
    }
}

