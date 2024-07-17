<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;
use App\ArticleBundle\Entity\ArticleImage;
class ImportArticlePhotosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:article:photos')
            ->setDescription('import office photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager('test');
        $conn = $em->getConnection();
        $imagesSQLQuery = 'select * from wgn_galeria order by id_art , poz';
        $stmt_1 = $conn->prepare($imagesSQLQuery,$output);
        $stmt_1->execute();

        while ($row = $stmt_1->fetch()) {
            $this->insertArticlePhoto($row,$output);

        }

    }

    protected function insertArticlePhoto($photo,$output){
        $em2 = $this->getContainer()->get('doctrine')->getManager();
        $conn2 = $em2->getConnection();

        $article = $em2->getRepository('AppArticleBundle:Article')
                       ->findOneByLegacyId($photo['id_art']);

        if(!is_object($article)){
            $output->writeln('article not found'.$photo['id_art']);
            return false;
        }

        $path = __DIR__ . '/../../../../web/uploads/articles/'.$article->getId();
        if(!file_exists($path)){
           mkdir($path);
        }

        $url = 'https://www.wgn.pl'.$photo['plik'];

        if(!$this->remote_image_exists($url)){
            $output->writeln('file not exists');
            return false;
        }

        $fileInfo = pathinfo($url);
        $file = $fileInfo['basename'];
        $image = $em2->getRepository('AppArticleBundle:ArticleImage')
                     ->findOneByLegacyId($photo['id']);
        if(!is_object($image)){
            $output->writeln('insert image');
            $insertImageSQL = "INSERT INTO article_image ( modifyDate,createDate, ordering, name,legacyId, article_id) VALUES (:modifyDate,:createDate, :ordering, :name,:legacyId, :article_id)";
            $stmt4 = $conn2->prepare($insertImageSQL);
            $stmt4->bindValue('name',$file);
            $stmt4->bindValue('ordering',$photo['poz']+1);
            $stmt4->bindValue('modifyDate',$photo['ddata']);
            $stmt4->bindValue('createDate',$photo['udata']);
            $stmt4->bindValue('legacyId',$photo['id']);
            $stmt4->bindValue('article_id',$article->getId());
            $stmt4->execute();
        }else{
            $output->writeln('update image');
            $updateImageSQL = "UPDATE article_image SET modifyDate = :modifyDate,createDate = :createDate ,ordering = :ordering, name = :name,article_id=:article_id WHERE legacyId = :id";
            $stmt4 = $conn2->prepare($updateImageSQL);
            $stmt4->bindValue('name',$file);
            $stmt4->bindValue('ordering',$photo['poz']+1);
            $stmt4->bindValue('id',$photo['id']);
            $stmt4->bindValue('modifyDate',$photo['ddata']);
            $stmt4->bindValue('createDate',$photo['udata']);
            $stmt4->bindValue('article_id',$article->getId());
            $stmt4->execute();
        }

        if(!file_exists($path.'/'.$file)){
            $output->writeln('from '.$url);
            $output->writeln('copy image '.$path.'/'.$file);
            copy($url, $path.'/'.$file);
        }

    }

    protected function remote_image_exists($url){
        if( @file_get_contents($url) === false ){
            return false;
        }

        return true;
    }
}

