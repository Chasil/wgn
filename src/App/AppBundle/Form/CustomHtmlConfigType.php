<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
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
class CustomHtmlConfigType extends AbstractType
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
                ->add('class','text',array('label'=>'Klasa css'))
                ->add('html','textarea',array('label'=>'Kod Html'))
            ;
    }
}