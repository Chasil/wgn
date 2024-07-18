<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HouseType
 *
 * @author wojciech przygoda
 */
class HouseType extends OfferAbstractType
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
        $required = true;

        if($transactionTypeId ==2 || $transactionTypeId==4){
            $required = false;
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
            ->add('windows', 'entity', array(
                    'class' => 'AppOfferBundle:Windows',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\WindowsRepository $repo) {
                        return $repo->createQueryBuilder('w')
                            ->where('w.isPublish=1')
                            ->orderBy('w.ordering','ASC');
                    },
            ))
            ->add('roofCover', 'entity', array(
                    'class' => 'AppOfferBundle:RoofCover',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\RoofCoverRepository $repo) {
                        return $repo->createQueryBuilder('rf')
                            ->where('rf.isPublish=1')
                            ->orderBy('rf.ordering','ASC');
                    },
            ))
            ->add('roof', 'entity', array(
                    'class' => 'AppOfferBundle:Roof',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\RoofRepository $repo) {
                        return $repo->createQueryBuilder('r')
                            ->where('r.isPublish=1')
                            ->orderBy('r.ordering','ASC');
                    },
            ))
            ->add('localization', 'entity', array(
                    'class' => 'AppOfferBundle:Localization',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\LocalizationRepository $repo) {
                        return $repo->createQueryBuilder('l')
                            ->where('l.isPublish=1')
                            ->orderBy('l.ordering','ASC');
                    },
            ))
            ->add('accessRoad', 'entity', array(
                    'class' => 'AppOfferBundle:AccessRoad',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\AccessRoadRepository $repo) {
                        return $repo->createQueryBuilder('ar')
                            ->where('ar.isPublish=1')
                            ->orderBy('ar.ordering','ASC');
                    },
            ))
            ->add('rooms','choice', array(
                                    'choices'  => array_combine(range(1,10),range(1,10)),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wybierz',
                                    'required'=>$required))
            ->add('storeys','choice', array(
                                    'choices'  => array_combine(range(0,30),range(0,30)),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wybierz',
                                    'required'=>false))
            ->add('technicalCondition', 'entity', array(
                            'class' => 'AppOfferBundle:TechnicalCondition',
                            'required'=>false,
                            'empty_value'=>'wybierz',
                            'query_builder' => function (\App\OfferBundle\Entity\TechnicalConditionRepository $repo) {
                                return $repo->createQueryBuilder('tc')
                                    ->where('tc.isPublish=1')
                                    ->orderBy('tc.ordering','ASC');
                            },
            ))
            ->add('yearOfBuilding','text',array('required'=>false))
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
