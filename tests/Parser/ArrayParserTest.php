<?php

use Mormat\FormulaInterpreter\Parser\ArrayParser;
use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the parsing of arrays
 *
 * @author mormat
 */
class ArrayParserTest extends TestCase {
    
    protected ArrayParser $parser;
    
    public function setUp(): void {
        
        $itemParser = $this->getMockBuilder(
            ParserInterface::class
        )->getMock();
        $itemParser
            ->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(array($this, 'mockItemParser')));
        
        $this->parser = new ArrayParserTest_ArrayParser($itemParser);
        
    }
    
    #[DataProvider('getParseIfExpressionisCorrectData')]
    public function testParseIfExpressionisCorrect($expression, $expected) {
        $expected['type'] = 'array';
        $this->assertEquals($this->parser->parse($expression), $expected);
    }
    
    public static function getParseIfExpressionisCorrectData() {
        return array(
            array("[]",  array('value' => [])),
            array("[1]", array('value' => ['item = 1'])),
            array("[2,3]", array('value' => ['item = 2', "item = 3"])),
        );
    }
    
    #[DataProvider('getParseIfExpressionisUncorrectData')]
    public function testParseIfExpressionisUncorrect($expression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $expression)
        );
        $this->parser->parse($expression);
    }
    
    public static function getParseIfExpressionisUncorrectData() {
        return array(
            array(""),
            array(" "),
            array(" foo "),
        );
    }
    
    public function mockItemParser($expression)
    {
        return 'item ' . $expression;
    }
    
}

class ArrayParserTest_ArrayParser extends ArrayParser
{
    function explodeExpression($expression, array $separators, array $options = [])
    {
        return ExpressionExploderTraitTest::mockExplodeExpression($expression, $separators, $options);
    }
}
