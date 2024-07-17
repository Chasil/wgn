<?php
namespace App\SitemapBundle\Twig;
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
            new \Twig_SimpleFilter('prepareSitemapUrl', array($this, 'prepareSitemapUrlFilter')),
			new \Twig_SimpleFilter('basename', [$this, 'basenameFilter']),
        );
    }

    /**
     * Filter Url
     * @param string $url
     * @param array $replace
     * @param string $delimiter
     * @return string
     */
    public function prepareSitemapUrlFilter($url, $replace=array(), $delimiter='-')
    {
    	setlocale(LC_ALL, 'en_US.UTF8');
        if( !empty($replace) )
        {
			$str = str_replace((array)$replace, ' ', $url);
		}
		
		$clean = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $url );
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		if(!$clean )
		{
			return 'domyslna';
		}
	
		return $clean;
    }
	
	/**
	 * @var string $value
	 * @return string
	 */
	public function basenameFilter( $value, $suffix = '' )
	{
		return basename($value, $suffix);
	}
    
    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return 'sitemap_filter_extension';
    }
}

