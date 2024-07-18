<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Services\Block;

use App\AppBundle\Entity\BlockElement;

/**
 * Interface BlockInterface
 *
 * @author wojciech przygoda
 */
interface BlockInterface {

    /**
     * Set config
     *
     * @param array $config block config
     */
    public function setConfig(array $config);

    /**
     * Get config
     */
    public function getConfig();

    /**
     * Set block element
     *
     * @param BlockElement $element
     */
    public function setElement(BlockElement $element);

    /**
     * Get block element
     */
    public function getElement();

    /**
     * Render block element
     */
    public function render();

    /**
     * Get block element name
     */
    public function getName();
}