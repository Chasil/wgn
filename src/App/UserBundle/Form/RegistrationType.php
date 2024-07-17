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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
/**
 * Class RegistrationType
 *
 * @author wojciech przygoda
 */
class RegistrationType extends AbstractType
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
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => true,
            ))
            ->add('agreement_1',CheckboxType::class,[
                'label'=>'Akceptuję Regulamin, Politykę Prywatności i Politykę Ochrony Danych Osobowych',
                'required'=>true,
                'mapped'=>false,
            ])
            ->add('agreement_2',CheckboxType::class,[
                'label'=>'Wyrażam zgodę na przetwarzanie moich danych osobowych podanych przy tworzeniu konta w serwisie, na potrzeby marketingu bezpośredniego towarów i usług przez WGN Nieruchomości Sp. z o.o. z siedzibą we Wrocławiu.',
                'required'=>true,
                'mapped'=>false,
            ])
            ->add('agreement_3',CheckboxType::class,[
                'label'=>'Wyrażam zgodę na używanie przez WGN Nieruchomości Sp. z o.o. z siedzibą we Wrocławiu w kontaktach ze mną, telekomunikacyjnych urządzeń końcowych w celu prowadzenia marketingu bezpośredniego.',
                'required'=>true,
                'mapped'=>false,
            ])
            ->add('agreement_4',CheckboxType::class,[
                'label'=>'Wyrażam zgodę na otrzymywanie informacji handlowych drogą elektroniczną dot. produktów i usług oferowanych przez WGN Nieruchomości Sp. z o.o. z siedzibą we Wrocławiu.',
                'required'=>true,
                'mapped'=>false,
            ])
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
                    $validationGroups = array('registration');
                    return $validationGroups;
             },
        ));
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getName()
    {
        return 'buisness_user';
    }
}
