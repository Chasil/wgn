<?php

namespace App\OfferBundle\Command;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\LocationAutocomplete;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvalidLocationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('offer:location:invalid')
            ->setDescription('Add subdomain to offer');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT * FROM offer where country_id = 1 and (subdomain ="" or subdomain is null) and isPublish=1 AND expireDate >= NOW() AND isDelete = 0';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute();
        $searchManager = $this->getContainer()->get('search.manager');
        file_put_contents(__DIR__ . '/report.csv', sprintf("ID;NR;WOJ;POW;MI;NAZWA;ERR\n"), FILE_APPEND);
        foreach($stmt->fetchAll() as $row)
        {
            $query = $row['region'];

            if(empty($row['region']) || empty($row['city']))
            {
                $output->writeln($row['id']);
                $output->writeln('no data');

                file_put_contents(__DIR__ . '/report.csv', sprintf("%s;%s;%s;%s;%s;%s;%s\n",
                    $row['id'],
                    $row['signature'],
                    $row['region'],
                    $row['district'],
                    $row['city'],
                    $row['name'],
                    'brak miasta lub wojewodztwa'
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

                file_put_contents(__DIR__ . '/report.csv', sprintf("%s;%s;%s;%s;%s;%s;%s\n",
                    $row['id'],
                    $row['signature'],
                    $row['region'],
                    $row['district'],
                    $row['city'],
                    $row['name'],
                    'brak lokalizacji ' . $query
                ), FILE_APPEND);
                continue;
            }

            if($location['city'] != $row['city'])
            {
                file_put_contents(__DIR__ . '/report.csv', sprintf("%s;%s;%s;%s;%s;%s;%s\n",
                    $row['id'],
                    $row['signature'],
                    $row['region'],
                    $row['district'],
                    $row['city'],
                    $row['name'],
                    'miasto ('.$row['city'].') znalezionej lokazizacji nie pasuje ('.$location['city'].') - lokalizacja ' . $query
                ), FILE_APPEND);
                $output->writeln(sprintf('city not match %s %s',$location['city'],$row['city']));
                continue;
            }
        }
    }
}
