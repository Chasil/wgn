<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;
use App\ArticleBundle\Entity\Article;
use App\ArticleBundle\Entity\Tag;
class ImportArticleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:article')
            ->setDescription('import office photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('test');
        $em2 = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM wgn_art WHERE id > 7236");

        $statement->execute();
        $results = $statement->fetchAll();
        $repo = $em2->getRepository('AppArticleBundle:ArticleCategory');
        foreach($results as $result){
            $art = new Article();
            if($result['status']==3){
                $state = 1;
            }else {
                $state =0;
            }
            $category = $repo->findOneByLegacyId($result['id_kat']);
            if(!is_object($category)){
                $category = $repo->findOneById(59);
            }
            $result['id_kat']."<br>";
            $category->getId()."<br>";

            $art->setCategory($category)
                ->setName($result['tytul'])
                ->setIntro($result['intro'])
                ->setContent($result['tresc'])
                ->setCreateDate(new \DateTime($result['ddata']))
                ->setIsPublish($state);
              $art->setLegacyId($result['id']);
              $art->setLegacyCategory($result['id_kat']);

            if($result['foto']!=''){
                $pathInfo = pathinfo('https://wgn.pl'.$result['foto']);
                $art->setMainPhoto($pathInfo['basename']);
            }

            $tags = array_filter(array_map('trim', explode(',', $result['tagi'])));
            foreach($tags as $tag){
            $item = $em2->getRepository('App\ArticleBundle\Entity\Tag')
                                 ->findOneByName($tag);
                if(!is_object($item)){
                    $item = new Tag();
                    $item->setName($tag);
                }
                if(!$art->getTags()->contains($item)){
                    $art->getTags()->add($item);
                }
            }
            $articleManager = $this->getContainer()->get('article.manager');
            $articleManager->save($art);
            $id = $art->getId();
            echo $path = __DIR__ . '/../../../../web/uploads/articles/'.$id;
            mkdir($path);

            if($result['foto']!=''){
                $pathInfo = pathinfo('https://wgn.pl'.$result['foto']);
                    @copy('https://wgn.pl'.$result['foto'],$path.'/'.$pathInfo['basename']);


            }

        }

    }
    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}

