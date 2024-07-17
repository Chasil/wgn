<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\OfferBundle\Entity\Category;

/**
 * Class SearchLocalType
 *
 * @author wojciech przygoda
 */
class SearchLocalType extends AbstractType
{

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pricem2From','text',array('required'=>false))
            ->add('pricem2To','text',array('required'=>false))
            ->add('floor','choice', array(
                                    'choices'  => array_combine(range(-1,30),range(-1,30)),
                                    'choices_as_values' => true,
                                    'empty_value'=>'dowolne',
                                    'required'=>false))
            ->add('yearOfBuildingFrom','text',array('required'=>false))
            ->add('yearOfBuildingTo','text',array('required'=>false))
            ->add('market', 'entity', array(
                            'class' => 'AppOfferBundle:Market',
                            'required'=>true,
                            'empty_value'=>'wybierz',
                            'query_builder' => function (\App\OfferBundle\Entity\MarketRepository $repo) {
                                return $repo->createQueryBuilder('m')
                                    ->where('m.isPublish=1');
                            },))
            ->add('type', 'entity', array(
                            'class' => 'AppOfferBundle:Type',
                            'multiple'     => false,
                            'expanded' => false,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\TypeRepository $repo) {
                                return $repo->createQueryBuilder('t')
                                    ->join('t.category', 'c')
                                    ->where('t.isPublish=1')
                                    ->andWhere('c.uniqueKey=:key')
                                    ->orderBy('t.ordering','DESC')
                                    ->setParameter('key', Category::TYPE_LOCAL);
                            },
                            'empty_value'=>'dowolny',
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))
            ->add('additionalInfo', 'entity', array(
                            'class' => 'AppOfferBundle:AdditionalInfo',
                            'multiple'     => true,
                            'expanded' => true,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\AdditionalInfoRepository $repo) {
                                return $repo->createQueryBuilder('a')
                                    ->join('a.categories', 'c')
                                    ->where('a.isPublish=1')
                                    ->andWhere('c.uniqueKey=:key')
                                    ->setParameter('key', Category::TYPE_LOCAL);
                            },
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))
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
            'inherit_data' => true
        ));
    }
}
