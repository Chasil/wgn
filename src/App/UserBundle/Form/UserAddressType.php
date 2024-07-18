<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserAddressType
 *
 * @author wojciech przygoda
 */
class UserAddressType extends AbstractType
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
            ->add('city')
            ->add('street')
            ->add('zipCode')
            ->add('phone')
            ->add('phone2')
            ->add('name')
            ->add('email')
            ->add('openFrom','choice', array(
                                    'choices'  => array('7:00'=>'7:00',
                                                        '7:30'=>'7:30',
                                                        '8:00'=>'8:00',
                                                        '8:30'=>'8:30',
                                                        '9:00'=>'9:00',
                                                        '9:30'=>'9:30',
                                                        '10:00'=>'10:00',
                                                        '10:30'=>'10:30',
                                                        '11:00'=>'11:00',
                                                        '11:30'=>'11:30',
                                                        '12:00'=>'12:00',
                                                        '12:30'=>'12:30',
                                                        '13:00'=>'13:00',
                                                        '13:30'=>'13:30',
                                                        '14:00'=>'14:00',
                                                        '14:30'=>'14:30',
                                                        '15:00'=>'15:00',
                                                        '15:30'=>'15:30',
                                                        '16:00'=>'16:00',
                                                        '16:30'=>'16:30',
                                                        '17:00'=>'17:00',
                                                        '17:30'=>'17:30',
                                                        '18:00'=>'18:00',
                                                        '18:30'=>'18:30',
                                                        '19:00'=>'19:00',
                                                        '19:30'=>'19:30',
                                                        '20:00'=>'20:00'),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wybierz',
                                    'required'=>true))
            ->add('openTo','choice', array(
                                    'choices'  => array('7:00'=>'7:00',
                                                        '7:30'=>'7:30',
                                                        '8:00'=>'8:00',
                                                        '8:30'=>'8:30',
                                                        '9:00'=>'9:00',
                                                        '9:30'=>'9:30',
                                                        '10:00'=>'10:00',
                                                        '10:30'=>'10:30',
                                                        '11:00'=>'11:00',
                                                        '11:30'=>'11:30',
                                                        '12:00'=>'12:00',
                                                        '12:30'=>'12:30',
                                                        '13:00'=>'13:00',
                                                        '13:30'=>'13:30',
                                                        '14:00'=>'14:00',
                                                        '14:30'=>'14:30',
                                                        '15:00'=>'15:00',
                                                        '15:30'=>'15:30',
                                                        '16:00'=>'16:00',
                                                        '16:30'=>'16:30',
                                                        '17:00'=>'17:00',
                                                        '17:30'=>'17:30',
                                                        '18:00'=>'18:00',
                                                        '18:30'=>'18:30',
                                                        '19:00'=>'19:00',
                                                        '19:30'=>'19:30',
                                                        '20:00'=>'20:00'),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wybierz',
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
            'data_class' => 'App\UserBundle\Entity\Address'
        ));
    }
}
