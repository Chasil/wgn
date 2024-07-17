<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FlatType
 *
 * @author wojciech przygoda
 */
class FlatType extends OfferAbstractType
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
            ->add('yearOfBuilding','text',array('required'=>false))
            ->add('floor','choice', array(
                                    'choices'  => array_combine(range(-1,30),range(-1,30)),
                                    'choices_as_values' => true,
                                    'empty_value'=>'wybierz',
                                    'required'=>false))
            ->add('abilityToWath','text',array('required'=>false))
            ->add('availableFrom','date',array('widget'=>'single_text','required'=>false,'html5' => false,))
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
            ->add('exhibitionWindows', 'entity', array(
                    'class' => 'AppOfferBundle:ExhibitionWindows',
                    'required'=>false,
                    'empty_value'=>'wybierz',
                    'query_builder' => function (\App\OfferBundle\Entity\ExhibitionWindowsRepository $repo) {
                        return $repo->createQueryBuilder('ew')
                            ->where('ew.isPublish=1')
                            ->orderBy('ew.ordering','ASC');
                    },
            ))
            ->add('monthPayments')
            ->add('monthPaymentsCurrency', 'entity', array(
                            'class' => 'AppOfferBundle:Currency',
                            'required'=>true))
            ->add('property', 'entity', array(
                            'class' => 'AppOfferBundle:Property',
                            'required'=>false,
                            'empty_value'=>'wybierz',
                            'query_builder' => function (\App\OfferBundle\Entity\PropertyRepository $repo) {
                                return $repo->createQueryBuilder('p')
                                    ->where('p.isPublish=1')
                                    ->orderBy('p.ordering','ASC');
                            },
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
