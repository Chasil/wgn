<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

class GeolocalizationAutocompleteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('geolocalization:autocomplete:update')
            ->setDescription('update carrency exchange rates');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $acSQLQuery = 'SELECT id, name FROM location_autocomplete where lat is null and lng is null';
        $stmt = $conn->prepare($acSQLQuery);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $this->updateLocalization($row, $output);
            //sleep(1);
        }
    }
    protected function updateLocalization($ac,$output){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $address =  $ac['name'];
        $output->writeln($address);
        $latLang = $this->getGeolocalization($address,$output);

        if(count($latLang)==2){
            $updateSQLQuery = 'UPDATE location_autocomplete SET lat = :lat, lng = :lng WHERE id = :id';
            $stmt = $conn->prepare($updateSQLQuery);
            $stmt->bindValue('lat',$latLang['lat']);
            $stmt->bindValue('lng',$latLang['lng']);
            $stmt->bindValue('id',$ac['id']);
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
        if($resp['status']=='OK'){

            // get the important data
            $lat = $resp['results'][0]['geometry']['location']['lat'];
            $lng = $resp['results'][0]['geometry']['location']['lng'];
            $formattedAddress = $resp['results'][0]['formatted_address'];

            // verify if data is complete
            if($lat && $lng && $formattedAddress){
                $output->writeln($formattedAddress);
               return array('lat'=>$lat,'lng'=>$lng);

            }

        }

        return null;
    }
}
?>
