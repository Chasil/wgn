<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
/**
 * Class SearchUsersType
 *
 * @author wojciech przygoda
 */
class SearchUsersType extends AbstractType
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
            ->add('username')
            ->add('email')
            ->add('name')
            ->add('role','choice', array(
                                    'choices'  => array('Super Administrator'=>'ROLE_SUPER_ADMIN',
                                                         'Administrator'=>'ROLE_ADMIN',
                                                         'Dziennikarz'=>'ROLE_WRITER',
                                                         'Redaktor'=>'ROLE_AUTHOR',
                                                         'Menadżer'=>'ROLE_MANAGER'),
                                    'choices_as_values' => true,
                                    'empty_value'=>'Wszyscy',
                                    'required'=>true))
            ->add('dateFrom')
            ->add('dateTo')
            ->setMethod('GET')
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
            'data_class' => 'App\UserBundle\Model\SearchUsers',
        ));
    }

    /**
     * Get type
     * 
     * @return string
     */
    public function getName()
    {
        return 'search';
    }
}
