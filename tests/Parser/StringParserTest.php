<?php

use Mormat\FormulaInterpreter\Parser\StringParser;
use Mormat\FormulaInterpreter\Parser\ParserException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parsing of strings
 *
 * @author mormat
 */
class StringParserTest extends TestCase {
    
    protected StringParser $parser;
    
    public function setUp(): void {
        $this->parser = new StringParser();
    }
    
    #[DataProvider('getStringValues')]
    public function testParseString($expression, $infos) {
        $infos['type'] = 'string';
        $this->assertEquals($this->parser->parse($expression), $infos);
    }
    
    public static function getStringValues() {
        return array(
            array("'foo'",     array('value' => 'foo')),
            array("'bar'",     array('value' => 'bar')),
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
            array("2"),
            array("foo"),
            array(" foo "),
            array("'foo'bar'"),
            array("''foobar'"),
            array(" ''foobar'"),
        );
    }
    
}
