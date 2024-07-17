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
class ImportNewsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:news')
            ->setDescription('import news');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('test');
        $em2 = $this->getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM wgn_aktualnosci  ORDER BY ddata ASC");

        $statement->execute();
        $results = $statement->fetchAll();
        $repo = $em2->getRepository('AppArticleBundle:ArticleCategory');
        $category = $repo->findOneById(77);
        $ordering = 0;
        foreach($results as $result){
            $art = $em2->getRepository('AppArticleBundle:Article')
                       ->findOneBy(array('legacyId'=>$result['id'],
                                         'category'=>$category));
            $ordering++;

            if($result['status']==3){
                $state = 1;
            }else {
                $state =0;
            }
            if(is_object($art)){
                $output->writeln('has article with legacyId '.$result['id']);
                $art->setOrdering($ordering)
                    ->setIsPublish($state);
                $articleManager = $this->getContainer()->get('article.manager');
                $articleManager->save($art);
                continue;
            }
            $art = new Article();



            $art->setCategory($category)
                ->setName($result['nazwa'])
                ->setIntro($result['intro'])
                ->setContent($result['opis'])
                ->setCreateDate(new \DateTime($result['ddata']))
                ->setIsPublish($state)
                ->setLegacyId($result['id'])
                ->setLegacyCategory(null)
                ->setOrdering($ordering);
            if($result['foto']!=''){
                $pathInfo = pathinfo('http://old.wgn.pl'.$result['foto']);
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
            $path = __DIR__ . '/../../../../web/uploads/articles/'.$id;
            mkdir($path);

            if($result['foto']!=''){
                $pathInfo = pathinfo('http://old.wgn.pl'.$result['foto']);
                    @copy('http://old.wgn.pl'.$result['foto'],$path.'/'.$pathInfo['basename']);


            }
            $output->writeln('add article with legacyId '.$result['id']);

        }

    }

    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}

