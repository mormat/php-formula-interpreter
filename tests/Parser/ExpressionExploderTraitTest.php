<?php

use Mormat\FormulaInterpreter\Parser\ArrayParser;
use Mormat\FormulaInterpreter\Parser\ExpressionExploderTrait;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;


/**
 * Tests the spliting of expressions
 *
 * @author mormat
 */
class ExpressionExploderTraitTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @var ExpressionExploderTrait
     */
    protected $expressionExploder;
    
    public function setUp()
    {
        $this->expressionExploder = new ExpressionExploderTraitTest_ExpressionExploderUser();
    }
    
    /**
     * @dataProvider getExplodeExpressionWithCommaAsSerapatorData
     */
    public function testExplodeExpressionWithCommaAsSerapator($expression, $expected) {
        $actual = $this->expressionExploder->explodeExpression($expression, [',']);
        $this->assertEquals(
            $actual, 
            $expected,
            sprintf("actual value is %s", var_export($actual, true))
        );
    }
    
    public function getExplodeExpressionWithCommaAsSerapatorData() {
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
    
    /**
     * @dataProvider getExplodeExpressionWithOperatorsAsSerapatorData
     */
    public function testExplodeExpressionWithOperatorsAsSerapator($expression, $expected) {
        $operators = ['+', '-', '/', '*', ' in ', ' or '];
        $actual = $this->expressionExploder->explodeExpression($expression, $operators);
        $this->assertEquals(
            $actual, 
            $expected,
            sprintf("actual value is %s", var_export($actual, true))
        );
    }
    
    public function getExplodeExpressionWithOperatorsAsSerapatorData() {
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
                ["'a'", ' in ', 'foo', ' or ', "'b'", ' in ', 'bar']
            ),
            // missing closing parenthesis
            array(
                "((5) * 2 / (3 - 1)", 
                ["((5) * 2 / (3 - 1)"]
            ),
            // the operator 'in' between quotes must be ignored
            array(
                "'fun' in 'fun in fundamental'", 
                ["'fun'", ' in ', "'fun in fundamental'"]
            ),
            
            array(
                "cos(1 * 2) + (3)",
                ['cos(1 * 2) ', '+', ' (3)']
            )
        );
    }
    
    /**
     * to be used for testing class using this trait
     * 
     * @param string $expression
     * @param array  $options
     * @return array
     */
    public static function mockExplodeExpression($expression, array $validators, array $options = []) {
        if ($expression === '') {
            return [];
        }
        
        if ($validators == [',']) {
            $results = array_map(function($i) {
                return '= ' . $i;
            }, explode(',', $expression));

            return $results;
        }
        
        throw new \Exception("not implemented");
    }
    
}

class ExpressionExploderTraitTest_ExpressionExploderUser {
    use ExpressionExploderTrait;
}
