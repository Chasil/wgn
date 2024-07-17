<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AdsListConfigType
 *
 * @author wojciech przygoda
 */
class AdsListConfigType extends AbstractType
{
    /**
     *
     * @var ObjectManager database manager
     */
    private $manager;

    /**
     *
     * @var array ad positions
     */
    private $positions;

    /**
     * Constructor
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager) {
        $this->manager = $manager;
        $repo = $this->manager->getRepository('AppAdBundle:AdPosition');
        $this->positions = $repo->findToList();
    }
    /**
     * Build PlotType form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
                ->add('idPosition','choice', array(
                                    'choices'  => $this->positions,
                                    'required'=>true,
                                    'label'=>'Kategoria Reklam'))
                ->add('view','choice', array(
                                    'choices'=> array('Normalny'=>'adsBox',
                                                        'Slider'=>'adsSlider'),
                                    'choices_as_values' => true,
                                    'required'=>true,
                                    'label'=>'Widok'))
                ->add('class','text',array('label'=>'Klasa css'))
                ->add('size','choice', array(
                                    'choices'=> array('cała'=>'b-col-12',
                                                      '1/2'=>'b-col-6',
                                                      '1/3'=>'b-col-4',
                                                      '1/4'=>'b-col-3'),
                                    'choices_as_values' => true,
                                    'required'=>true,
                                    'label'=>'szerokość'))
            ;
    }
}