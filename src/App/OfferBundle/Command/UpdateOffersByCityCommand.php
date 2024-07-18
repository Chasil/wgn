<?php

namespace App\OfferBundle\Command;

use App\AppBundle\Component\SubdomainHelper;
use App\OfferBundle\Entity\LocationAutocomplete;
use App\SearchLinkBundle\Entity\Category;
use App\SearchLinkBundle\Entity\Link;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UpdateOffersByCityCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('offer:city:update')
            ->setDescription('Update offers by city');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $searchLinkCategoryManager = $this->getContainer()->get('search_link_category.manager');
        $searchLinkManager = $this->getContainer()->get('search_link.manager');

        $categories = $searchLinkCategoryManager->getAll();
        $em = $this->getContainer()->get('doctrine')->getManager();

        foreach ($categories as $category) {

            $subdomains = $this->getSubdomainsWithOffers($category, 5);
            if(empty($subdomains)) {
                continue;
            }
            $this->removeLinksByCategory($category);

            foreach($subdomains as $subdomain) {
                $this->addCityLink($subdomain, $category);
            }
        }
    }
    protected function addCityLink(array $subdomain, Category $category) {
        $searchLinkManager = $this->getContainer()->get('search_link.manager');
        $name = sprintf('%s', $subdomain['city']);
        $url = 'https:'. $this->getContainer()->get('router')->generate('frontend_offer_search_subdomain',['subdomain' => $subdomain['subdomain']]);
        $searchLinkManager->add(Link::create($name, $url,  $category));
    }
    protected function getSubdomainsWithOffers(Category $category, $minimumOffersCount) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $sqlQuery = 'SELECT COUNT(*) AS count_offers, subdomain, city FROM  offer 
                            where transaction_type_id = :transactionTypeId and category_id = :categoryId and subdomain IS NOT NULL AND subdomain != ""
                            AND isPublish = 1 AND expireDate >= NOW()
                            GROUP BY subdomain HAVING count_offers > :minimumOffersCount;';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute([
            'transactionTypeId' => $category->getTransactionType()->getId(),
            'categoryId' => $category->getCategory()->getId(),
            'minimumOffersCount'=>$minimumOffersCount,

        ]);

        return $stmt->fetchAll();
    }
    protected function removeLinksByCategory(Category $category) {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $conn = $em->getConnection();
        $sqlQuery = 'DELETE FROM link where category_id = :categoryId;';
        $stmt = $conn->prepare($sqlQuery);
        $stmt->execute([
            'categoryId' => $category->getId(),
        ]);

    }

}
