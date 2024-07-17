<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class ImportArticleCategoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:articlecategory')
            ->setDescription('import office photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager('test');
        $em2 = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        #copy cats
        $statement = $connection->prepare("SELECT * FROM wgn_art_kat");

        $statement->execute();
        $results = $statement->fetchAll();

        foreach($results as $result){
            if($result['status']==3){
                $state = 1;
            }else {
                $state =0;
            }
            $cat = $em2->getRepository('AppArticleBundle:ArticleCategory')
                       ->findOneByLegacyId($result['id']);

            if(!is_object($cat)){
            $cat = new ArticleCategory();
            $cat->setLegacyId($result['id'])
                ->setName($result['nazwa'])
                ->setMetaDesc($result['nazwa'])
                ->setMetaKeywords('')
                ->setState($state);
            $em2->persist($cat);
            $output->writeln($result['nazwa']);
            }
        }
        $em2->flush();

    }

}

