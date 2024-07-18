<?php
namespace App\ImportBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\OfficeBundle\Entity\Office;
use App\UserBundle\Entity\User;
class AgentFromDbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('agents:ordering')
            ->setDescription('import offices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $agentsSQLQuery = 'SELECT user.id as id, office.id as idOffice FROM user JOIN office ON user.office_id = office.id WHERE roles LIKE :role Order BY office.id';
                $stmt = $conn->prepare($agentsSQLQuery);
                $stmt->bindValue('role','%ROLE_AGENT%');
                $stmt->execute();

        while ($row = $stmt->fetch()) {
            $output->writeln('agent');
            $connection = $em->getConnection();

            $statement = $connection->prepare("SELECT MAX(ordering) AS maxOrdering FROM user WHERE office_id = :idOffice");
            $statement->execute(array("idOffice"=>$row['idOffice']));

            $maxOrdering = $statement->fetchColumn(0);

            $connection->prepare("UPDATE user SET ordering = :ordering
                         WHERE id=:id")
             ->execute(array("id"=> $row['id'],"ordering"=> $maxOrdering + 1));
        }
    }
}

