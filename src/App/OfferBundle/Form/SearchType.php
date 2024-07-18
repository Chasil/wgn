<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use App\OfferBundle\Form\SearchFlatType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use App\OfferBundle\Services\SearchManager;
use App\OfferBundle\Form\Subscriber\AddCountryFieldSubscriber;

/**
 * Class SearchType
 *
 * @author wojciech przygoda
 */
class SearchType extends AbstractType
{

    /**
     *
     * @var SearchManager database manager
     */
    private $searchManager;

    /**
     * Constructor
     *
     * @param SearchManager $searchManager  database manager
     *
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
            ->addEventSubscriber(new AddCountryFieldSubscriber($this->searchManager))
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

            ->add('priceDefFrom','text',array('required'=>false))
            ->add('office','hidden',array('required'=>false))
            ->add('user','hidden',array('required'=>false))
            ->add('priceDefTo','text',array('required'=>false))
            ->add('squereFrom','text',array('required'=>false))
            ->add('squereTo','text',array('required'=>false))
            ->add('roomsFrom','text',array('required'=>false))
            ->add('roomsTo','text',array('required'=>false))
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
            ->add('currency','choice', array(
                                    'choices'  => array(
                                        "zł"=>1,
                                        "€"=>2,
                                        "$"=>3,
                                        ),
                                    'choices_as_values' => true,
                                    ))
            ->add('signatureLike','text',array('required'=>false))
            ->add('descriptionLike','text',array('required'=>false))
            ->add('isExclusive','checkbox',array('required'=>false,'label'=>'tylko oferty na wyłączność'))
            ->add('isDirect','checkbox',array('required'=>false,'label'=>'tylko oferty bezpośrednie - 0% prowizji'))
            ->add('mainPhotoHas','checkbox',array('required'=>false,'label'=>'tylko ze zdjęciem'))
            ->add('market','choice', array(
                                    'choices'  => array(
                                        "pierwotny"=>1,
                                        "wtórny"=>2,
                                        ),
                                    'choices_as_values' => true,
                                    'empty_value'=>'dowolny',
                                    ))
            ->add('storeys','choice', array(
                                    'choices'  => array_combine(range(0,30),range(0,30)),
                                    'choices_as_values' => true,
                                    'empty_value'=>'dowolna',
                                    'required'=>false))
            ->add('yearOfBuildingFrom','text',array('required'=>false))
            ->add('yearOfBuildingTo','text',array('required'=>false))
            ->add('priceDefm2From','text',array('required'=>false))
            ->add('priceDefm2To','text',array('required'=>false))
            ->add('floor','choice', array(
                                    'choices'  => array_combine(range(-1,30),range(-1,30)),
                                    'choices_as_values' => true,
                                    'empty_value'=>'dowolne',
                                    'required'=>false))

            ->add('squerePlotFrom','text',array('required'=>false))
            ->add('squerePlotTo','text',array('required'=>false))
            ->add('roof','choice', array(
                                    'choices'  => array(
                                        "kopertowy"=>1,
                                        "dwuspadowy"=>2,
                                        "płaski"=>3,
                                        "inne"=>4,
                                        ),
                                    'choices_as_values' => true,
                                    'empty_value'=>'dowolny',
                                    ))
            ->add('technicalCondition','choice', array(
                                    'choices'  => array(
                                        "do zamieszkania"=>1,
                                        "do wykończenia"=>2,
                                        "do remontu"=>3,
                                        "deweloperskie"=>4,
                                        ),
                                    'choices_as_values' => true,
                                    'empty_value'=>'dowolny',
                                    ))

                ;

        $builder->setMethod('GET');

        $formModifier = function (FormInterface $form, $category = null) {

            $id = null === $category ? 1 : $category;

            $form->add('type', 'entity', array(
                            'class' => 'AppOfferBundle:Type',
                            'multiple'     => false,
                            'expanded' => false,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\TypeRepository $repo)use($id) {
                                return $repo->createQueryBuilder('t')
                                    ->join('t.category', 'c')
                                    ->where('t.isPublish=1')
                                    ->andWhere('c.id=:id')
                                    ->setParameter('id', $id)
                                    ->orderBy('t.ordering','ASC')
                                ;
                            },
                            'empty_value'=>'dowolny',
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))
            ->add('additionalInfo', 'entity', array(
                            'class' => 'AppOfferBundle:AdditionalInfo',
                            'multiple' => true,
                            'expanded' => true,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\AdditionalInfoRepository $repo)use($id) {
                                return $repo->createQueryBuilder('a')
                                    ->join('a.categories', 'c')
                                    ->where('a.isPublish=1')
                                    ->andWhere('c.id=:id')
                                    ->setParameter('id', $id)
                                ;
                            },
                            'empty_value'=>'wybierz',
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))
            ->add('media', 'entity', array(
                            'class' => 'AppOfferBundle:Media',
                            'multiple'     => true,
                            'expanded' => true,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\MediaRepository $repo)use($id) {
                                return $repo->createQueryBuilder('m')
                                    ->join('m.categories', 'c')
                                    ->where('m.isPublish=1')
                                    ->andWhere('c.id=:id')
                                    ->setParameter('id', $id)
                                 ;
                            },
                            'empty_value'=>'wybierz',
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))
            ->add('neighborhood', 'entity', array(
                            'class' => 'AppOfferBundle:Neighborhood',
                            'multiple'     => true,
                            'expanded' => true,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\NeighborhoodRepository $repo)use($id) {
                                return $repo->createQueryBuilder('n')
                                    ->join('n.categories', 'c')
                                    ->where('n.isPublish=1')
                                    ->andWhere('c.id=:id')
                                    ->setParameter('id', $id)
                                   ;
                            },
                            'empty_value'=>'wybierz',
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))

            ;
    };

        $builder->get('locationIndexLike')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $query = $event->getData();

                if($query==''){
                  return;
                }

                $suggestedLocation = $this->searchManager->findClosestLocation($query);

                if($suggestedLocation){
                     $event->setData($suggestedLocation['name']);
                     return;
                }
                $event->setData($query);
            }
        );
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getCategory());
            }
        );

        $builder->get('category')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {

                $category = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $category);
            }
        );

    }
    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\OfferBundle\Model\Search',
            'csrf_protection'   => false,
        ));
    }

}
