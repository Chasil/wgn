<?php

namespace App\OfferBundle\Command;

use App\OfferBundle\Tools\OfferTitleBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateOfferTitleCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('offer:update:title');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT * FROM offer WHERE office_id IS NOT NULL and isPublish = 0';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute();

        foreach($stmt->fetchAll() as $row)
        {
            $params = [
                'idCategory'=>$row['category_id'],
                'idTransaction'=>$row['transaction_type_id'],
                'idType'=>$row['type_id'],
                'city'=>$row['city'],
                'dzielnica'=>$row['section'],
                'osiedle'=>$row['sub_section']
            ];
            $title = OfferTitleBuilder::prepareTitle($params);

//            $updateSql = "UPDATE offer SET old_name = :oldName WHERE id = :id";
//            $stmt = $conn->prepare($updateSql);
//            $stmt->bindValue('id',$row['id']);
//            $stmt->bindValue('oldName',$row['name']);
//            $stmt->execute();

            $updateSql = "UPDATE offer SET name= :name WHERE id = :id";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('name',$title);
            $stmt->execute();
        }
    }
}
