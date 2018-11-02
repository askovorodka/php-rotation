<?php

namespace Common\Doctrine\Extensions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Lexer;

/**
 * Расширение для DQL, реализующее SQL функцию ISNULL
 *
 * Class IsNull
 *
 * @package Common\Doctrine\Extensions
 */
class IsNull extends FunctionNode
{
    /**
     * @var Literal
     */
    private $field;

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return string
     *
     * @throws \Doctrine\ORM\Query\AST\ASTException
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $fieldStr = $this->field->dispatch($sqlWalker);

        return "ISNULL($fieldStr)";
    }

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     *
     * @return void
     *
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
