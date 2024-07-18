<?php
/**
 * Created by PhpStorm.
 * User: CP24
 * Date: 25.02.2019
 * Time: 14:24
 */

namespace App\SettingsBundle\Services;


use App\SettingsBundle\Entity\Link;
use App\SettingsBundle\Repository\LinkRepository;
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
            'AppSettingsBundle:Link'
        );
    }

    public function getWidthPagination()
    {
        $qb = $this->repo->createQueryBuilder('l')
            ->orderBy('l.ordering','DESC')
        ;

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
            $maxOrdering = $this->repo->getMaxOrdering();
            $link->setOrdering($maxOrdering+1);
        }

        $this->em->flush();
    }

    public function remove(Link $link)
    {
        $this->em->remove($link);
        $this->em->flush();

        $this->repo->updateOrderingAfterDelete(
            $link->getOrdering()
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
}