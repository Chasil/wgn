<?php
namespace App\OfferBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use App\OfferBundle\Entity\CategoryOfferDescription;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use \Doctrine\Common\Cache\CacheProvider;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class OfferCategoryDescriptionManager {

    protected $om;
    protected $paginator;
    protected $request;
    protected $cache;

    /**
     *
     * @var Container services container
     */
    private $services;

    public function __construct(ObjectManager $om,
                                PaginatorInterface $paginator,
                                RequestStack $requestStack,
                                CacheProvider $cache,
                                Container $container
            ) {
        $this->om = $om;
        $this->paginator = $paginator;
        $this->cache = $cache;
        $this->services = $container;

        if($requestStack->getCurrentRequest()){
            $this->request = $requestStack->getCurrentRequest();
        }
    }
    public function getById($id){
        return $this->om
                    ->getRepository(CategoryOfferDescription::class)
                    ->findOneBy(['id'=>$id]);
    }

    /**
     * Find offer by id
     *
     * @param int $id offer id
     * @param bool $isPublish only published offer
     * @return CategoryOfferDescription
     * @throws NotFoundHttpException
     */
    public function findById($id,$isPublish=false){
        $repo = $this->services
            ->get('doctrine')
            ->getManager()
            ->getRepository('AppOfferBundle:CategoryOfferDescription')
        ;

        if($isPublish){
            $offer = $repo->findOneBy(array('id'=>$id,'isDelete'=>0));
        }else {
            $offer = $repo->findOneById($id);
        }

        return $offer;
    }

    public function save(CategoryOfferDescription $description){


        $this->om->persist($description);
        $this->om->flush();

        $this->cache
             ->delete('offer_category_description_'
                     .$description->getTransactionType()->getId()
                     .'_'. $description->getCategory()->getId()
                     .'_'.$description->getCity());
    }
    public function delete(CategoryOfferDescription $description) {
        $this->cache
             ->delete('offer_category_description_'
                     .$description->getTransactionType()->getId()
                     .'_'. $description->getCategory()->getId()
                     .'_'.$description->getCity());

        $this->om->remove($description);
        $this->om->flush();
    }
    public function getWithPagination(){
        $repo = $this->om
                    ->getRepository(CategoryOfferDescription::class);

        $qb = $repo->createQueryBuilder('d');


        return $this->paginator->paginate(
            $qb->getQuery(),
            $this->request->query->get('page', 1)/*page number*/,
            $this->request->query->get('pp', 20)/*limit per page*/
        );
    }

    public function getDescriptionBy($transactionId,$categoryId,$city = null){

        $key = 'offer_category_description2_' .$transactionId
              .'_'. $categoryId;

        if($city)
        {
            $key .= '_'.$city;
        }

        if ($this->cache->contains($key)) {
            $description = $this->cache->fetch($key);
        } else {
            $description = $this->om->getRepository(CategoryOfferDescription::class)
                                ->findOneByParameters($transactionId,$categoryId,$city);

            $this->cache->save($key, $description,1);
        }

        return $description;
    }

