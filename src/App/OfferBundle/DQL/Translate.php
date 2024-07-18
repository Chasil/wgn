<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\DQL;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * Class Translate
 *
 * @author wojciech przygoda
 */
class Translate extends FunctionNode {

    /**
     *
     * @var SimpleStringExpression simple string expression
     */
    private $string = null;

    /**
     * Parse
     * @param \Doctrine\ORM\Query\Parser $parser parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->string = $parser->StringPrimary(); //SimpleStringExpression();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

    }

    /**
     * Get sql
     *
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker sql walker
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'translate(' . $this->string->dispatch($sqlWalker) .')';
    }
}







