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
use App\OfferBundle\Entity\TransactionType;

/**
 * Class OfferAbstractType
 *
 * @author wojciech przygoda
 */
class OfferAbstractType extends AbstractType
{

    /**
     *
     * @var Category offer cateogry
     */
    protected $category;

    /**
     *
     * @var TransactionType offer transaction type
     */
    protected $transactionType;
    /**
     * Constructor
     *
     * @param Category $category offer category
     * @param TransactionType $transactionType offer transaction type
     */
    public function __construct(Category $category, TransactionType $transactionType) {
        $this->category = $category;
        $this->transactionType = $transactionType;
    }
}
