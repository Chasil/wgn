<?php
/**
 * This file is part of the AppPaymentBundle package.
 *
 */
namespace App\PaymentBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractType;
use App\PaymentBundle\Entity\Payment;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class PaymentType
 *
 * @author wojciech przygoda
 */
class PaymentType extends AbstractType
{

    /**
     *
     * @var bool can pay via sms
     */
    protected $canPaySms = true;

    /**
     * Constructor
     *
     * @param bool $canPaySms can pay via sms
     */
    public function __construct($canPaySms=true) {
        $this->canPaySms = $canPaySms;
    }
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publication','checkbox',array('required'=>true,'label'=> 'Publikacja ogłoszenia na okres 90 dni na WGN.pl'
            ))
            ->add('promo','checkbox',array('label'=>'Wyróżnienie oferty na 14 dni'))
            ->add('paymentMethod','choice', array(
                                    'choices'=> array('Przelew / Karta'=>Payment::TYPE_CARD,
                                                      'SMS'=>Payment::TYPE_SMS),
                                    'choices_as_values' => true,
                                    'multiple'=>false,
                                    'expanded'=>true,
                                    'required'=>true))
            ->add('smsCode')
            ->add('agreeRegulations','checkbox',array('required'=>true,
                                                      'label'=>'* Zapoznałem/am się z treścią regulaminu www.wgn.pl i akceptuje jego zapisy.'))
            ->add('agreeProcessingData','checkbox',array('required'=>true,
                                                         'label'=>'* Wyrażam zgodę na przetwarzanie moich danych osobowych w celach niezbędnych do realizacji zakupionej usługi (zgoda jest konieczna do opublikowania ogłoszenia)'))
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')
            ->add('address')
            ->add('city')
            ->add('zipCode')
            ->add('country', 'entity', array(
                            'class' => 'AppOfferBundle:Country',
                            'required'=>true))
			->add('legalPersonality', 'choice', array(
							'choices'=>array(	'Osoba fizyczna*'=>Payment::TYPE_PERSON,
												'Firma*'=>Payment::TYPE_COMPANY),
							'choices_as_values'=>true,
							'multiple'=>false,
							'expanded'=>true,
							'required'=>true))
			->add('name')
			->add('NIP')
			->add('VAT', 'choice', array(
							'choices'=>array('TAK'=>true, 'NIE'=>false),
							'choices_as_values'=>true,
							'multiple'=>false,
							'expanded'=>true,
							'required'=>true))
         ;
        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
        );
    }
    /**
     * PreSetData Event
     * 
     * @param FormEvent $event form event
     */
    public function onPreSetData(FormEvent $event){
        $form = $event->getForm();

        if(!$this->canPaySms){
            $form->remove('paymentMethod')
                 ->remove('smsCode')
                 ->add('paymentMethod','choice', array(
                                    'choices'=> array('Przelew / Karta'=>Payment::TYPE_CARD),
                                    'choices_as_values' => true,
                                    'multiple'=>false,
                                    'expanded'=>true,
                                    'required'=>true)) ;
        }
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\PaymentBundle\Entity\Payment',
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if (Payment::TYPE_CARD == $data->getPaymentMethod()) {
                    return array(Payment::TYPE_CARD );
                }

                return array(Payment::TYPE_SMS );
            },
        ));
    }
}
