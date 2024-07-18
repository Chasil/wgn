<?php
/**
 * This file is part of the AppUserBundle package.
 *
 */
namespace App\UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\UserBundle\Form\DataTransformer\UserToIntTransformer;
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
     * @param ObjectManager $manager database manager
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
        $userTransformer = new UserToIntTransformer($this->manager);
        $builder
            ->add($builder->create('user', 'hidden')->addModelTransformer($userTransformer))
            ->add('message', 'textarea')
            ->add('subject','text')
            ->add('name','text')
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
            'data_class' => 'App\UserBundle\Model\Contact',
        ));
    }
}
