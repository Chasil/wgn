<?php
/**
 * This file is part of the AppAdBundle package.
 *
 */
namespace App\AdBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\AdBundle\Form\DataTransformer\OfferToSignatureTransformer;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AdType
 *
 * @author wojciech przygoda
 */
class AdType extends AbstractType
{
    /**
     *
     * @var ObjectManager database manager
     */
    private $manager;

    /**
     *
     * @var bool is office position
     */
    private $isOfficePosition;

    /**
     *
     * Constructor
     *
     * @param ObjectManager $manager
     * @param type $isOfficePosition
     */
    public function __construct(ObjectManager $manager,$isOfficePosition=false)
    {
        $this->isOfficePosition  = $isOfficePosition;
        $this->manager = $manager;
    }
    /**
     * Build PlotType form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $offerTransformer = new OfferToSignatureTransformer($this->manager);
        $builder
            ->add('name')
            ->add('code')
            ->add('adFile')
            ->add('adMobileFile')
            ->add('clickLimit')
            ->add('displayLimit')
            ->add('weight')
            ->add('height')
            ->add('startDate', 'datetime',array('date_widget'=>'single_text','time_widget'=>'single_text','required'=>false,'html5' => false,))
            ->add('endDate', 'datetime',array('date_widget'=>'single_text','time_widget'=>'single_text','required'=>false,'html5' => false,))
            ->add('url','text',array('required'=>false))
            ->add('position', ChoiceType::class, array(
                'choices' => $this->getPositions(),
                'choices_as_values' => true,
                'choice_attr' => function($choiceValue, $key, $value){
                    return ['class' => 'position_'.strtolower($key)];
                },
                'preferred_choices' => $this->actualPosition(),
            ))
            ->add('isPublish','checkbox',array('label'=>'Tak','required'=>false))
        ;

        if(!$this->isOfficePosition){
            $builder->add($builder->create('offer', 'text',array('required'=>false))
                                  ->addModelTransformer($offerTransformer));
        }
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AdBundle\Entity\Ad'
        ));
    }

    private function actualPosition(){
        if(isset($_GET['idPosition'])){
            $repo = $this->manager->getRepository('AppAdBundle:AdPosition');
            return $repo->createQueryBuilder('p')
                ->where('p.isOfficePosition = :isOfficePosition AND p.id = :idPosition')
                ->setParameter('isOfficePosition', $this->isOfficePosition)
                ->setParameter('idPosition', $_GET['idPosition'])
                ->getQuery()->getResult();
        }
        return [];
    }

    private function getPositions(){
        $repo = $this->manager->getRepository('AppAdBundle:AdPosition');
        $query = $repo->createQueryBuilder('p')
            ->where('p.isOfficePosition = :isOfficePosition')
            ->setParameter('isOfficePosition', $this->isOfficePosition)
            ->orderBy('p.id','ASC')->getQuery()->getResult();

        $results = [];
        foreach($query as $item){
            $results[$item->getName()] = $item;
        }

        return $results;
    }
}
