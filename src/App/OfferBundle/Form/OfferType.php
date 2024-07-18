<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class OfferAbstractType
 *
 * @author wojciech przygoda
 */
class OfferType extends OfferAbstractType
{
    /**
     * Build PlotType form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('properties',new PropertiesType())
            ->add('typeProperties',$this->loadType())
            ->add('country', 'entity', array(
                            'class' => 'AppOfferBundle:Country',
                            'required'=>true,
                            'empty_value'=>'wybierz'))
            ->add('district','text',array('required'=>false))
            ->add('city','text',array('required'=>true))
            ->add('street','text',array('required'=>true))
            ->add('lat','hidden')
            ->add('lng','hidden')
        ;
        $formModifier = function (FormInterface $form, $country = null) {
            $isPoland = (is_object($country) && $country->getIsoCode()=='pl') ? true: false;
            if($isPoland){
                $form->add('region','choice', array(
                                        'choices'  => array(
                                            'dolnośląskie'=>'dolnośląskie',
                                            'kujawsko-pomorskie'=>'kujawsko-pomorskie',
                                            'lubelskie'=>'lubelskie',
                                            'lubuskie'=>'lubuskie',
                                            'łódzkie'=>'łódzkie',
                                            'małopolskie'=>'małopolskie',
                                            'mazowieckie'=>'mazowieckie',
                                            'opolskie'=>'opolskie',
                                            'podkarpackie'=>'podkarpackie',
                                            'podlaskie'=>'podlaskie',
                                            'pomorskie'=>'pomorskie',
                                            'śląskie'=>'śląskie',
                                            'świętokrzyskie'=>'świętokrzyskie',
                                            'warmińsko-mazurskie'=>'warmińsko-mazurskie',
                                            'wielkopolskie'=>'wielkopolskie',
                                            'zachodniopomorskie'=>'zachodniopomorskie',
                                            ),
                                        'choices_as_values' => true,
                                        'required'=>true))
                ;

            }else {
                $form->add('region','text',array('required'=>true));
            }
    };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $formModifier($event->getForm(), $data->getCountry());
            }
        );

        $builder->get('country')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $country = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $country);
            }
        );
    }
    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\OfferBundle\Entity\Offer',
            'validation_groups' => function(FormInterface $form) {
                    $validationGroups = array('add');
                    $validationGroups[] = $this->category->getUniqueKey();
                    return $validationGroups;
             },
        ));
    }

    /**
     * Load form type for selected category
     *
     * @return OfferAbstractType
     */
    private function loadType(){
        $categoryKey = $this->category->getUniqueKey();
        $class = 'App\\OfferBundle\\Form\\' . ucfirst($categoryKey). 'Type';

        if(!class_exists($class)) {
            throw new \Exception('Form '. $class .' not found.');
        }

        return new $class($this->category,  $this->transactionType);
    }
}
