<?php

namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
class CheckLocationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('check:locations')
            ->setDescription('check if locations exist in  db')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $helper = $this->getHelper('question');
        $question = new Question('Podaj id biura: ');
        $question->setValidator(function ($answer) {
        if (!is_numeric($answer) ) {
            throw new \RuntimeException(
                'Podaj liczbę'
            );
        }

        return $answer;
        });
        $question->setMaxAttempts(2);
        $id = $helper->ask($input, $output, $question);
        foreach ($this->getLocations($id) as $row){
            if(!$this->existsInDB($row)){
                $this->insert($row, $input, $output);
            }

        }
    }
    private function insert($row,$input,$output){
        $helper = $this->getHelper('question');
        $output->writeln('nie znaleziono');

        $location = mb_strtolower($row['region']).', '.mb_strtolower($row['district']).', '.$row['city'];
        $question = new ConfirmationQuestion(sprintf('Dodać lokalizacje %s do bazy ?',$location), false,'/^(y|t)/i');

        if ($helper->ask($input, $output, $question)) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $conn = $em->getConnection();
            $uniqueKey = $this->generateUniqueKey($row['city']);

            $sql = 'INSERT INTO location_autocomplete (name,city,district,province,uniqueKey) VALUES(:name, :city,:district,:province,:uniqueKey)';

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'name'=>$location,
                'city'=>$row['city'],
                'district'=>$row['district'],
                'province'=>$row['region'],
                'uniqueKey'=>$uniqueKey,
            ]);

            $output->writeln('dodano ' . $uniqueKey);
        }
    }
    private  function generateUniqueKey($city){
        $uniqueKey = self::prepareUniqueKey($city);
        $newKey = $uniqueKey;
        $i = 1;
        while($this->existsUniqueKey($newKey)){
            $newKey = $uniqueKey . '_'.$i;
            $i++;
        }

        return $newKey;
    }
    private function existsUniqueKey($uniqueKey){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sql = 'SELECT city, district, province FROM location_autocomplete where uniqueKey = :uniqueKey';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
                'uniqueKey'=>$uniqueKey,
        ]);

        if(!$stmt->fetch()){
            return false;
        }

        return true;
    }
    private static function prepareUniqueKey($city, $delimiter='-')
    {
        setlocale(LC_ALL, 'en_US.UTF8');

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $city);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
    }
    private function getLocations($idOffice){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sql = 'SELECT city, district, region FROM offer where office_id = :idOffice GROUP BY locationindex';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idOffice'=>$idOffice]);

        return $stmt->fetchAll();
    }

    private function existsInDB(array $row){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $sql = 'SELECT city, district, province FROM location_autocomplete where city LIKE :city AND province LIKE :province AND district LIKE :district';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
                'city'=>$row['city'],
                'district'=>$row['district'],
                'province'=>$row['region']
        ]);

        if(!$stmt->fetch()){
            return false;
        }

        return true;
    }
}
