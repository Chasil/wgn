<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\AppBundle\Form\DataTransformer\ConfigToArrayTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class CustomHtmlConfigType
 *
 * @author wojciech przygoda
 */
class CustomLinkConfigType extends AbstractType
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
    public function __construct(ObjectManager $manager) {
    }
    /**
     * Build PlotType form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
                ->add('blockTitle','text',array('label'=>'Tytuł bloku'))
                ->add('class','text',array('label'=>'Klasa css'))
                ->add('url','text',array('label'=>'adres url'))
                ->add('nofollow',CheckboxType::class,array('label'=>'no follow'))
                ->add('blank',CheckboxType::class,array('label'=>'otwórz w nowym oknie'))
            ;
    }
}