<?php

namespace App\OfferBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLinksCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('links:update')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT * FROM link';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute();

        foreach($stmt->fetchAll() as $row)
        {
            $output->writeln('old: ' . $row['url']);
            $parts = explode('/',$row['url']);
            $output->writeln(count($parts));
            if(count($parts)<6)
            {
                $output->writeln('za malo elementow'. count($parts));
                continue;
            }


            $type = $parts[4];

            if($type=='obiekt-komercyjny')
            {
                $type = 'komercyjne';
            }


            $subdomain = $parts[3] . '-' . $type . '-' . str_replace('_', '-', $parts[5]);
            $link = 'https://' . $subdomain . '.wgn.pl';
            $output->writeln($link);

            $sqlUpdateQuery = 'UPDATE link SET url = :url WHERE id = :id';
            $stmt = $conn->prepare($sqlUpdateQuery);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('url',$link);
            $stmt->execute();
        }
    }
}
