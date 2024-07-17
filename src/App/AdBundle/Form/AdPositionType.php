<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class AdPositionType
 *
 * @author wojciech przygoda
 */
class AdPositionType extends AbstractType
{
    /**
     * Build AdPositionType form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('uniqKey')
            ->add('description')
            ->add('mode', 'choice', array(
                    'choices'  => array('0' => 'losowo', '1' => 'w/g kolejności'),
                    'choices_as_values' => false,
                ))
            ->add('adsLimit')
            ->add('isPublish','checkbox',array('label'=>'Tak','required'=>false))
            ->add('isMobileSupported','checkbox',array('label'=>'Tak','required'=>false))
        ;
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AdBundle\Entity\AdPosition'
        ));
    }
}