    /**
     * Save image
     * @param CategoryOfferDescriptionImage $image category offer description image
     * @param bool $tmp is tmp offer
     * @throws Exception
     */
    public function saveImage($image,$tmp = true)
    {
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:CategoryOfferDescriptionImage');
        $em->getConnection()->beginTransaction();
        try {
            if($tmp){
                $idOffer = $image->getTmpIdCategoryOfferDescription();
                $image->setOrdering($repo->getMaxOrdering($idOffer)+1);
                $em->persist($image);
                $em->flush();
            }else {
                $image->setOrdering($repo->getMaxOrdering($image->getCategoryOfferDescription()->getId(),
                        false)+1);
                $em->persist($image);
                $em->flush();

                $offer = $image->getCategoryOfferDescription();
                $offer->setFirstAsMainPhoto();
                $this->save($offer);
            }

            $em->getConnection()->commit();
        } catch (\Exception $e) {

            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Update Images ordering
     *
     * @param int $idCategoryOfferDescription category offer description id
     * @param array $ids image ids
     * @param bool $tmp is temp offer
     *
     * @throws Exception
     */
    public function updateImagesOrdering($idCategoryOfferDescription, $ids, $tmp=true){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:CategoryOfferDescriptionImage');
        $em->getConnection()->beginTransaction();
        try {
            foreach($ids as $key=>$value){
                $repo->updateOrdering($value, $key+1);
            }
            if(!$tmp){
                $offer = $this->findById($idCategoryOfferDescription);
                $offer->setFirstAsMainPhoto();
                $this->save($offer);
            }
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Remove image
     *
     * @param int $id category offer description image id
     * @param bool $tmp is tmp category offer description
     * @return boolean
     * @throws Exception
     */
    public function removeImage($id,$tmp=true){
        $em = $this->services->get('doctrine')->getManager();
        $repo = $em->getRepository('AppOfferBundle:CategoryOfferDescriptionImage');
        $em->getConnection()->beginTransaction();
        try {
            $image = $this->findImage($id);

            $em->remove($image);
            $em->flush();

            if($tmp){
                $idCategoryOfferDescription = $image->getTmpIdCategoryOfferDescription();
                $repo->updateOrderingAfterDelete($image->getOrdering(),
                    $image->getTmpIdCategoryOfferDescription());
            }else {
                $idCategoryOfferDescription = $image->getCategoryOfferDescription()->getId();
                $repo->updateOrderingAfterDelete($image->getOrdering(),
                    $image->getCategoryOfferDescription()->getId(),
                    false
                );
                $offer = $image->getCategoryOfferDescription();
                $offer->setFirstAsMainPhoto();
                $this->save($offer);
            }

            $em->getConnection()->commit();
        } catch (Exception $e) {

            $em->getConnection()->rollback();
            throw $e;
        }
        return true;
    }

    /**
     * Find image
     *
     * @param int $id category offer description image id
     * @return CategoryOfferDescriptionImage
     */
    public function findImage($id)
    {
        $repo = $this->services->get('doctrine')->getManager()
            ->getRepository('AppOfferBundle:CategoryOfferDescriptionImage');
        return $repo->findOneById($id);
    }

    /**
     * Find all category offer description images
     *
     * @param int $id category offer description id
     * @return type
     */
    public function findAllOfferImages(int $id){
        $em = $this->services->get('doctrine')->getManager();

        $query = $em->createQueryBuilder('i')
            ->select('i.name')
            ->from('AppOfferBundle:CategoryOfferDescriptionImage', 'i')
            ->where("i.categoryOfferDescription = :id")
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * Fild add category offer description images
     *
     * @param int $id category offer description id
     * @return type
     */
    public function findAllImages($id){
        $em = $this->services->get('doctrine')->getManager();

        $query = $em->createQueryBuilder('i')
            ->select('i.name')
            ->from('AppOfferBundle:CategoryOfferDescriptionImage', 'i')
            ->where("i.categoryOfferDescription = :id")
            ->orderBy("i.ordering", 'ASC')
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function getAllImagesByParams($transactionType, $category, $city){
        $em = $this->services->get('doctrine')->getManager();

        $offerCategoryDescriptionId = $em->createQueryBuilder('i')
            ->select('i.id')
            ->from('AppOfferBundle:CategoryOfferDescription', 'i')
            ->where("i.transactionType = :transactionTypeId")
            ->andWhere("i.category = :categoryId")
            ->andWhere("i.city = :city")
            ->setParameter('transactionTypeId', $transactionType)
            ->setParameter('categoryId', $category)
            ->setParameter('city', $city)
            ->getQuery()
            ->getResult()
        ;

        return $this->findAllImages($offerCategoryDescriptionId);
    }

    public function getIdByParams($transactionType, $category, $city){
        $em = $this->services->get('doctrine')->getManager();

        $offerCategoryDescriptionId = $em->createQueryBuilder('i')
            ->select('i.id')
            ->from('AppOfferBundle:CategoryOfferDescription', 'i')
            ->where("i.transactionType = :transactionTypeId")
            ->andWhere("i.category = :categoryId")
            ->andWhere("i.city = :city")
            ->setParameter('transactionTypeId', $transactionType)
            ->setParameter('categoryId', $category)
            ->setParameter('city', $city)
            ->getQuery()
            ->getResult()
        ;

        return $offerCategoryDescriptionId;
    }
}

