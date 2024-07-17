<?php

namespace App\OfferBundle\Command;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\LocationAutocomplete;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddSubdomainCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('offer:subdomain:add')
            ->setDescription('Add subdomain to offer');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT * FROM offer where country_id = 1 and (subdomain ="" or subdomain is null)';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute();
        $searchManager = $this->getContainer()->get('search.manager');
        foreach($stmt->fetchAll() as $row)
        {
            $query = $row['region'];

            if(empty($row['region']) || empty($row['city']))
            {
                $output->writeln($row['id']);
                $output->writeln('no data');
                continue;
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
                continue;
            }

            if($location['city'] != $row['city'])
            {
                $output->writeln(sprintf('city not match %s %s',$location['city'],$row['city']));
                continue;
            }


            $subdomain = SubdomainHelper::prepareSubdomainFromParameters($row['category_id'],$row['transaction_type_id'],$location['uniqueKey']);

            $updateSql = "UPDATE offer SET subdomain= :subdomain WHERE id = :id";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('subdomain',$subdomain);
            $stmt->execute();
        }
    }
}
