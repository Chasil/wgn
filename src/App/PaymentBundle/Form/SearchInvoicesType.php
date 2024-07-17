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
use App\PaymentBundle\Entity\Invoice;

/**
 * Class SearchInvoicesType
 *
 * @author wojciech przygoda
 */
class SearchInvoicesType extends AbstractType
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
            ->add('nip')
            ->add('type','choice', array(
                                    'choices'=>array(Invoice::TYPE_PROMO=>Invoice::TYPE_PROMO,
                                        Invoice::TYPE_PUBLICATION=>Invoice::TYPE_PUBLICATION,
                                        Invoice::TYPE_SUBSCRIPTION=>Invoice::TYPE_SUBSCRIPTION,),
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
            'data_class' => 'App\PaymentBundle\Model\SearchInvoices',
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
