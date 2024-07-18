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
 * Class MetaTagsType
 *
 * @author wojciech przygoda
 */
class MetaTagsType extends AbstractType
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
            ->add('pageTitle','text', ['required' => true])
            ->add('h1', 'text', ['required' => true])
            ->add('metaKeywords','text', ['required' => true])
            ->add('metaDescription','text', ['required' => true])
            ->add('pageDescription','textarea', ['required' => false])
            ->add('pageArchiveDescription','textarea', ['required' => false])
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
