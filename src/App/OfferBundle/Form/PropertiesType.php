<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PropertiesType
 *
 * @author wojciech przygoda
 */
class PropertiesType extends AbstractType
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
            ->add('name','text',array('required'=>true))
            ->add('tmpId','hidden',array('required'=>false))
            ->add('description','textarea',array('required'=>true))
            ->add('video','text',array('required'=>false))
            ->add('price','text',array('required'=>false))
            ->add('pricem2','text',array('required'=>false))
            ->add('squere','text',array('required'=>false))
            ->add('squerePlot','text',array('required'=>false))
            ->add('currency', 'entity', array(
                            'class' => 'AppOfferBundle:Currency',
                            'required'=>true))
            ->add('isExclusive','checkbox', array(
                                'label'    => 'Oferta na wyłączność',
                                'required' => false,
                ))
            ->add('isDirect','checkbox', array(
                                'label'    => 'Oferta bezpośrednia - 0 prowizji',
                                'required' => false,))
            ->add('contactPerson','text',array('required'=>false))
            ->add('email','text',array('required'=>true))
            ->add('phone','text',array('required'=>false))
        ;
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
