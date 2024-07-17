<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GarageType
 *
 * @author wojciech przygoda
 */
class GarageType extends OfferAbstractType
{
    /**
     * Build form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transactionTypeId = $this->transactionType->getId();
        $required = false;
        if($transactionTypeId ==2 || $transactionTypeId==4){

            $marketOptions =  array(
                'class' => 'AppOfferBundle:Market',
                'required'=>false,
                'empty_value'=>'dowolny',
                'query_builder' => function (\App\OfferBundle\Entity\MarketRepository $repo) {
                    return $repo->createQueryBuilder('m')
                        ;
                },
            );
        }else {
            $marketOptions = array(
                'class' => 'AppOfferBundle:Market',
                'required'=>true,
                'empty_value'=>'wybierz',
                'query_builder' => function (\App\OfferBundle\Entity\MarketRepository $repo) {
                    return $repo->createQueryBuilder('m')
                        ;
                },
            );
        }

        $builder
            ->add('market','entity', $marketOptions)
            ->add('availableFrom','date',array('widget'=>'single_text','required'=>false,'html5' => false,))
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
