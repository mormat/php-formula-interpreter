<?php

use Mormat\FormulaInterpreter\Parser\NumericParser;
use Mormat\FormulaInterpreter\Parser\ParserException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parsing of numeric values
 *
 * @author mormat
 */
class NumericParserTest extends TestCase {
    
    protected NumericParser $parser;
    
    public function setUp(): void {
        $this->parser = new NumericParser();
    }
    
    #[DataProvider('getIntegerValue')]
    public function testParseInteger($expression, $infos) {
        $infos['type'] = 'numeric';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public static function getIntegerValue() {
        return array(
            array('2', array('value' => 2)),
            array('2.4', array('value' => 2.4)),
            array(' 2.4 ', array('value' => 2.4)),
        );
    }
    
    #[DataProvider('getUncorrectExpressionData')]
    public function testParseUncorrectExpression($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        $this->parser->parse($expression);
    }
    
    public static function getUncorrectExpressionData() {
        return array(
            array('mlksdf'),
            array('MLKmlm'),
            array(' MLKmlm '),
            array(' some_function( '),
            array('2.23.23')
        );
    }
    
}
