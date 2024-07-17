<?php
/**
 * This file is part of the AppSubscriptionBundle package.
 *
 */
namespace App\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SubscriptionType
 *
 * @author wojciech przygoda
 */
class SubscriptionType extends AbstractType
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
            ->add('package','choice', array(
                                    'choices'  => array('50 Ogłoszeń ważny 90 dni - 16 zł netto (brutto z VAT 19,68)'=>'50',
                                        '100 Ogłoszeń ważny 90 dni - 30 zł netto (brutto z VAT 36,90)'=>'100',
                                        '250 Ogłoszeń ważny 90 dni - 40 zł netto (brutto z VAT 49,20)'=>'250',
                                        'powyżej 250 Ogłoszeń ważny 90 dni - 55 zł netto (brutto z VAT 67,65)'=>'-1',),
                                    'choices_as_values' => true,
                                    'mapped'=>false,
                                    'required'=>true))
            ->add('user', 'entity', array(
                    'class' => 'AppUserBundle:User',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\UserBundle\Entity\UserRepository $repo) {
                        return $repo->createQueryBuilder('u')
                            ->where('u.roles LIKE :user')
                            ->orWhere('u.roles LIKE :buisness')
                            ->orderBy('u.username','ASC')
                            ->setParameter('user','%ROLE_USER%')
                            ->setParameter('buisness','%ROLE_BUISNESS%');
                    },
            ))
        ;
        $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                array($this, 'onPostSubmit')
        );
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\SubscriptionBundle\Entity\Subscription'
        ));
    }

    /**
     * PostSubmit event
     * @param FormEvent $event form event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $subscription = $event->getData();
        $form = $event->getForm();

        $package = $form->get('package')->getData();
        switch($package){
            case '50':
                $subscription->setNumberOfAvailable(50)
                             ->setNetPrice(16)
                             ->setNetPrice(23);
            break;
            case '100':
                $subscription->setNumberOfAvailable(100)
                             ->setNetPrice(30)
                             ->setNetPrice(23);
            break;
            case '250':
                $subscription->setNumberOfAvailable(250)
                             ->setNetPrice(40)
                             ->setNetPrice(23);
            break;
            case '-1':
                $subscription->setNumberOfAvailable(-1)
                             ->setNetPrice(55)
                             ->setNetPrice(23);
            break;
        }

        $now = new \DateTime();
        $subscription->setExpireDate($now->modify('+90 days'));

        $event->setData($subscription);
    }
}
