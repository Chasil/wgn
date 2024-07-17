<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\AppBundle\Form\DataTransformer\ConfigToArrayTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ArticleListConfigType
 *
 * @author wojciech przygoda
 */
class ArticleListConfigType extends AbstractType
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
    public function __construct(ObjectManager $manager) {

        $this->manager = $manager;
        $repo = $this->manager->getRepository('AppArticleBundle:ArticleCategory');
        $this->categories = $repo->findToList(false);
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
                ->add('view','choice', array(
                                    'choices'  => array('Lista bez zdjęć'=>'articleList',
                                                        'Lista ze zdjęciami'=>'articleListWithImages',
                                                        'Lista głównym artykułem'=>'articleListWithMainArticle'),
                                    'choices_as_values' => true,
                                    'required'=>true,
                                    'label'=>'Widok listy'))
                ->add('class','text',array('label'=>'Klasa css'))
                ->add('limit','text',array('label'=>'Ilość wyświetlanych artykułów',
                                           'attr'=>array('data-validator'=>'required')))
                ->add('blockTitle','text',array('label'=>'Tytuł bloku'))
                ->add('idCategory','choice', array(
                                    'choices'  => $this->categories,
                                    'required'=>true,
                                    'label'=>'Kategoria Artykułów'))

            ;
    }
}