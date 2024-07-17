<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode,
    Doctrine\ORM\Query\Lexer;

/**
 * Class Power
 *
 * @author wojciech przygoda
 */
class Power extends FunctionNode
{
    /**
     *
     * @var SimpleArithmeticExpression Simple arithmetic expression
     */
    public $arithmeticExpression;
    public $secondArithmeticExpression;


    /**
     * Get sql
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker sql walker
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'POWER(' . $sqlWalker->walkSimpleArithmeticExpression(
                $this->arithmeticExpression
        ) . ',' . $sqlWalker->walkSimpleArithmeticExpression(
                $this->secondArithmeticExpression
            ) .

        ')';
    }

    /**
     * Parse
     * @param \Doctrine\ORM\Query\Parser $parser parser
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->arithmeticExpression = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->secondArithmeticExpression = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
