<?php
/**
 * This file is part of the AppSettingsBundle package.
 *
 */
namespace App\SettingsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CompanyAddressType
 *
 * @author wojciech przygoda
 */
class CompanyAddressType extends AbstractType
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
            ->add('companyCountry','text', ['required' => true])
            ->add('companyCity','text', ['required' => true])
            ->add('companyZipCode','text', ['required' => true])
            ->add('companyStreet','text', ['required' => true])
            ->add('companyPhone','text', ['required' => true])
            ->add('companyEmail','text', ['required' => true])
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
            'data_class' => 'App\SettingsBundle\Entity\Settings'
        ));
    }
}
