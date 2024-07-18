<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ArticleCategoryType
 *
 * @author wojciech przygoda
 */
class ArticleCategoryType extends AbstractType
{
    /**
     * Build PlotType form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('disallowRobots','checkbox',['label'=>'Nie indeksuj w wyszukiwarkach'])
            ->add('followAttribute','choice', array(
                'choices'  => array(
                    'brak'=>'',
                    'dofollow'=>'dofollow',
                    'noffolow'=>'noffolow',
                ),
                'choices_as_values' => true,
                'required'=>true))
            ->add('metaTitle')
            ->add('metaKeywords')
            ->add('metaDesc')
        ;
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ArticleBundle\Entity\ArticleCategory'
        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'app_articlebundle_articlecategory';
    }
}
