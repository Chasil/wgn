<?php
/**
 * This file is part of the AppNewsletterBundle package.
 *
 */
namespace App\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class MessageType
 *
 * @author wojciech przygoda
 */
class MessageType extends AbstractType
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
            ->add('subject')
            ->add('message','textarea')
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
            'data_class' => 'App\NewsletterBundle\Model\Message',
        ));
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'search';
    }
}
