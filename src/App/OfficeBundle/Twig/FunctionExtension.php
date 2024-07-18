<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Twig;

use \Twig_Filter_Function;
use \Twig_Filter_Method;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use App\OfficeBundle\Entity\Office;

/**
 * Class FunctionExtension
 *
 * @author wojciech przygoda
 */
class FunctionExtension extends \Twig_Extension
{
    /**
     *
     * @var OfficeManager office manager
     */
    private $officeManager;
    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param Container $services  services container
     */
    function __construct(Container $services) {

        $this->officeManager = $services->get('office.manager');
        $this->services = $services;
    }
    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('officesList',[$this,'getOfficesList'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('officesMobileMenu',[$this,'getOfficesMobileMenu'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('creditOfficesList',[$this,'getCreditOfficesList'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('developmentOfficesList',[$this,'getDevelopmentOfficesList'],['is_safe' => ['html']]),
            new \Twig_SimpleFunction('officeSubdomain',[$this,'getOfficeSubdomain'],['is_safe' => ['html']]),
            ];
    }

    /**
     * Get offices list
     *
     * @param string $countryIsoCode
     * @return string
     * @throws \Twig_Error
     */
    public function getOfficesList($countryIsoCode = 'pl') {

        $offices = $this->officeManager->getAll(Office::TYPE_PROPERTIES, 1, $countryIsoCode);

        return $this->services->get('templating')
                    ->render("AppOfficeBundle:Twig:list.html.twig",
                            array("offices" => $offices));
    }
    public function getOfficesMobileMenu() {

        $offices = $this->officeManager->getAll(Office::TYPE_PROPERTIES, 1, 'pl');
        $officesEs = $this->officeManager->getAll(Office::TYPE_PROPERTIES, 1, 'es');

        return $this->services->get('templating')
                    ->render("AppOfficeBundle:Twig:officesMobileMenu.html.twig",
                            ["offices" => $offices,
                             "officesEs" => $officesEs,
                                ]);
    }
    /**
     * Get credit offices list
     * @return string
     */
    public function getCreditOfficesList() {
        $offices = $this->officeManager->getAll(Office::TYPE_CREDIT);
        return $this->services->get('templating')
                    ->render("AppOfficeBundle:Twig:creditList.html.twig",
                            array("offices" => $offices));
    }

    /**
     * @return string
     * @throws \Twig_Error
     */
    public function getDevelopmentOfficesList() {
        $offices = $this->services->get('office.manager')
                                ->getDevelopment();
        return $this->services->get('templating')
            ->render("AppOfficeBundle:Twig:developmentList.html.twig",
                array("offices" => $offices));
    }
    /**
     * Get office subdomain
     *
     * @param string $subdomain
     * @return string
     */
    public function getOfficeSubdomain($subdomain) {
        $request = $this->services->get('request');

        return $request->getScheme(). '://' . $subdomain
                . '.' . $this->services->getParameter('domain');


    }
    /**
     * Get function name
     * @return string
     */
    public function getName()
    {
        return 'office_function_extension';
    }
}

