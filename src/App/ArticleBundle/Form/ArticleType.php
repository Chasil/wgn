<?php
/**
 * This file is part of the AppArticeBundle package.
 *
 */
namespace App\ArticleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\ArticleBundle\Form\DataTransformer\TagsTransformer;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ArticleType
 *
 * @author wojciech przygoda
 */
class ArticleType extends AbstractType
{
    /**
     *
     * @var ObjectManager database manager
     */
    protected $manager;
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
        $tagsTransformer = new TagsTransformer($this->manager);

        $builder
            ->add('name')
            ->add('slug')
            ->add('metaTitle')
            ->add('disallowRobots','checkbox',['label'=>'Nie indeksuj w wyszukiwarkach'])
            ->add('followAttribute','choice', array(
                'choices'  => array(
                    'brak'=>'',
                    'dofollow'=>'dofollow',
                    'noffolow'=>'noffolow',
                ),
                'choices_as_values' => true,
                'required'=>true))
            ->add('category','entity', array(
                        'class'=>'App\ArticleBundle\Entity\ArticleCategory',
                        'required' => true,
                        'query_builder' => function (\App\ArticleBundle\Entity\ArticleCategoryRepository $repo) {
                                return $repo->createQueryBuilder('c')
                                    ->where('c.isDelete!=1')
                                    ->orderBy('c.name','ASC');
                        },
            ))
            ->add('metaDesc')
            ->add('metaKeywords')
            ->add('intro')
            ->add('content')
            ->add('isUrl','checkbox',array('label'=>'Dodaj jako link','required'=>false))
            ->add('url')
            ->add('source')
            ->add('file')
            ->add($builder->create('tags', 'text')->addModelTransformer($tagsTransformer))
            ->add('isPublish','checkbox',array('label'=>'Tak','required'=>false))
            ->add('publishDate', 'datetime',array('date_widget'=>'single_text',
                                                  'time_widget'=>'single_text',
                                                  'required'=>false,
                                                  'html5' => false,))
            ->add('createDate', 'datetime',array('date_widget'=>'single_text',
                                                  'time_widget'=>'single_text',
                                                  'required'=>false,
                                                  'html5' => false,))
        ;
    }

    /**
     * Configure form options
     *
     * @param OptionsResolver $resolver options resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\ArticleBundle\Entity\Article'
        ));
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'app_articlebundle_article';
    }
}
