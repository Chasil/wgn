<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use App\PaymentBundle\Entity\Payment;
/**
 * Class SearchPaymentsType
 *
 * @author wojciech przygoda
 */
class SearchPaymentsType extends AbstractType
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
            ->add('paymentMethod','choice', array(
                                    'choices'  => array('Płatność elektorniczna'=>Payment::TYPE_CARD,
                                                         'SMS'=>Payment::TYPE_SMS,
                                                         'Abonament'=>Payment::TYPE_SUBSCRIPTION),
                                    'choices_as_values' => true,
                                    'empty_value'=>'Wszystkie',
                                    'required'=>true))
            ->add('state','choice', array(
                                    'choices'  => array('Rozpoczęta'=>Payment::STATE_STARTED,
                                                         'Zakończona'=>Payment::STATE_SUCCESS,
                                                         'Odrzucona'=>Payment::STATE_FAILD),
                                    'choices_as_values' => true,
                                    'empty_value'=>'Wszystkie',
                                    'required'=>true))
            ->add('signature')
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
            'data_class' => 'App\PaymentBundle\Model\SearchPayments',
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
