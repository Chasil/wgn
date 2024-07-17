<?php

/**
 * This file is part of the AppImportBundle package.
 *
 */
namespace App\OfferBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportFromInetCommand
 *
 * @author wojciech przygoda
 */
class GenerateLocationKeyCommand extends ContainerAwareCommand {
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('generate:locationkey')
            ->setDescription('generate offers sitemap');
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT id, province, district, city FROM location_autocomplete WHERE section is null and subsection is null and street is null';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute();

        foreach($stmt->fetchAll() as $row){
            if($row['city'] !=''){
                $uniqueKey = mb_strtolower($row['city']);
            }elseif($row['city'] =='' && $row['district'] !=''){
                $uniqueKey = 'powiat-' . mb_strtolower($row['district']);
            }else {
               $uniqueKey = mb_strtolower($row['province']);
            }
            $uniqueKey = $this->prepareUrlFilter($uniqueKey);

            $uniqueKey = $this->checkExistUniqueName($uniqueKey,$output);

            $updateSql = "UPDATE location_autocomplete SET uniqueKey = :uniqueKey WHERE id = :id";
            $stmt = $conn->prepare($updateSql);
            $stmt->bindValue('id',$row['id']);
            $stmt->bindValue('uniqueKey',$uniqueKey);
            $stmt->execute();
            //$output->writeln($uniqueKey);
        }


    }
    protected function checkExistUniqueName($uniqueKey,$output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sqlQuery = 'SELECT id, province, district, city,uniqueKey  FROM location_autocomplete WHERE uniqueKey = :uniqueKey';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->bindValue('uniqueKey',$uniqueKey);
        $stmt->execute();
        $row = $stmt->fetch();

        if(isset($row['uniqueKey'])){
            $parts = explode('_',$uniqueKey);
            $uniqueKey = $parts[0];
            $counter = (isset($parts[1])) ? (int)$parts[1] : 0;
            $uniqueKey .='_'.++$counter;
            $output->writeln($uniqueKey);
            return $this->checkExistUniqueName($uniqueKey,$output);
        }

        return $uniqueKey;


    }
    public function prepareUrlFilter($url, $replace=array(), $delimiter='-')
    {
        setlocale(LC_ALL, 'en_US.UTF8');
        if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $url);
	}

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $url);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
    }
}
