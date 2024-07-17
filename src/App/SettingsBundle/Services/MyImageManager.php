<?php

namespace App\SettingsBundle\Services;


use App\SettingsBundle\Entity\MyImage;
use Symfony\Component\DependencyInjection\Container;

class MyImageManager
{
	/**
	 *
	 * @var Container services container
	 */
	private $services;
	
	private $em;
	
	/**
	 * Constructor
	 *
	 * @param Container $container services container
	 */
	function __construct(Container $container) {
		$this->services = $container;
		$this->em = $this->services->get('doctrine')->getManager();
	}
	
	
	function save( $myImage )
	{
		$this->em->persist( $myImage );
		$this->em->flush();
	}
	function remove( $id )
	{
	    $image = $this->em->getRepository(MyImage::class)->find($id);
		$this->em->remove( $image );
		$this->em->flush();
	}
	
	function get($id)
	{
		return $this->em
			->getRepository('AppSettingsBundle:MyImage')
			->find($id)
			;

	}
	function getRandom()
	{
		return $this->em
			->getRepository('AppSettingsBundle:MyImage')
			->findRandom()
			;

	}
	public function changeEnabled($id, $enabled)
    {
        $image = $this->em->getRepository(MyImage::class)->find($id);
        $image->setIsEnabled($enabled);
        $this->em->persist($image);
        $this->em->flush();
    }
	public function getAll()
    {
        return  $this->em
                ->getRepository('AppSettingsBundle:MyImage')
                ->findAll()
            ;
    }
}