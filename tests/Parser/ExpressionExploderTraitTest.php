<?php

namespace Mormat\FormulaInterpreter\Tests;

use Mormat\FormulaInterpreter\Parser\ArrayParser;
use Mormat\FormulaInterpreter\Parser\ExpressionExploderTrait;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the splitting of expressions
 */
class ExpressionExploderTraitTest extends TestCase
{
    /**
     * @var ExpressionExploderTrait
     */
    protected $expressionExploder;
    
    public function setUp(): void
    {
        $this->expressionExploder = new class() {
            use ExpressionExploderTrait;
        };
    }
    
    #[DataProvider('getExplodeExpressionWithCommaAsSeparatorData')]
    public function testExplodeExpressionWithCommaAsSeparator($expression, $expected)
    {
        $actual = $this->expressionExploder->explodeExpression($expression, [',']);
        $this->assertEquals(
            $actual,
            $expected,
            sprintf("actual value is %s", var_export($actual, true))
        );
    }
    
    public static function getExplodeExpressionWithCommaAsSeparatorData()
    {
        return array(
            
            array(
                " foo ",
                [" foo "]
            ),
            
            // beware when filtering values because 0 will be removed in PHP !!!
            array(
                "0",
                ["0"]
            ),
            
            array(
                "foo, bar",
                ["foo", ",", " bar"]
            ),
            
            array(
                " [foo, bar] ",
                [" [foo, bar] "]
            ),
            
            array(
                " func(2, 4) ",
                [" func(2, 4) "]
            ),
            
            array(
                " '2, 4' ",
                [" '2, 4' "]
            ),
            
            array(
                "'foo,bar', 'foo'",
                ["'foo,bar'", ',', " 'foo'"]
            )
            
        );
    }
    
    #[DataProvider('getExplodeExpressionWithOperatorsAsSeparatorData')]
    public function testExplodeExpressionWithOperatorsAsSeparator($expression, $expected)
    {
        $operators = ['+', '-', '/', '*', 'in', 'or'];
        $actual = $this->expressionExploder->explodeExpression($expression, $operators);
        $this->assertEquals(
            $actual,
            $expected,
            sprintf("actual value is %s", var_export($actual, true))
        );
    }
    
    public static function getExplodeExpressionWithOperatorsAsSeparatorData()
    {
        return array(
            
            array(
                '2 + 3 - 4 + 5',
                ['2 ', '+', ' 3 ', '-', " 4 ", '+', ' 5']
            ),
            array(
                '2 * 3 + 4',
                ['2 ', '*', ' 3 ', '+', " 4"]
            ),
            array(
                '2 * (3 + 4)',
                ['2 ', '*', ' (3 + 4)']
            ),
            
            array(
                "'a' in foo or 'b' in bar",
                ["'a' ", 'in',  ' foo ', 'or', " 'b' ", 'in', ' bar']
            ),
            
            array(
                "cos(1 * 2) + (3)",
                ['cos(1 * 2) ', '+', ' (3)']
            ),
            
            // missing closing parenthesis
            array(
                "((5) * 2 / (3 - 1)",
                ["((5) * 2 / (3 - 1)"]
            ),
            
            // the operator 'in' between quotes must be ignored
            array(
                "'fun' in 'fun in fundamental'",
                ["'fun' ", 'in', " 'fun in fundamental'"]
            ),
            
            // the 'in' operator is ignored if previous or next character is a letter, digit or underscore
            array(
                'ainzAinZ0in9_in_ + 2',
                ['ainzAinZ0in9_in_ ', '+', ' 2']
            ),
            
            // because we are checking previous and next character,
            // make sure that what don't get an offset error
            // when the expression starts and ends with 'in' keyword
            array(
                'in in',
                ['in', ' ', 'in']
            ),
            
        );
    }
    
    /**
     * to be used for testing class using this trait
     * @param string $expression
     * @param array  $options
     * @return array
     */
    public static function mockExplodeExpression(
        $expression,
        array $validators,
        array $options = []
    ) {
        if ($expression === '') {
            return [];
        }
        
        if ($validators == [',']) {
            $results = array_map(function ($i) {
                return '= ' . $i;
            }, explode(',', $expression));

            return $results;
        }
        
        throw new \Exception("not implemented");
    }
}
