<?php
/**
 * This file is part of the AppAppBundle package.
 *
 */
namespace App\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class BlockType
 *
 * @author wojciech przygoda
 */
class BlockType extends AbstractType
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
            $builder
                ->add('name','text')
                ->add('headerBgColor','text')
                ->add('headerFontColor','text')
                ->add('headerFontSize')
                ->add('headerFontIsBold','checkbox',array('label'=>'Czcionka nagłówka pogrubiona'))
                ->add('fontColor','text')
                ->add('fontSize')
                ->add('fontIsBold','checkbox',array('label'=>'Czcionka pogrubiona'))
                ->add('customStyle','textarea')
                ->add('place','entity', array('class'=>'App\AppBundle\Entity\BlockPlace',
                                       'required' => true,))
;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,array($this, 'onPreSetData')
        );
    }

    /**
     * PreSetData Event
     *
     * @param FormEvent $event form event
     * @throws \Exception
     */
    public function onPreSetData(FormEvent $event){
                $form = $event->getForm();

                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();
                $service = $data->getService();
                $parts = explode('_',$service);

                if(count($parts)!=3){
                    throw new \Exception('Invalid service name format '.$service);
                }

                $className = 'App\\'.ucfirst($parts[0]).'Bundle\\Form\\'
                             .ucfirst($parts[1]).ucfirst($parts[2]).'ConfigType';

                if(!class_exists($className)){
                    throw new \Exception('Config Form for service '.$service.' not found');
                }
                $configType = new $className($this->manager);
                $form->add('config',$configType);
    }


    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\AppBundle\Entity\BlockElement'
        ));
    }
}