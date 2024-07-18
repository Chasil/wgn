<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use App\OfferBundle\Form\Subscriber\AddCountryFieldSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use App\OfferBundle\Form\SearchFlatType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use App\OfferBundle\Services\SearchManager;

/**
 * Class SearchMobileType
 *
 * @author wojciech przygoda
 */
class SearchMobileType extends AbstractType
{
    /**
     *
     * @var SearchManager database manager
     */
    private $searchManager;

    /**
     * Constructor
     *
     * @param SearchManager $searchManager database manager
     */
    public function __construct(SearchManager $searchManager) {
        $this->searchManager = $searchManager;
    }

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category','choice', array(
                                    'choices'  => array(
                                        "mieszkania"=>1,
                                        "domy"=>2,
                                        "działki"=>3,
                                        "lokale"=>4,
                                        "komercyjne"=>5,
                                        "garaże"=>6),
                                    'choices_as_values' => true,
                                    ))
            ->add('transactionType','choice', array(
                                    'choices'  => array(
                                        "sprzedaż"=>1,
                                        "wynajem"=>3,
                                        ),
                                    'choices_as_values' => true,
                                    ))

//            ->add('priceDefFrom','text',array('required'=>false))
//            ->add('priceDefTo','text',array('required'=>false))
//            ->add('currency','choice', array(
//                                    'choices'  => array(
//                                        "zł"=>1,
//                                        "€"=>2,
//                                        "$"=>3,
//                                        ),
//                                    'choices_as_values' => true,
//                                    ))
            ->addEventSubscriber(new AddCountryFieldSubscriber($this->searchManager))
            ->add('locationIndexLike','text',array('required'=>false))
            ->add('distance','choice', array(
                                    'choices'  => array(
                                        "+0km"=>0,
                                        "+5km"=>5,
                                        "+10km"=>10,
                                        "+15km"=>15,
                                        "+20km"=>20,
                                        "+25km"=>25,
                                        "+30km"=>30,
            ),
                                    'choices_as_values' => true,
                                    ))
        ;

        $builder->get('locationIndexLike')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {

                $query = $event->getData();

                if($query==''){
                  return;
                }
                $suggestedLocation = $this->searchManager->findSuggestedLocation($query);
                if($suggestedLocation){
                     $event->setData($suggestedLocation['name']);
                     return;
                }
                $locations = $this->searchManager->locationIndexAutocomplete($query);
                $location = (isset($locations[0]['name'])) ? $locations[0]['name'] : $query;
                $event->setData($location);
            }
        );
        $builder->setMethod('GET');
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\OfferBundle\Model\SearchMobile',
            'csrf_protection'   => false,
        ));
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName(){
        return 'search';
    }


}
