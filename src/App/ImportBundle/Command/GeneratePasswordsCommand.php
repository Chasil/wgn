<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class GeneratePasswordsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('agents:passwords:generate')
            ->setDescription('update offers price');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->getContainer()->get('doctrine')->getRepository('AppUserBundle:User');
        $userManager = $this->getContainer()->get('user.manager');
        $qb = $repo->createQueryBuilder('u')
                   ->leftJoin('u.addresses', 'a')
                   ->leftJoin('u.companyData','c')
                   ->leftJoin('u.office','o')
                   ->andWhere('u.roles LIKE :role')
                   ->orderBy('o.id','DESC')
                   ->setParameter('role', '%ROLE_AGENT%');
                ;
        $path = '/home/webmaster.wgnpl/www/html/';
        $file = 'passwords.txt';
        $agents = $qb->getQuery()->getResult();
        foreach($agents as $agent){
            $password = substr(hash('sha512',rand()),0,12);
            $agent->setPlainPassword($password);
            $userManager->changePassword($agent);

            file_put_contents($path.$file,
                              sprintf("%s;%s;%s;%s;%s\n",
                                      $agent->getOffice()->getName(). ' ' . $agent->getOffice()->getDefaultAddress()->getStreet(),
                                      $agent->getFirstName(),
                                      $agent->getLastName(),
                                      $agent->getUsername(),
                                      $password
                                      ),
                                      FILE_APPEND);
        }


        $output->writeln('update offer price');
    }
}

