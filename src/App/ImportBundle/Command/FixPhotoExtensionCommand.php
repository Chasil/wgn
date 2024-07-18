<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class FixPhotoExtensionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('fix:photo:extension')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $offerSQLQuery = 'SELECT id, mainPhoto FROM `article` where mainPhoto !=""';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $logFile = 'images.log';
        foreach($result as $row){
            $path = __DIR__ . '/../../../../web/uploads/articles/'.$row['id'];

            if(!preg_match('/\.jpg$/', $row['mainPhoto']) &&
                    !preg_match('/\.jpeg$/', $row['mainPhoto']) &&
                    !preg_match('/\.png$/', $row['mainPhoto']) &&
                    !preg_match('/\.gif$/', $row['mainPhoto'])){

                if($this->remote_image_exists('http://old.wgn.pl/ilustracje/'.rawurlencode($row['mainPhoto']))){
                    $output->writeln('file exists');
                    file_put_contents($logFile, "article id ".$row['id']. "file exists ".$row['mainPhoto']."\n",FILE_APPEND);
                    $newName = sha1(uniqid()).'.jpg';

                    @copy('http://old.wgn.pl/ilustracje/'.rawurlencode($row['mainPhoto']),$path.'/'.$row['mainPhoto']);
                    $mime = mime_content_type($path.'/'.$row['mainPhoto']);
                    file_put_contents($logFile, "mime type ".$mime."\n",FILE_APPEND);
                    $parts = explode('/',$mime);

                    $newName = sha1(uniqid(mt_rand(), true)).'.'.$parts[1];
                    file_put_contents($logFile, "new name " .$newName."\n",FILE_APPEND);
                    rename($path.'/'.$row['mainPhoto'], $path.'/'.$newName);

                    $updateSQLQuery = 'UPDATE `article` set mainPhoto = :mainPhoto where id = :id LIMIT 1';
                    $stmt = $conn->prepare($updateSQLQuery);
                    $stmt->bindValue('mainPhoto',$newName);
                    $stmt->bindValue('id',(int)$row['id']);
                    $stmt->execute();

                }
                $output->writeln('http://old.wgn.pl/ilustracje/'.rawurlencode($row['mainPhoto']));
                $output->writeln('########### article id '.$row['id']. ' photo '.$row['mainPhoto']);
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
?>
