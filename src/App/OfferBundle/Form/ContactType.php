<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\OfferBundle\Form\DataTransformer\OfferToIntTransformer;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ContactType
 *
 * @author wojciech przygoda
 */
class ContactType extends AbstractType
{
    /**
     *
     * @var ObjectManager database manager
     */
    private $manager;

    /**
     * Constructor
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $offerTransformer = new OfferToIntTransformer($this->manager);
        $builder
            ->add($builder->create('offer', 'hidden')->addModelTransformer($offerTransformer))
            ->add('message', 'textarea')
            ->add('email','text')
            ->add('phone','text')
            ->add('agree','checkbox',array('label_attr' => ['class' => 'checkbox-inline'],
                                           'label'=>'Wyrażam zgodę na jednorazowy kontakt ogłoszeniodawcy ze mną w celu przedstawienia w/w oferty.'))
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\OfferBundle\Model\Contact',
        ));
    }
}
