<?php

use Mormat\FormulaInterpreter\Parser\NumericParser;
use Mormat\FormulaInterpreter\Parser\ParserException;

/**
 * Description of NumericParserTest
 *
 * @author mormat
 */
class NumericParserTest extends PHPUnit_Framework_TestCase {
    
    public function setUp() {
        $this->parser = new NumericParser();
    }
    
    /**
     * @dataProvider getIntegerValue
     */
    public function testParseInteger($expression, $infos) {
        $infos['type'] = 'numeric';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public function getIntegerValue() {
        return array(
            array('2', array('value' => 2)),
            array('2.4', array('value' => 2.4)),
            array(' 2.4 ', array('value' => 2.4)),
        );
    }
    
    /**
     * @dataProvider getUncorrectExpressionData
     */
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        $this->parser->parse($expression);
    }
    
    public function getUncorrectExpressionData() {
        return array(
            array('mlksdf'),
            array('MLKmlm'),
            array(' MLKmlm '),
            array(' some_function( '),
            array('2.23.23')
        );
    }
    
}
