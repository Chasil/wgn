<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Command;

use Doctrine\ORM\Query\QueryException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\UserBundle\Entity\User;

/**
 * Class LookingForCommand
 *
 * @author wojciech przygoda
 */
class LookingForCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('send:lookingfor:offers')
            ->setDescription('update carrency exchange rates');
    }
    /**
     * Execute command
     *
     * @param InputInterface $input input
     * @param OutputInterface $output output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newsletterManager = $this->getContainer()->get('newsletter.manager');
        $searchManager = $this->getContainer()->get('search.manager');
        $subscriptions = $newsletterManager->getAllLookingFor();

        foreach($subscriptions as $subscription){
            try {
                $results = $searchManager->searchLookingFor($subscription->getQuery());
                $output->writeln(count($results));
                if(count($results)>0){
                    $newsletterManager->sendLookingFor($subscription, $results);
                    $output->writeln(sprintf('sent to %s query: %s', $subscription->getEmail(), serialize($subscription->getQuery())));
                }
            } catch (QueryException $e) {
                $output->writeln(sprintf('cant sent to %s query: %s', $subscription->getEmail(), serialize($subscription->getQuery())));
            }


        }


    }
}
?>
