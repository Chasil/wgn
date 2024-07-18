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
class MergeArticlesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('articles:merge')
            ->setDescription('import news');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository('AppArticleBundle:ArticleCategory');
        $warto = $repo->findOneById(47);
        $aktualnosci = $repo->findOneById(77);
        $results = $em->getRepository('AppArticleBundle:Article')
                      ->findBy(array('category'=>array($warto,$aktualnosci)),array('createDate' => 'ASC'));

        $category = $repo->findOneById(78);
        $count = 0;
        $new = 0;
        foreach($results as $article){
            $art = $em->getRepository('AppArticleBundle:Article')
                       ->findOneBy(array('legacyId'=>$article->getLegacyId(),
                                         'category'=>$category));
            $count++;
            if(is_object($art)){
                $output->writeln('has article with id'.$article->getId());
                continue;
            }
            $new++;
            $art = clone $article;
            $art->setCategory($category);

            $tags = $art->getTags();
//            foreach($tags as $tag){
//            $item = $em->getRepository('App\ArticleBundle\Entity\Tag')
//                                 ->findOneByName($tag);
//                if(!is_object($item)){
//                    $item = new Tag();
//                    $item->setName($tag);
//                }
//                if(!$art->getTags()->contains($item)){
//                    $art->getTags()->add($item);
//                }
//            }
            $articleManager = $this->getContainer()->get('article.manager');
            $articleManager->save($art);
            $id = $art->getId();
            $path = __DIR__ . '/../../../../web/uploads/articles/'.$id;
            $oldPath= __DIR__ . '/../../../../web/uploads/articles/'.$article->getId();
            mkdir($path);

            if($art->getMainPhoto()){
                @copy($oldPath.'/'.$art->getMainPhoto(),$path.'/'.$art->getMainPhoto());
            }
            $output->writeln('add article with legacyId '.$art->getId().' tags'.count($tags));

        }
         $output->writeln('total '.$count.' new:'.$new);
    }

    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}

