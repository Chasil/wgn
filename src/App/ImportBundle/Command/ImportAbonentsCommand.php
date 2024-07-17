<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;
use App\UserBundle\Entity\Address;
use App\UserBundle\Entity\CompanyData;

class ImportAbonentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('import:abonents')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('user.manager');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $em2 = $this->getContainer()->get('doctrine')->getManager('test');
        $conn2 = $em2->getConnection();
        $userBuisnessSQLQuery = "select * from wgn_biura";
        $stmtBuisnessUser = $conn2->prepare($userBuisnessSQLQuery);
        $stmtBuisnessUser->execute();
        while ($agency = $stmtBuisnessUser->fetch()) {
             if (substr($agency['sygnatura'], 0, 3) !== "ABO") {
                 continue;
             }
                    $userNew = $userManager->findBySignature($agency['sygnatura']);
                    $update = true;
                    if(!$userNew){
                        $userNew = new User();
                        $update = FALSE;
                    }
                    $username = null;
                    if(strlen($agency['email1'])> 2){
                        $output->writeln('username email' . $agency['email1']);
                        $username = $agency['email1'];
                    }else {
                        $output->writeln('username erandom' . $agency['email1']);
                        $username = time().'@wgn.pl';
                    }

                    $output->writeln('username ' . $agency['email1'].' u'.$username);
                    $userNew->setSignature($agency['sygnatura'])
                     ->setFirstName('')
                     ->setLastName('')
                     ->setPhone($agency['tel1_praca'])
                     ->setMobile($agency['tel2_praca'])
                     ->setEmail($agency['email1'])
                     ->setPlainPassword(uniqid())
                     ->setRoles(array('ROLE_BUISNESS'));
                            ;
                    $address = $userNew->getDefaultAddress();

                    if(!$address){
                        $address = new Address();
                    }
                    $country = $em->getRepository('AppOfferBundle:Country')->findOneByIsoCode('pl');
                    $address->setStreet($agency['ulica'])
                            ->setZipCode($agency['kod'])
                            ->setIsDefault(true)
                            ->setCity($agency['poczta'])
                            ->setProvince('')
                            ->setName('default')
                            ->setCountry($country);
                    $userNew->addAddress($address);

                    $companyData = $userNew->getCompanyData();
                    $companyAddress = null;
                    if(!is_object($companyData)){
                        $companyData = new CompanyData();
                    }else {
                        $companyAddress = $companyData->getAddress();
                    }


                    if(!is_object($companyAddress)){
                        $companyAddress = new Address();
                    }

                    $companyData->setName($agency['nazwa'])
                                ->setNip($agency['nip'])
                                ->setIsBilling(true);
                    $companyAddress->setStreet($agency['ulica'])
                                   ->setZipCode($agency['kod'])
                                   ->setIsDefault(true)
                                   ->setCity($agency['poczta'])
                                   ->setProvince('')
                                   ->setName('default')
                                   ->setCountry($country);
                    $companyData->setAddress($companyAddress);
                    $userNew->setCompanyData($companyData);

                    if($update){
                        $userManager->update($userNew);
                    }else {
                        $userExists = $userManager->findByUsername($username);
                        if(is_object($userExists)){
                            $output->writeln('istnieje');
                            $username = microtime().'@wgn.pl';
                        }
                        $userNew->setUsername($username);
                        $userManager->add($userNew);
                    }
                    $idUser  = $userNew->getId();
        }
        $output->writeln('update');
    }
}
?>
