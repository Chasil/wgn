<?php
/**
 * This file is part of the AppOfficeBundle package.
 *
 */
namespace App\OfficeBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\OfficeBundle\Form\DataTransformer\OfficeToIntTransformer;
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
     * @var ObjectManager datablase manager
     */
    private $manager;
    /**
     * Constructor
     *
     * @param ObjectManager $manager datablase manager
     *
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
        $officeTransformer = new OfficeToIntTransformer($this->manager);
        $builder
            ->add($builder->create('office', 'hidden')->addModelTransformer($officeTransformer))
            ->add('message', 'textarea')
            ->add('email','text')
            ->add('phone','text')
            ->add('subject','text')
            ->add('name','text')
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
            'data_class' => 'App\OfficeBundle\Model\Contact',
        ));
    }
}
