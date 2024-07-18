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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\UserBundle\Entity\User;
/**
 * Class OrderType
 *
 * @author wojciech przygoda
 */
class OrderType extends AbstractType
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
            ->add('login')
            ->add('contactPerson')
            ->add('nip')
            ->add('package','choice', array(
                                    'choices'  => array('50 Ogłoszeń ważny 90 dni - 16 zł netto (brutto z VAT 19,68)'=>'50',
                                        '100 Ogłoszeń ważny 90 dni - 30 zł netto (brutto z VAT 36,90)'=>'100',
                                        '250 Ogłoszeń ważny 90 dni - 40 zł netto (brutto z VAT 49,20)'=>'250',
                                        'powyżej 250 Ogłoszeń ważny 90 dni - 55 zł netto (brutto z VAT 67,65)'=>'-1',),
                                    'choices_as_values' => true,
                                    'expanded'=>true,
                                    'multiple'=>false,
                                    'required'=>true))
            ->add('agreeRegulations','checkbox',array('required'=>true,
                                                      'label'=>'* Zapoznałem/am się z treścią regulaminu www.wgn.pl i akceptuje jego zapisy.'))
            ->add('agreeProcessingData','checkbox',array('required'=>true,
                                                         'label'=>'* Wyrażam zgodę na przetwarzanie moich danych osobowych w celach niezbędnych do realizacji zakupionej usługi (zgoda jest konieczna do opublikowania ogłoszenia)'))
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
            'data_class' => 'App\UserBundle\Model\Order',
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
