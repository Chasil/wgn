<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use App\SubscriptionBundle\Entity\Subscription;

/**
 * Class SearchSubscriptionsType
 *
 * @author wojciech przygoda
 */
class SearchSubscriptionsType extends AbstractType
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
            ->add('state','choice', array(
                                    'choices'  => array('Aktywne'=> Subscription::STATE_ACTIVE,
                                                         'Nieaktywne'=>  Subscription::STATE_UNACTIVE,),
                                    'choices_as_values' => true,
                                    'empty_value'=>'Wszystkie',
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
            'data_class' => 'App\SubscriptionBundle\Model\SearchSubscriptions',
        ));
    }

    /**
     *
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'search';
    }
}
