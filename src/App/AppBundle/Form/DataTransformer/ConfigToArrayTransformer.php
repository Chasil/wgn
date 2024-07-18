<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\DataTransformerInterface;
/**
 * Class ConfigToArrayTransformer
 *
 * @author wojciech przygoda
 */
class ConfigToArrayTransformer implements DataTransformerInterface
{
    /**
     *
     * @var array config fields
     */
    private $fields;
    /**
     * Constructor.
     *
     * @param array  $fields config fields
     *
     */
    public function __construct($fields)
    {
        $this->fields = $fields;

    }
    /**
     * Transforms array fields
     *
     * @param array $array config fields  to array
     * @return array
     *
     */
    public function transform($array)
    {
        if (null === $array) {

        }
        $result = array_intersect_key($array, array_flip($this->fields));
        return $result;
    }
    /**
     * Transforms fields
     *
     * @param array $value to config fields
     *
     * @return array
     *
     */
    public function reverseTransform($value)
    {
        $result = array();
        foreach ($this->fields as $field) {
            if (is_object($value[$field])) {
                $result[$field] = $value[$field]->getId();
            }else {
                $result[$field] = $value[$field];
            }
        }
        return $result;
    }
}