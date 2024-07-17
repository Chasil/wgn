<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use App\ArticleBundle\Entity\Tag;

/**
 * Class TagsTransformer
 *
 * @author wojciech przygoda
 */
class TagsTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager database manager
     */
    private $manager;

    /**
     * Constructor
     *
     * @param ObjectManager $manager database manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }
    /**
     * Transform
     *
     * @param Tag[] $collection collection of tags
     *
     * @return string
     */
    public function transform($collection)
    {
        $tags = array();
        if (!$collection) {
            return '';
        }
        foreach($collection as $tag){
            $tags[] = $tag->getName();
        }
        return implode(', ', $tags); // concatenate the tags to one string
    }

    /**
     * Reverse transform
     *
     * @param string $tagsString
     *
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     *
     * @return Tag[]
     */
    public function reverseTransform($tagsString)
    {
        $collection  = new ArrayCollection();
        if (!$tagsString) {
            return $collection; // default
        }
        $tags = array_filter(array_map('trim', explode(',', $tagsString)));
        foreach($tags as $tag){
            $item = $this->manager->getRepository('App\ArticleBundle\Entity\Tag')
                                 ->findOneByName($tag);
            if(!is_object($item)){
                $item = new Tag();
                $item->setName($tag);
            }
            $collection->add($item);
        }
        return $collection;

    }
}
