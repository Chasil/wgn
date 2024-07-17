<?php
/**
 * This file is part of the AppMenuBundle package.
 *
 */
namespace App\MenuBundle\Form;

use App\AppBundle\Component\UrlHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Persistence\ObjectManager;
use App\MenuBundle\Entity\MenuItem;
use App\MenuBundle\Entity\Menu;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Class MenuItemType
 *
 * @author wojciech przygoda
 */
class MenuItemType extends AbstractType
{
    /**
     *
     * @var Menu menu
     */
    protected $menu;

    /**
     *
     * @var ObjectManager database manager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param ObjectManager $em database manager
     * @param Menu $menu
     */
    public function __construct(ObjectManager $em, Menu $menu) {
        $this->menu = $menu;
        $this->em = $em;
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
            ->add('route','hidden')
            ->add('inNewWindow','checkbox',array('required'=>false,
                                                     'label'=>'otwórz w nowym oknie'))
            ->add('routeParameters','hidden')
            ->add('type','choice', array(
                                    'choices'  => array('Artykuł'=>  MenuItem::TYPE_ARTICLE,
                                                        'Kategoria Artykułów'=>  MenuItem::TYPE_CATEGORY,
                                                         'Link Zewnętrzny'=>  MenuItem::TYPE_EXTERNAL,
                                                         'Separator'=> MenuItem::TYPE_SEPARATOR,
                                                         'Strona statyczna'=> MenuItem::TYPE_ROUTE),
                                    'choices_as_values' => true,
                                    'required'=>true,
                                    'label'=>'Typ pozycji'))
            ->add('parent','entity', array('class'=>'App\MenuBundle\Entity\MenuItem',
                                       'required' => true,
                                       'property' => 'indentedName',
                                        'query_builder' => function($qb) {
                                        return $qb->createQueryBuilder('i')
                                                  ->join('i.menu','m')
                                                  ->where('m.uniqueKey = :key')
                                                  ->setParameter('key',  $this->menu->getUniqueKey())
                                                  ->orderBy('i.root', 'ASC')
                                                  ->addOrderBy('i.lft', 'ASC');
                                    },
        ))
;
    $builder->get('routeParameters')
            ->addModelTransformer(new CallbackTransformer(
                function ($params) {
                    return json_encode($params);
                },
                function ($params) {
                     return $params;
                }
            ));
    $builder->addEventListener(
            FormEvents::PRE_SET_DATA,array($this, 'onPreSetData')
        );
    $builder->addEventListener(
            FormEvents::PRE_SUBMIT,array($this, 'onPreSubmit')
        );
    $builder->addEventListener(
            FormEvents::POST_SET_DATA,array($this, 'onPostSetData')
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
        $menuItem = $event->getData();

        switch($menuItem->getType()){
            case MenuItem::TYPE_ARTICLE:

                $form->add('id','hidden',array('mapped'=>false,
                                                'attr'=>array('data-validator'=>'required')));
                $form->add('article','text',array('label'=>'wybierz artykuł',
                                                  'mapped'=>false,
                                                  'attr'=>array('data-validator'=>'required')));
            break;
            case MenuItem::TYPE_CATEGORY:

                $form->add('id','hidden',array('mapped'=>false,
                                                'attr'=>array('data-validator'=>'required')));
                $form->add('category','text',array('label'=>'wybierz kategorię',
                                                  'mapped'=>false,
                                                  'attr'=>array('data-validator'=>'required')));
            break;
            case MenuItem::TYPE_EXTERNAL:
                $form->add('url','text',array('mapped'=>false));
            break;
            case MenuItem::TYPE_ROUTE:
                $form->remove('route');
                $form->add('route','text',array('label'=>'nazwa trasy','attr'=>array('data-validator'=>'required')));
            break;
        }
    }
    /**
     * PostSetData Event
     *
     * @param FormEvent $event form event
     * @throws \Exception
     */
    public function onPostSetData(FormEvent $event){
        $form = $event->getForm();
        $menuItem = $event->getData();

        switch($menuItem->getType()){
            case MenuItem::TYPE_ARTICLE:
               $params = $menuItem->getRouteParameters();

               if(!isset($params['id'])){
                   return;
               }

               $article = $this->em->getRepository('AppArticleBundle:Article')
                               ->findOneById($params['id']);
               if(is_object($article)){
                   $form->get('article')->setData($article->getName());
                   $form->get('id')->setData($article->getId());
               }
            break;
            case MenuItem::TYPE_CATEGORY:
               $params = $menuItem->getRouteParameters();

               if(!isset($params['idCategory'])){
                   return;
               }
               $category = $this->em->getRepository('AppArticleBundle:ArticleCategory')
                               ->findOneById($params['idCategory']);
               if(is_object($category)){
                   $form->get('category')->setData($category->getName());
                   $form->get('id')->setData($category->getId());
               }
            break;
            case MenuItem::TYPE_EXTERNAL:
                $form->get('route')->setData('');
                $form->get('url')->setData($menuItem->getRoute());

            break;
        }
    }
    /**
     * PreSetSubmit Event
     *
     * @param FormEvent $event form event
     * @throws \Exception
     */
    public function onPreSubmit(FormEvent $event){

        $form = $event->getForm();
        $data = $event->getData();

        switch($data['type']){
            case MenuItem::TYPE_ARTICLE:
                $article = $this->em->getRepository('AppArticleBundle:Article')
                               ->findOneById($data['id']);
                $data['route'] = 'frontend_article_show';

                $name = UrlHelper::prepare($article->getName());
                $category = UrlHelper::prepare($article->getCategory()->getName());

                $data['routeParameters'] = array('id'=>$article->getId(),
                                                 'categoryName'=>$category,
                                                 'articleName'=>substr($name,0,100),
                    );
            break;
            case MenuItem::TYPE_CATEGORY:
                $category = $this->em->getRepository('AppArticleBundle:ArticleCategory')
                               ->findOneById($data['id']);
                $data['route'] = 'frontend_article_category_show';

                $name = UrlHelper::prepare($category->getName());

                $data['routeParameters'] = array('idCategory'=>$category->getId(),
                                                 'categoryName'=>substr($name,0,100),
                    );
            break;
            case MenuItem::TYPE_EXTERNAL:
                $data['route'] = $data['url'];
            break;
            case MenuItem::TYPE_SEPARATOR:
                $data['route'] = '#';
            break;
        }

        $event->setData($data);
    }
    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\MenuBundle\Entity\MenuItem'
        ));
    }
}