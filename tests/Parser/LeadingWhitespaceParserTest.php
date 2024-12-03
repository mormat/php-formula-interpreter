<?php

use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;
use Mormat\FormulaInterpreter\Parser\LeadingWhitespaceParser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LeadingWhitespaceParserTest extends TestCase {
    
    protected LeadingWhitespaceParser $parser;
    
    protected function setUp(): void {
        $childParser = $this->createMock(ParserInterface::class);
        $childParser->method('parse')->willReturnCallback(function($expr) {
            if ($expr === '') {
                throw new ParserException($expr);
            }
            return ['parsed' => $expr];
        });
        
        $this->parser = new LeadingWhitespaceParser($childParser);
    }
    
    #[DataProvider('dataParse')]
    public function testParse($expression, $expectedParsing) {
        
        $this->assertEquals(
            ['parsed' => $expectedParsing],
            $this->parser->parse($expression)
        );
                
    }
    
    public static function dataParse() {
        return [
            [' 0', '0'],
            ['0 ', '0'],
            [' 0 ', '0'],
        ];
    }
    
    #[DataProvider('dataParseWithInvalidExpression')]
    public function testParseWithInvalidExpression($invalidExpression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $invalidExpression)
        );
        
        $this->parser->parse($invalidExpression);
    }
    
    public static function dataParseWithInvalidExpression() {
        return [
            ['whatever'],
            ['']
        ];
    }
        
}
