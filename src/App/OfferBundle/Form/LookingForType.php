<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\OfferBundle\Form\DataTransformer\OfferToIntTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class LookingForType
 *
 * @author wojciech przygoda
 */
class LookingForType extends AbstractType
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
            ->add('message', 'textarea')
            ->add('transaction','choice', array(
                                    'choices'  => array(
                                        'kupno'=>'kupno',
                                        'sprzedaż'=>'sprzedaż',
                                        'wynajem'=>'wynajem',
                                        'najem'=>'najem'),
                                    'choices_as_values' => true,
                                    'required'=>true))

            ->add('category', 'entity', array(
                            'class' => 'AppOfferBundle:Category',
                            'required'=>true))
            ->add('country', 'entity', array(
                            'class' => 'AppOfferBundle:Country',
                            'required'=>true,
                            'empty_value'=>'wybierz'))
            ->add('province','choice', array(
                                    'choices'  => array(
                                        ''=>'',
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
            ->add('city','text')
            ->add('squereFrom','text')
            ->add('squereTo','text')
            ->add('roomsFrom','text')
            ->add('roomsTo','text')
            ->add('floorFrom','text')
            ->add('floorTo','text')
            ->add('priceFrom','text')
            ->add('priceTo','text')
            ->add('currency','choice', array(
                                    'choices'  => array(
                                        'PLN'=>'PLN',
                                        'EUR'=>'EUR',
                                        'USD'=>'USD'),
                                    'choices_as_values' => true,
                                    'required'=>true))
            ->add('email','text')
            ->add('name','text')
            ->add('phone','text')
            ->add('agreeRegulations','checkbox',array('required'=>true,
                                                      'mapped'=>false,
                                                      'label'=>'* Zapoznałem/am się z treścią regulaminu www.wgn.pl i akceptuje jego zapisy.'))
        ;
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
            ;
        };
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
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $category = $event->getForm()->getData();
                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
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
            'data_class' => 'App\OfferBundle\Model\LookingFor',
        ));
    }
}
