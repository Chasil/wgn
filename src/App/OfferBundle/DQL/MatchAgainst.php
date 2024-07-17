<?php
/**
 * This file is part of the AppOfferBundle package.
 *
 */
namespace App\OfferBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
/**
 * Class MatchAgainst
 *
 * @author wojciech przygoda
 */
class MatchAgainst extends FunctionNode {

    /**
     *
     * @var array columns
     */
    public $columns = array();

    /**
     *
     * @var InputParameter input parameter
     */
    public $needle;

    /**
     * Parse
     * @param \Doctrine\ORM\Query\Parser $parser parser
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        do {
            $this->columns[] = $parser->StateFieldPathExpression();
            $parser->match(Lexer::T_COMMA);
        }
        while (!$parser->getLexer()->isNextToken(Lexer::T_INPUT_PARAMETER));

        $this->needle = $parser->InputParameter();

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
        $haystack = null;
        $first = true;
        foreach ($this->columns as $column) {
            $first ? $first = false : $haystack .= ', ';
            $haystack .= $column->dispatch($sqlWalker);
        }
        return "MATCH(" .
            $haystack .
            ") AGAINST (" .
            $this->needle->dispatch($sqlWalker) .
            " IN BOOLEAN MODE )";
    }
}
