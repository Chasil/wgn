<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdditionalServiceType
 *
 * @author wojciech przygoda
 */
class AdditionalServiceType extends AbstractType
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
            ->add('name','text',array('label'=>'Nazwa'))
            ->add('type','entity',array('required'=>true,'class'=>'App\OfficeBundle\Entity\AdditionalServiceType'))
            ->add('url','text',array('label'=>'Adres www usługi'))
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
            'data_class' => 'App\OfficeBundle\Entity\AdditionalService'
        ));
    }
}
