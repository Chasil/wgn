<?php
/**
 * This file is part of the AppFrontPageBundle package.
 *
 */
namespace App\FrontPageBundle\Twig;

use App\AppBundle\Component\UrlHelper;

/**
 * Class FilterExtension
 *
 * @author wojciech przygoda
 */
class FilterExtension extends \Twig_Extension
{
    /**
     * Return the fileters registered as twig extensions
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('prepareUrl', array($this, 'prepareUrlFilter')),
            new \Twig_SimpleFilter('humanDate', array($this, 'humanDate')),
        );
    }

    /**
     * Filter Url
     * @param string $url
     * @param array $replace
     * @param string $delimiter
     * @return string
     */
    public function prepareUrlFilter($url, $replace=array(), $delimiter='-')
    {

	    return UrlHelper::prepare($url, $replace, $delimiter);
    }
    /**
     * Filter human date
     *
     * @param \DateTime $date
     * @return string
     */
    public function humanDate($date){
        $lessDate = clone new \DateTime();
        $lessDate->modify('-14 days');

        if($date < $lessDate){
            return 'ponad 14 dni temu';
        }

        return $date->format('d.m.Y');
    }
    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return 'frontpage_filter_extension';
    }
}

