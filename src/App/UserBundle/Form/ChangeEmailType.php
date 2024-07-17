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
 * Class ChangeEmailType
 *
 * @author wojciech przygoda
 */
class ChangeEmailType extends AbstractType
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
            ->add('email','repeated', array(
                  'type' => 'email',
                  'invalid_message' => 'Podałeś dwa różne adresy.',))
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
            'data_class' => 'App\UserBundle\Entity\User',
            'validation_groups' => function(FormInterface $form) {
                    $validationGroups = array('changeEmail');
                    return $validationGroups;
             },
        ));
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return 'frontend_bundle_profilebundle_user';
    }
}
