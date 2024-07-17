<?php
/**
 * Created by PhpStorm.
 * User: KRZYSZTOF
 * Date: 2018-07-09
 * Time: 16:46
 */

namespace App\SettingsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyImageType extends AbstractType
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
			->add('file',FileType::class, [
				'attr' => array(
					'accept' => 'image/*'
				),
			] )
			->add('is_enabled',CheckboxType::class ,[
                'label'=>'Tak'
            ])
			->add( 'description', TextareaType::class )
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
			'data_class' => 'App\SettingsBundle\Entity\MyImage'
		));
	}
}
