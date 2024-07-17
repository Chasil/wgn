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
 * Class SearchClientsType
 *
 * @author wojciech przygoda
 */
class SearchClientsType extends AbstractType
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
            ->add('company')
            ->add('role','choice', array(
                                    'choices'  => array('Użytkownik'=>'ROLE_USER',
                                                         'Abonament'=>'ROLE_BUISNESS'
                                        ),
                                    'choices_as_values' => true,
                                    'empty_value'=>'Wszyscy',
                                    'required'=>true))
            ->add('isCompany','choice', array(
                                    'choices'  => array('Firma'=>'1',
                                                         'Osoba Prywatna'=>'0'),
                                    'choices_as_values' => true,
                                    'empty_value'=>'Wszyscy',
                                    'required'=>true))
            ->add('nip')
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
            'data_class' => 'App\UserBundle\Model\SearchClients',
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
