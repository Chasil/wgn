<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlotType
 *
 * @author wojciech przygoda
 */
class PlotType extends OfferAbstractType
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
            ->add('dimensions','text',array('required'=>false))
            ->add('hasFence','choice', array(
                                    'choices'=> array('tak'=>1,'nie'=>0),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wybierz',
                                    'required'=>false))
            ->add('legalStatus', 'entity', array(
                    'class' => 'AppOfferBundle:LegalStatus',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\LegalStatusRepository $repo) {
                        return $repo->createQueryBuilder('ls')
                            ->where('ls.isPublish=1')
                            ->orderBy('ls.ordering','ASC');
                    },
            ))
            ->add('media', 'entity', array(
                            'class' => 'AppOfferBundle:Media',
                            'multiple'     => true,
                            'expanded' => true,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\MediaRepository $repo) {
                                return $repo->createQueryBuilder('m')
                                    ->join('m.categories', 'c')
                                    ->where('m.isPublish=1')
                                    ->andWhere('c.uniqueKey=:key')
                                    ->setParameter('key',  $this->category->getUniqueKey());
                            },
                            'empty_value'=>'wybierz',
                            'label_attr' => ['class' => 'checkbox-inline'],
            ))
            ->add('neighborhood', 'entity', array(
                            'class' => 'AppOfferBundle:Neighborhood',
                            'multiple'     => true,
                            'expanded' => true,
                            'required'=>false,
                            'query_builder' => function (\App\OfferBundle\Entity\NeighborhoodRepository $repo) {
                                return $repo->createQueryBuilder('n')
                                    ->join('n.categories', 'c')
                                    ->where('n.isPublish=1')
                                    ->andWhere('c.uniqueKey=:key')
                                    ->setParameter('key',  $this->category->getUniqueKey());
                            },
                            'empty_value'=>'wybierz',
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
