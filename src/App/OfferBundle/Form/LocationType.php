<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class LocationType
 *
 * @author wojciech przygoda
 */
class LocationType extends AbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'entity', array(
                            'class' => 'AppOfferBundle:Country',
                            'required'=>true,
                            'empty_value'=>'wybierz'))
            ->add('region','text',array('required'=>true))
            ->add('district','text',array('required'=>false))
            ->add('city','text',array('required'=>true))
            ->add('street','text',array('required'=>true))
            ->add('lat','hidden')
            ->add('lng','hidden')
        ;
        $formModifier = function (FormInterface $form, $country = null) {
            $isPoland = (is_object($country) && $country->getIsoCode()=='pl') ? true: false;


            if($isPoland){
                //$form->remove('region');
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
                $form->remove('region');
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
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $country = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $country);
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
            'inherit_data' => true
        ));
    }
}
