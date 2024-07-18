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
 * Class SearchGarageType
 *
 * @author wojciech przygoda
 */
class SearchGarageType extends AbstractType
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
            ->add('market', 'entity', array(
                            'class' => 'AppOfferBundle:Market',
                            'required'=>true,
                            'empty_value'=>'dowolny',
                            'query_builder' => function (\App\OfferBundle\Entity\MarketRepository $repo) {
                                return $repo->createQueryBuilder('m')
                                    ->where('m.isPublish=1');
                            },))
            ->add('yearOfBuildingFrom','text',array('required'=>false))
            ->add('yearOfBuildingTo','text',array('required'=>false))
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
                                    ->setParameter('key', Category::TYPE_GARAGE);
                            },
                            'empty_value'=>'dowolny',
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
