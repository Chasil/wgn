<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class BackOfficeSearchType
 *
 * @author wojciech przygoda
 */
class BackOfficeSearchType extends AbstractType
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
            ->add('signature')
            ->add('name')
            ->add('category', 'entity', array(
                            'class' => 'AppOfferBundle:Category',
                            'required'=>true,
                            'empty_value'=>'wszystkie',))
            ->add('transactionType', 'entity', array(
                            'class' => 'AppOfferBundle:TransactionType',
                            'required'=>true,
                            'empty_value'=>'wszystkie',))
            ->add('isPublish','choice', array(
                                    'choices'  => array('Aktywne'=>'1',
                                                         'Nieaktywne'=>'0'),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wszystkie',
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
            'data_class' => 'App\OfferBundle\Model\BackOfficeSearch',
        ));
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName()
    {
        return 'search';
    }
}
