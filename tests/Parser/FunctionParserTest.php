<?php

use Mormat\FormulaInterpreter\Parser\FunctionParser;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

/**
 * Tests the parsing of functions
 *
 * @author mormat
 */
class FunctionParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        $argumentParser = $this->getMockBuilder(
            ParserInterface::class
        )->getMock();
        $argumentParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockArgumentParser')));
        $this->parser = new FunctionParserTest_FunctionParser($argumentParser);
        
    }
    
    /**
     * @dataProvider getCorrectExpressions
     */
    public function testParseWithCorrecrExpression($expression, $infos) {
        
        $infos['type'] = 'function';
        
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getCorrectExpressions() {
        return array(
            array('pi()', array('name' => 'pi')),
            array(' pi( ) ', array('name' => 'pi')),
            array('do_this()', array('name' => 'do_this')),
            array('cos(0)', array('name' => 'cos', 'arguments' => ['arg = 0'])),
            array('sqrt(2)', array('name' => 'sqrt', 'arguments' => ['arg = 2'])),
            array('pow(2,3)', array('name' => 'pow', 'arguments' => ['arg = 2', 'arg = 3'])),
            array('sqrt(pi())', array('name' => 'sqrt', 'arguments' => ['arg = pi()'])),
            array('max(sqrt(pow(3)),2)', array('name' => 'max', 'arguments' => ['arg = sqrt(pow(3))', 'arg = 2'])),
        );
    }
    
    /**
     * @dataProvider getUncorrectExpressions
     */
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressions() {
        return array(
            array(' what ever '),
            array(' what_ever( '),
            array('what_ever ( )'),
        );
    }
    
    public function mockArgumentParser($expression) {
        return 'arg ' . $expression;
    }
}

class FunctionParserTest_FunctionParser extends FunctionParser
{
    function explodeExpression($expression, array $separators, array $options = [])
    {
        return ExpressionExploderTraitTest::mockExplodeExpression($expression, $separators, $options);
    }
}
