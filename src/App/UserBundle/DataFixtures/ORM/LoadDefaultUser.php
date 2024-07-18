<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\UserBundle\Entity\User;
/**
 * Class LoadUserData
 *
 * @author wojciech przygoda
 */
class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface services container
     */
    private $container;

    /**
     * {@inheritDoc}
     * @param ContainerInterface $container services container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     * @param ObjectManager $manager database manager
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('user.manager');
        $user = new User();

        $user->setEmail('admin@itega.pl');
        $user->setPassword('admin');
        $userManager->add($user);
    }
}

