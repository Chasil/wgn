<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\OfficeBundle\Form\AdditionalServiceType;
use App\UserBundle\Form\AddressType;
use App\OfficeBundle\Entity\Office;
use Symfony\Component\Form\FormInterface;


/**
 * Class OfficeType
 *
 * @author wojciech przygoda
 */
class OfficeType extends AbstractType
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
            ->add('name')
            ->add('subdomain')
            ->add('importId')
            ->add('signature')
            ->add('description')
            ->add('type','choice', array(
                                    'choices'  => array(
                                    'nieruchomości'=>Office::TYPE_PROPERTIES,
                                    'kredyty'=>Office::TYPE_CREDIT,
                                    'inne usługi'=>Office::TYPE_PROPERTIES_OTHER,
                                        ),
                                    'choices_as_values' => true,
                                    'required'=>true))
            ->add('lat','hidden')
            ->add('lng','hidden')
            ->add('phone')
            ->add('mobile')
            ->add('addresses', 'collection', array(
                'entry_type'   => new AddressType(),
                'by_reference' => false,
                'allow_add' => false,
                'allow_delete' => false,
                'entry_options'  => array(
                    'required'  => false,
                )))
            ->add('fax')
            ->add('email')
            ->add('www')
            ->add('creditOfferUrl')
            ->add('developmentOfferUrl')
            ->add('additionalServices', 'collection', array(
                'entry_type'   => new AdditionalServiceType(),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options'  => array(
                    'required'  => false,
                )))
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
            'data_class' => 'App\OfficeBundle\Entity\Office',
            'validation_groups' => function(FormInterface $form) {
                    return array('registration');
             },
        ));
    }
}
