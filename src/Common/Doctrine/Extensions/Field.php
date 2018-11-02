<?php

namespace Common\Doctrine\Extensions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Literal;

/**
 * Расширения, добавляющее возможность использования ORDER BY FIELD-синтаксиса
 *
 * @author Jeremy Hicks <jeremy.hicks@gmail.com>
 */
class Field extends FunctionNode
{
    /** @var Literal */
    private $field;
    /** @var array */
    private $values = [];

    /**
     * @inheritdoc
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        // Do the field.
        $this->field = $parser->ArithmeticPrimary();
        // Add the strings to the values array. FIELD must
        // be used with at least 1 string not including the field.
        $lexer = $parser->getLexer();
        while (count($this->values) < 1 ||
            $lexer->lookahead['type'] != Lexer::T_CLOSE_PARENTHESIS) {
            $parser->match(Lexer::T_COMMA);
            $this->values[] = $parser->ArithmeticPrimary();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @inheritdoc
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        $query = 'FIELD(';
        $query .= $this->field->dispatch($sqlWalker);
        $query .= ', ';
        for ($i = 0; $i < count($this->values); $i++) {
            if ($i > 0) {
                $query .= ', ';
            }
            $query .= $this->values[$i]->dispatch($sqlWalker);
        }
        $query .= ')';
        return $query;
    }
}
