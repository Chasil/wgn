<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\OfferBundle\Services\SearchManager;

/**
 * Class AddCountryFieldSubscriber
 *
 * @author wojciech przygoda
 */
class AddCountryFieldSubscriber implements EventSubscriberInterface
{
    public function __construct(SearchManager $searchManager) {
        $this->searchManager = $searchManager;
    }
    /**
     *
     * Get Subscribed Events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        );
    }

    /**
     * Add country field
     *
     * @param Form $form form form
     * @param int $idCategory category id
     * @param int $idTranasactionType transaction type id
     * @param int $country
     */
    private function addCountryForm($form, $idCategory, $idTranasactionType, $country = null)
    {

        $idCategory = (!empty($idCategory)) ? $idCategory : 1;
        $idTranasactionType = (!empty($idTranasactionType)) ? $idTranasactionType : 1;
        $formOptions = array(
            'class' => 'AppOfferBundle:Country',
            'choices' => $this->searchManager->findAvailableCountries($idCategory,$idTranasactionType,false)
        );
//
//        if ($country) {
//
//           $formOptions['data'] = $country;
//        }

        $form->add('country','entity', $formOptions);
    }
    /**
     * PreSetData event
     *
     * @param FormEvent $event event
     * @return null
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }
        $accessor    = PropertyAccess::createPropertyAccessor();
        $category = $accessor->getValue($data, 'category');
        $idCategory  = ($category) ? $data->getCategory()->getId() : 1;
        $transactionType = $accessor->getValue($data, 'transactionType');
        $idTranasactionType   = ($transactionType) ? $data->getTransactionType()->getId() : 1;
        $country  = ($data->getCountry()) ? $data->getCountry() : 1;

        $this->addCountryForm($form, $idCategory, $idTranasactionType,$country);
    }

    /**
     * PreSubmit Event
     *
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = (is_array($event->getData())) ? $event->getData() : [] ;
        $form = $event->getForm();

        $idCategory = array_key_exists('category', $data) ? $data['category'] : null;
        $idTranasactionType = array_key_exists('transactionType', $data) ? $data['transactionType'] : null;
        $this->addCountryForm($form, $idCategory,$idTranasactionType);
    }
}