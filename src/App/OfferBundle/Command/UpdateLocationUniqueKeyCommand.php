<?php

namespace App\OfferBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLocationUniqueKeyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('location:uniquekey:update')
            ->setDescription('Update unique key location');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT * FROM location_autocomplete';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute();

        foreach($stmt->fetchAll() as $row)
        {
            $newUniqueKey = $row['uniqueKey'];

            if(strpos($row['uniqueKey'], '_') !== false)
            {
                $output->writeln($row['name']);
                $newUniqueKey = str_replace('_', '-', $row['uniqueKey']);
                $output->writeln($newUniqueKey);
            }
            $sqlUpdateQuery = 'UPDATE location_autocomplete SET oldUniqueKey = :oldUniqueKey, uniqueKey = :uniqueKey WHERE id = :id';
            $stmt = $conn->prepare($sqlUpdateQuery);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('uniqueKey',$newUniqueKey);
            $stmt->bindValue('oldUniqueKey',$row['uniqueKey']);
            $stmt->execute();
        }
    }
}
