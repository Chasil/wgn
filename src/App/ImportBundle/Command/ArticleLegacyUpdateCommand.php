<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class ArticleLegacyUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:update:legacy')
            ->setDescription('import office photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('test');
        $conn = $em->getConnection();
        $articlesSQLQuery = 'select * from wgn_art';
        $stmt_1 = $conn->prepare($articlesSQLQuery);
        $stmt_1->execute();
        $i= 0;
        while ($row = $stmt_1->fetch()) {
            $output->writeln($i);
            $this->updateArticle($row,$output);
             $i++;
        }

    }

    protected function updateArticle($article,$output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $articleSQLQuery = 'select * from article WHERE name = :name AND legacyCategory = :legacyCategory AND intro = :intro LIMIT 1';
        $stmt_1 = $conn->prepare($articleSQLQuery);
        $stmt_1->bindValue('name',$article['tytul']);
        $stmt_1->bindValue('legacyCategory',(int)$article['id_kat']);
        $stmt_1->bindValue('intro',$article['intro']);
        $stmt_1->execute();
        $articleNew = $stmt_1->fetch();
        if($articleNew){
            $output->writeln('aupdate'.$articleNew['id'].' '.$articleNew['name']);
            $insertImageSQL = "UPDATE article SET legacyId = :legacyId WHERE id = :id";
            $stmt3 = $conn->prepare($insertImageSQL);
            $stmt3->bindValue('legacyId',(int)$article['id']);
            $stmt3->bindValue('id',$articleNew['id']);
            $stmt3->execute();
            //$conn->lastInsertId();
            $modifyDate = date('Y-m-d');
        }


    }
}

