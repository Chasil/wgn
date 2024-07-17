<?php

namespace App\OfferBundle\Command;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\LocationAutocomplete;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSubdomainCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('offer:subdomain:update')
            ->setDescription('Add subdomain to offer');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $logFile = __DIR__ . '/subdomain.log';

        $sqlQuery = 'SELECT * FROM offer where country_id = 1;';
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
                file_put_contents($logFile, sprintf(
                    "[%s] - offer id: %s no data region [%s] city [%s] \n",
                    date('Y-m-d G:i:s'),
                    $row['id'],
                    $row['region'],
                    $row['city']
                ), FILE_APPEND);
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

                file_put_contents($logFile, sprintf(
                    "[%s] - offer id: %s no location from query [%s], region [%s] district [%s] city [%s]  \n",
                    date('Y-m-d G:i:s'),
                    $row['id'],
                    $query,
                    $row['region'],
                    $row['district'],
                    $row['city']
                ), FILE_APPEND);

                $updateSql = "UPDATE offer SET subdomain= :subdomain WHERE id = :id";
                $stmt = $conn->prepare($updateSql);
                $stmt->bindValue('id',$row['id']);
                $stmt->bindValue('subdomain','');
                $stmt->execute();

                continue;
            }

            if(strtolower($location['city']) != strtolower($row['city']))
            {
                $output->writeln(sprintf('city not match %s - %s',$location['city'],$row['city']));
                $updateSql = "UPDATE offer SET subdomain= :subdomain WHERE id = :id";
                $stmt = $conn->prepare($updateSql);
                $stmt->bindValue('id',$row['id']);
                $stmt->bindValue('subdomain','');
                $stmt->execute();

                file_put_contents($logFile, sprintf(
                    "[%s] - offer id: %s location id [%s] query [%s] with city [%s] not match  to offer city [%s] \n",
                    date('Y-m-d G:i:s'),
                    $row['id'],
                    $location['id'],
                    $query,
                    $location['city'],
                    $row['city']
                ), FILE_APPEND);
                continue;
            }


            $subdomain = SubdomainHelper::prepareSubdomainFromParameters($row['category_id'],$row['transaction_type_id'],$location['uniqueKey']);

            file_put_contents($logFile, sprintf(
                "[%s] - offer id: %s has location [%s] from query [%s], region [%s] district [%s] city [%s], add subdomain [%s] \n",
                date('Y-m-d G:i:s'),
                $row['id'],
                $location['id'],
                $query,
                $row['region'],
                $row['district'],
                $row['city'],
                $subdomain
            ), FILE_APPEND);
            $updateSql = "UPDATE offer SET subdomain= :subdomain WHERE id = :id";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('subdomain',$subdomain);
            $stmt->execute();
        }
    }
}
