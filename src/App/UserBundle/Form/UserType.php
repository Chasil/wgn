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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\UserBundle\Entity\User;

/**
 * Class UserType
 *
 * @author wojciech przygoda
 */
class UserType extends AbstractType
{
    /**
     *
     * @var string user type
     */
    protected $type;
    /**
     *
     * @var UserManager user manager
     */
    protected $userManager;
    /**
     * Constructor
     * 
     * @param string $type user type
     * @param type $userManager user manager
     * @throws \Exception
     */
    public function __construct($type,$userManager) {
        $this->type = $type;
        $this->userManager = $userManager;

        $types = array(User::TYPE_AGENTS,User::TYPE_CLIENTS,User::TYPE_USERS,User::TYPE_OFFICE_MANAGER);

        if(!in_array($type,$types)){
            throw new \Exception('invalid user type');
        }
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
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('file')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => true,
            ))
        ;

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
        );
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
                    $validationGroups = array('registration',$this->type);
                    return $validationGroups;
             },
        ));
    }
    /**
     * OnPreSetData event
     * @param FormEvent $event event
     */
    public function onPreSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        switch($this->type){
            case User::TYPE_AGENTS:
                $form->add('role','choice', array(
                                    'choices'  => array('Agent'=>'ROLE_AGENT',),
                                    'choices_as_values' => true,
                                    'required'=>true))
                     ->add('office', 'entity', array(
                            'class' => 'AppOfficeBundle:Office',
                            'query_builder' => function (\App\OfficeBundle\Entity\OfficeRepository $repo) {

                                $qb = $repo->createQueryBuilder('o')
                                    ->where('o.isPublish=1')
                                    ->orderBy('o.name','ASC')
                                ;
                                $user = $this->userManager->getCurrentLogged();

                                if($user->isOfficeManager()){
                                    $qb->andWhere('o.id=:id')
                                       ->setParameter('id',$user->getOffice()->getId())
                                    ;
                                }

                                return $qb;
                            },
                            'required'=>true,
                            'empty_value'=>'wybierz'))
                      ->add('phone')
                      ->add('position')
                      ->add('mobile')
                      ->add('importId')
                      ->add('licence');
            break;
            case User::TYPE_CLIENTS:
                $form->add('role','choice', array(
                                    'choices'  => array('Użytkownik'=>'ROLE_USER',
                                                        'Abonament'=>'ROLE_BUISNESS',
                                                        ),
                                    'choices_as_values' => true,
                                    'required'=>true))
                    ->add('addresses','collection',array('entry_type' => new AddressType()))
                    ->add('companyData',new CompanyDataType());
            break;
            case User::TYPE_USERS:
                $form->add('role','choice', array(
                                    'choices'  => array(
                                                         'Administrator'=>'ROLE_ADMIN',
                                                         'Dziennikarz'=>'ROLE_WRITER',
                                                         'Redaktor'=>'ROLE_AUTHOR',
                                                         'Menadżer'=>'ROLE_MANAGER',
                                                         ),
                                    'choices_as_values' => true,
                                    'required'=>true));
            break;
            case User::TYPE_OFFICE_MANAGER:
                $form->remove('file');
                $form->add('office', 'entity', array(
                            'class' => 'AppOfficeBundle:Office',
                            'query_builder' => function (\App\OfficeBundle\Entity\OfficeRepository $repo) {
                                return $repo->createQueryBuilder('o')
                                    ->where('o.isPublish=1')
                                    ->orderBy('o.name','ASC')
                                ;
                            },
                            'required'=>true,
                            'empty_value'=>'wybierz'));
                $form->add('role','choice', array(
                                    'choices'  => array(
                                                         'Menadżer Biura WGN'=>'ROLE_OFFICE',
                                                         ),
                                    'choices_as_values' => true,
                                    'required'=>true));
            break;
        }
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'frontend_bundle_profilebundle_user';
    }
}
