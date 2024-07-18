<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Services;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use App\OfficeBundle\Entity\Office;
use App\OfficeBundle\Model\SearchOffices;

/**
 * Class OfficeManager
 *
 * @author wojciech przygoda
 */
class OfficeManager {
    /**
     *
     * @var EntityManager entity manager
     */
    protected $em;
    /**
     *
     * @var Request request
     */
    protected $request;
    /**
     *
     * @var Paginator paginator
     */
    protected $paginator;
    /**
     *
     * @var Container services container
     */
    private $services;

    /**
     * Constructor
     *
     * @param EntityManager $em datababse manager
     * @param Request $request request
     * @param type $paginator paginator
     * @param Container $container services container
     */
    public function __construct(
        EntityManager $em,
        RequestStack $requestStack,
        $paginator,
        Container $container
    ){
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->paginator = $paginator;
        $this->services = $container;
    }

    /**
     * Find office by id
     *
     * @param int $id office id
     * @param boolean $onlyPublish find only published office
     * @return Office office
     * @throws NotFoundHttpException
     */
    public function findById($id,$onlyPublish = false) {
        $repo = $this->em->getRepository('AppOfficeBundle:Office');

        $office = $repo->findOneById($id);

        if(!is_object($office)){
             throw new NotFoundHttpException('Nie znaleziono!');
        }

        if($office->getIsPublish()==0 && $onlyPublish){
             throw new NotFoundHttpException('Nie znaleziono!');
        }
        return $office;
    }
    /**
     * Find office by subdomain
     *
     * @param string $subdomain
     * @param boolean $onlyPublish find only published office
     * @return Office
     * @throws NotFoundHttpException
     */
    public function findBySubdomain($subdomain,$onlyPublish = false) {
        $repo = $this->em->getRepository('AppOfficeBundle:Office');

        $office = $repo->findOneBySubdomain($subdomain);

        if(!is_object($office)){
             throw new NotFoundHttpException('Nie znaleziono!');
        }
        if($office->getIsPublish()== 0 && $onlyPublish){
            return null;
//             throw new NotFoundHttpException('Nie znaleziono!');
        }
        return $office;
    }
    /**
     * Find office by import id
     *
     * @param int $id import id
     * @return Office
     */
    public function findByImportId($id) {
        $repo = $this->em->getRepository('AppOfficeBundle:Office');

        $office = $repo->findOneByImportId($id);

        return $office;
    }
    /**
     * Save office
     *
     * @param Office $office office
     */
    public function save(Office $office) {
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $keys = ['offices_1_1','offices_2_1','offices_3_1','offices_4_1'];
        foreach($keys as $key){
            $cache->delete($key);
        }

        $this->em->persist($office);
        $this->em->flush();
    }
    /**
     * Get offices with pagination
     *
     * @param SearchOffices $searchOffices query params
     * @param bool $all
     * @return Office[]
     */
    public function getWidthPagination(SearchOffices $searchOffices,$all=false){
;
        $repo = $this->em->getRepository('AppOfficeBundle:Office');
        $qb = $repo->createQueryBuilder('o')
                   ->leftJoin('o.addresses', 'a')
                   ->where('a.isDefault = 1 OR a.isDefault IS NULL')
                   ->orderBy('o.createDate','DESC')
                ;


        if($searchOffices->getName()){
           $qb->andWhere('o.name LIKE :name')
               ->setParameter('name', '%'.$searchOffices->getName().'%');
        }
        if($searchOffices->getEmail()){
           $qb->andWhere('u.email LIKE :email')
               ->setParameter('email', '%'.$searchOffices->getEmail().'%');
        }
        if($searchOffices->getCity()){
           $qb->andWhere('a.city LIKE :city')
               ->setParameter('city', '%'.$searchOffices->getCity().'%');
        }
        if($all){
            return $qb->getQuery()->getResult();
        }
        return $this->paginator->paginate(
            $qb->getQuery(),
            $this->request->query->get('page', 1)/*page number*/,
            $this->request->query->get('pp', 25)/*limit per page*/
        );
    }
    /**
     * Get all offices
     *
     * @param int $type office type
     * @param int $publish publication state
     * @param string $countryIsoCode
     * @return Office[]
     */
    public function getAll( $type = 1,$publish = 1, $countryIsoCode = 'pl'){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $key = 'offices_'.$type.'_'.$publish . '_' . $countryIsoCode;

        if ($cache->contains($key)) {
            return $cache->fetch($key);
        } else {
            $offices = $this->em->getRepository('AppOfficeBundle:Office')
                                  ->findAllWithDefaultAddress($type,$publish,$countryIsoCode);
            $cache->save($key,$offices,1);
        }
        return $offices;
    }
    /**
     * Save office image
     *
     * @param OfficeImage $image office image
     */
    public function saveImage($image){
        $repo = $this->em->getRepository('AppOfficeBundle:OfficeImage');

        $image->setOrdering($repo->getMaxOrdering($image->getOffice()->getId())+1);
        $this->em->persist($image);
        $this->em->flush();
    }
    /**
     * Find image by id
     * @param int $id image id
     * @return OfficeImage
     */
    public function findImage($id){
        return $this->em->getRepository('AppOfficeBundle:OfficeImage')
                         ->findOneById($id);
    }
    /**
     * Remove image
     *
     * @param int $id
     * @return boolean
     */
    public function removeImage($id){
        $repo = $this->em->getRepository('AppOfficeBundle:OfficeImage');
        $image = $this->findImage($id);

        $this->em->remove($image);
        $this->em->flush();
        $repo->updateOrderingAfterDelete($image->getOrdering(),$image->getOffice()->getId());

        return true;
    }
    /**
     * Update ordering
     *
     * @param int $id image id
     * @param int $ordering image ordering
     */
    public function updateImageOrdering($id, $ordering){
        $repo = $this->em->getRepository('AppOfficeBundle:OfficeImage');
        $repo->updateOrdering($id, $ordering);
    }
    /**
     * Search office
     *
     * @param string $query query params
     * @return Office
     */
    public function search($query) {
        $repo = $this->em->getRepository('AppOfficeBundle:Office');
        return $repo->search($query);
    }
    /**
     * Change office publication
     *
     * @param int $id office id
     * @param bool $publish publication state
     * @throws AccessDeniedException
     * @throws \Exception
     */
    public function changePublish($id, $publish){
        $cache = $this->services->get('doctrine_cache.providers.main_cache');
        $keys = ['offices_1_1','offices_2_1','offices_3_1','offices_4_1'];
        foreach($keys as $key){
            $cache->delete($key);
        }

        $office = $this->findById($id);

        if (false ===  $this->services->get('security.context')
                            ->isGranted('publish', $office)) {
            throw new AccessDeniedException('Access Denid');
        }
        $this->em->getConnection()->beginTransaction();

        try{
           $office->setIsPublish($publish);
           $this->em->persist($office);
           $this->em->flush();

           $repo = $this->em->getRepository('AppOfferBundle:Offer');

           if(!$publish){
                $repo->unpublishOfficeOffers($office);
           }else {
               $repo->publishOfficeOffers($office);
           }

           $this->em->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->em->getConnection()->rollback();
            throw $ex;
        }


    }
    /**
     * Get additional services by type
     * @return AdditionalService[]
     */
    public function getAdditionalServicesByType(){
        return $this->em
                    ->getRepository('AppOfficeBundle:AdditionalService')
                    ->findAllByType();
    }
    public function getDevelopment()
    {
        return $this->em
            ->getRepository('AppOfficeBundle:Office')
            ->findDevelopment();
    }
}
