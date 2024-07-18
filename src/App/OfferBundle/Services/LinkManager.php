<?php

namespace App\OfferBundle\Services;


use App\OfferBundle\Entity\Link;
use App\OfferBundle\Entity\Offer;
use App\SettingsBundle\Entity\Link as SettingsLink;
use App\OfficeBundle\Entity\Link as OfficeLink;
use App\OfferBundle\Repository\LinkRepository;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class LinkManager
{
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
     * @var LinkRepository
     */
    protected $repo;

    public function __construct(
        EntityManager $em,
        RequestStack $requestStack,
        Paginator $paginator
    )
    {
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->paginator = $paginator;

        $this->repo = $this->em->getRepository(
            'AppOfferBundle:Link'
        );
    }

    public function getWidthPagination($idOffer)
    {
        $qb = $this->repo->createQueryBuilder('l')
            ->andWhere('l.offer = :idOffer')
            ->orderBy('l.ordering','DESC')
        ;

        $qb->setParameter('idOffer', $idOffer);

        return $this->paginator->paginate(
            $qb->getQuery(),
            $this->request->query->get('page', 1)/*page number*/,
            $this->request->query->get('pp', 25)/*limit per page*/
        );
    }

    /**
     * @param $id
     * @return Link|null
     */
    public function getById($id)
    {
        $link = $this->repo->findOneBy(array(
            'id' => $id
        ));

        if(!is_object($link)){
            throw new NotFoundHttpException('Nie znaleziono!');
        }

        return $link;
    }

    public function save(Link $link)
    {
        $this->em->persist($link);

        if(!$link->getId())
        {
            $idOffer = $link->getOffer()->getId();
            $maxOrdering = $this->repo->getMaxOrdering($idOffer);
            $link->setOrdering($maxOrdering+1);
        }

        $this->em->flush();
    }

    public function remove(Link $link)
    {
        $this->em->remove($link);
        $this->em->flush();

        $this->repo->updateOrderingAfterDelete(
            $link->getOrdering(),
            $link->getOffer()->getId()
        );
    }

    public function changeOrdering($id, $direction)
    {
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        $link = $this->getById($id);

        try
        {
            switch($direction)
            {
                case 'up':
                    $related = $this->repo->getPrev($link);
                    $related->decrementOrdering();
                    $link->incrementOrdering();
                    break;

                case 'down':
                    $related = $this->repo->getNext($link);
                    $related->incrementOrdering();
                    $link->decrementOrdering();
                    break;

                default:
                    throw new \Exception('Unknown order direction');
            }

            $this->em->persist($link);
            $this->em->persist($related);
            $this->em->flush();

            $connection->commit();
        }
        catch (\Exception $e)
        {
            $connection->rollBack();
            throw $e;
        }

        return true;
    }
    public function getAllByOffer(Offer $offer)
    {
        $office = $offer->getOffice();
        $officeLinks = [];
        if(is_object($office))
        {
           $officeLinks = $this->em->getRepository(OfficeLink::class)->findBy(['office'=>$office],['ordering'=>'ASC']);
        }
        $links = $this->em->getRepository(Link::class)->findBy(['offer'=>$offer],['ordering'=>'ASC']);

        if(count($links)> 0)
        {
               return array_merge($officeLinks,$links);
        }

       return array_merge($officeLinks, $this->em->getRepository(SettingsLink::class)->findBy([],['ordering'=>'ASC']));
    }

}