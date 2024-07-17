<?php

namespace App\OfferBundle\Form;

use App\OfferBundle\Entity\Category;
use App\OfferBundle\Entity\TransactionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\AppBundle\Form\DataTransformer\ConfigToArrayTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Persistence\ObjectManager;


class CurrentOffersConfigType extends AbstractType
{
    /**
     *
     * @var ObjectManager database manager
     */
    private $manager;

    /**
     *
     * @var array category names
     */
    private $categories;

    /**
     * Constructor
     *
     * @param ObjectManager $manager database manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category',EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label'=>'Kategoria Ofert',
                ])
            ->add('transactionType',EntityType::class, [
                    'class' => TransactionType::class,
                    'choice_label' => 'name',
                    'label'=>'Typ Transakcji',
                ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event)
        {
            $data = $event->getData();

            if(isset($data['category']))
            {
                $data['category'] = $this->manager->getReference(
                    Category::class,
                    $data['category']->getId()
                );
            }

            if(isset($data['transactionType']))
            {
                $data['transactionType'] = $this->manager->getReference(
                    TransactionType::class,
                    $data['transactionType']->getId()
                );
            }

            $event->setData($data);
        });
    }
}