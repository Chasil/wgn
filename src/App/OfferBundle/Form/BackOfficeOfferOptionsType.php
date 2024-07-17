<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

/**
 * Class BackOfficeOfferOptionsType
 *
 * @author wojciech przygoda
 */
class BackOfficeOfferOptionsType extends AbstractType
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
            ->add('days','choice', array(
                                    'choices'  => array('90 dni'=>'90',
                                                        '60 dni'=>'60',
                                                        '30 dni'=>'30'),
                                    'choices_as_values' => true,
                                    'required'=>true))
            ->add('isPromo','checkbox',array('required'=>false,'label'=>'tak'))
            ->add('promoDays','choice', array(
                                    'choices'  => array('14 dni'=>'14',
                                                        '7 dni'=>'7',
                                                        '21 dni'=>'21',
                                                        '30 dni'=>'30'),
                                    'choices_as_values' => true,
                                    'required'=>true))
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
            'data_class' => 'App\OfferBundle\Entity\Offer',
        ));
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return 'offer';
    }
}
