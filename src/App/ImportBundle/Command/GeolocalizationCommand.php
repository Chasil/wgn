<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class GeolocalizationCommand extends ContainerAwareCommand
{
    const STATE_OVER_LIMIT = 'OVER_QUERY_LIMIT';
    const STATE_OK = 'OK';
    protected function configure()
    {
        $this
            ->setName('geolocalization:update')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $offerSQLQuery = 'SELECT id,city,street,region FROM offer WHERE lat ="" AND lng ="" AND city !="" and isPublish=1 and isDelete=0';
        $stmt = $conn->prepare($offerSQLQuery);
        $stmt->execute();

        while ($row = $stmt->fetch()) {
            $result = $this->updateLocalization($row, $output);
            if($result==self::STATE_OVER_LIMIT){
                break;
            }
        }


    }
    protected function updateLocalization($offer,$output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();

        $address =  $offer['region'].' '.$offer['city'].' '.$offer['street'];
        $output->writeln($address);
        $latLang = $this->getGeolocalization($address,$output);

        if($latLang==self::STATE_OVER_LIMIT){
            return self::STATE_OVER_LIMIT;
        }

        if(count($latLang)==2){
            $updateSQLQuery = 'UPDATE offer SET lat = :lat, lng = :lng WHERE id = :id';
            $stmt = $conn->prepare($updateSQLQuery);
            $stmt->bindValue('lat',$latLang['lat']);
            $stmt->bindValue('lng',$latLang['lng']);
            $stmt->bindValue('id',$offer['id']);
            $stmt->execute();
        }

    }
    protected function getGeolocalization($address,$output) {

        $address = urlencode($address);
        $url = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyD0hQlIxCj29s41DhxUPSUpKEnmgzNLOi4&address={$address}";
        $response = file_get_contents($url);
        $output->writeln($response);
        // decode the json
        $resp = json_decode($response, true);
        if($resp['status']==self::STATE_OK){

            // get the important data
            $lat = $resp['results'][0]['geometry']['location']['lat'];
            $lng = $resp['results'][0]['geometry']['location']['lng'];
            $formattedAddress = $resp['results'][0]['formatted_address'];

            // verify if data is complete
            if($lat && $lng && $formattedAddress){
                $output->writeln($formattedAddress);
               return array('lat'=>$lat,'lng'=>$lng);

            }

        }else if($resp['status']==self::STATE_OVER_LIMIT){
            return self::STATE_OVER_LIMIT;
        }

        return null;
    }
}
?>
