<?php

namespace Tastd\Bundle\CoreBundle\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class DistanceFunction
 *
 * @package Tastd\Bundle\CoreBundle\Doctrine\Functions
 */
class DistanceFunction extends FunctionNode
{
    protected $fromLat;
    protected $fromLng;
    protected $toLat;
    protected $toLng;

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->fromLat = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->fromLng = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->toLat = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);

        $this->toLng = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $earthDiameterInKM = 1.609344 * 3956 * 2;

        $sql = '('.$earthDiameterInKM.' * ASIN(SQRT(POWER(' .
            'SIN(('.$this->fromLat->dispatch($sqlWalker).' - ABS('.$this->toLat->dispatch($sqlWalker).')) * PI() / 180 / 2), 2) + ' .
            'COS('.$this->fromLat->dispatch($sqlWalker).' * PI() / 180) * COS(ABS('.$this->toLat->dispatch($sqlWalker).') * PI() / 180) * ' .
            'POWER(SIN(('.$this->fromLng->dispatch($sqlWalker).' - '.$this->toLng->dispatch($sqlWalker).') * PI() / 180 / 2), 2) ' .
            ')))';

        return $sql;
    }
}