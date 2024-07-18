<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 *
 * @author wojciech przygoda
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * Load user by username
     *
     * @param string $username
     * @return null|User
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * Get user max ordering
     * @param int $idOffice user office id
     * @return int
     */
    public function getMaxOrdering($idOffice)
    {

        $em = $this->getEntityManager();
        $connection = $em->getConnection();

        $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM user WHERE office_id = :idOffice");
        $statement->execute(array("idOffice"=>$idOffice));

        $maxOrdering = $statement->fetchColumn(0);

        if($maxOrdering){
            return $maxOrdering;
        }
        return 0;
    }
    /**
     * Find previus user
     *
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function getPrev($user)
    {
        $qb = $this->createQueryBuilder('u')
                      ->join('u.office', 'o')
                      ->where('u.ordering > :ordering')
                      ->andWhere('o.id = :id')
                      ->setParameter('ordering', $user->getOrdering())
                      ->setParameter('id', $user->getOffice()->getId())
                      ->setMaxResults(1)
                      ->orderBy('u.ordering','ASC');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Preview User');
        }
        return $result;
    }

    /**
     * Find next user
     *
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function getNext($user)
    {
        $qb = $this->createQueryBuilder('u')
                   ->join('u.office', 'o')
                   ->where('u.ordering < :ordering')
                   ->andWhere('o.id = :id')
                   ->setParameter('ordering', $user->getOrdering())
                   ->setParameter('id', $user->getOffice()->getId())
                   ->setMaxResults(1)
                   ->orderBy('u.ordering','DESC')
        ;

        $query = $qb->getQuery();
        $result = $query->getOneOrNullResult();
        if(!$result){
            throw new \Exception('Not Found Next User');
        }
        return $result;
    }
    /**
     * Update Articles ordering
     *
     * @param int $lastOrdering last ordering
     * @param int $idOffice user office id
     */
    public function updateOrderingAfterDelete($lastOrdering,$idOffice) {

        $this->getEntityManager()
             ->getConnection()
             ->prepare("UPDATE user SET ordering = ordering - 1
                         WHERE ordering > :lastOrdering AND office_id=:idOffice")
             ->execute(array("idOffice"=> $idOffice,"lastOrdering"=> $lastOrdering));
        }
}
