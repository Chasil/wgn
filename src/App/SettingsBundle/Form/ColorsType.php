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
 * Class ColorsType
 *
 * @author wojciech przygoda
 */
class ColorsType extends AbstractType
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
            ->add('menuBgColor','text', ['required' => true])
            ->add('h1TextColor', 'text', ['required' => true])
            ->add('h1Color', 'text', ['required' => true])
            ->add('menuTextColor','text', ['required' => true])
            ->add('searchBgColor','text', ['required' => true])
            ->add('searchTextColor','text', ['required' => true])
            ->add('searchButtonBgColor','text', ['required' => true])
            ->add('searchButtonHoverBgColor','text', ['required' => true])
            ->add('searchButtonTextColor','text', ['required' => true])
            ->add('footerBgColor','text', ['required' => true])
            ->add('footerTextColor','text', ['required' => true])
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
