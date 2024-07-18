<?php

namespace App\OfferBundle\Command;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\LocationAutocomplete;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetSubdomainCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('offer:subdomain:get')
            ->setDescription('Add subdomain to offer')
            ->addArgument('id',InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT * FROM offer where id =:id';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute(['id'=>$input->getArgument('id')]);
        $searchManager = $this->getContainer()->get('search.manager');
        $row = $stmt->fetch();

            $query = $row['region'];

            if(empty($row['region']) || empty($row['city']))
            {
                $output->writeln($row['id']);
                $output->writeln('no data');
                return;
            }
            if($row['district']!='' && strpos($row['district'], 'grodzki') === false)
            {
                $query .= ', ' .  $row['district'];
            }

            $query .= ', ' . $row['city'];
            $output->writeln("query: " . $query);
            $location = $searchManager->findClosestLocation($query);

            if(!$location)
            {

                $output->writeln('brak lokacji');
                $output->writeln($row['id']);
                $output->writeln($row['name']);
                $output->writeln($row['transaction_type_id']);
                $output->writeln($row['category_id']);
                $output->writeln($row['region']);
                $output->writeln($row['district']);
                $output->writeln($row['city']);
                return;
            }

    }
}
