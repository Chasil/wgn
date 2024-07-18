<?php
/**
 * Created by PhpStorm.
 * User: Wojtek
 * Date: 28.11.2018
 * Time: 17:35
 */

namespace App\AppBundle\Services;


use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\LocationAutocomplete;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class SubdomainManager
{

    protected $request;
    protected $subdomain;
    protected $om;
    protected $cache;
    protected $cacheLifeTime;

    /**
     * SubdomainManager constructor.
     * @param RequestStack $requestStack
     * @param ObjectManager $objectManager
     * @param Cache $cache
     */
    public function __construct(RequestStack $requestStack, ObjectManager $objectManager, Cache $cache, $cacheLifeTime)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->subdomain = $this->request->get('subdomain');
        $this->om = $objectManager;
        $this->cache = $cache;
        $this->cacheLifeTime = $cacheLifeTime;
    }

    public function getSubdomainTitle()
    {

        if(!SubdomainHelper::isOfferSubdomain($this->subdomain))
        {
            return false;
        }

        $cacheKey = "subdomain_title_" . $this->subdomain ;

        $title = $this->cache->fetch($cacheKey);

        if ($title === false) {
            $title = $this->prepareTitle();
            $this->cache->save($cacheKey,$title ,$this->cacheLifeTime);
        }

        return $title;
        
    }
    protected function prepareTitle()
    {
        $params = SubdomainHelper::getParamsFromSubdomain($this->subdomain);
        $location = $this->om->getRepository(LocationAutocomplete::class)->findOneBy(['uniqueKey'=>$params[2]]);

        if(!is_object($location))
        {
            return '';
        }

        $city = $location->getCity();
        $province = $location->getProvince();


        $category = SubdomainHelper::getCategoryByKey($params[1]);

        $transaction = SubdomainHelper::getTransactionByKey($params[0]);

        $title = $category['plural'] . ' na ' . $transaction['name'];


        $title .= ', ' .$city . ', ' . $province;

        return $title;
    }

}