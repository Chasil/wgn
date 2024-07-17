<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RecommendType
 *
 * @author wojciech przygoda
 */
class RecommendType extends AbstractType
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
            ->add('email','text')
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
            'data_class' => 'App\ArticleBundle\Model\Recommend',
        ));
    }
}
