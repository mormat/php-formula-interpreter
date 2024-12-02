<?php

use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;
use Mormat\FormulaInterpreter\Parser\ExpressionCleanerDecorator;


use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExpressionCleanerDecoratorTest extends TestCase {
    
    #[DataProvider('dataParse')]
    public function testParse($rawExpression, $cleanedExpression) {
        
        $decorated = $this->createMock(ParserInterface::class);
        $decorated->method('parse')
            ->willReturnCallback(function($expr) {
                return ['cleanedExpression' => $expr];
            });
        
        $parser = new ExpressionCleanerDecorator($decorated);
        
        $this->assertEquals(
            ['cleanedExpression' => $cleanedExpression],
            $parser->parse($rawExpression)
        );
        
    }
    
    public static function dataParse() {
        return [
            ['0', '0'],
            ['100', '100'],
            [' 100 ', '100'],
            ['(100)', '100'],
            [' (100) ', '100'],
            ['( 100 )', '100'],
            ['((100) + (100))', '(100) + (100)'],
            ['(100) + (100)', '(100) + (100)'],
            ['(equal(b,1)*150+equal(c,1)) * ( 1-0.05)', '(equal(b,1)*150+equal(c,1)) * ( 1-0.05)']
            // 
        ];
    }
    
    #[DataProvider('dataParseWithInvalidExpression')]
    public function testParseWithInvalidExpression($invalidExpression) {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $invalidExpression)
        );
        
        $decorated = $this->createMock(ParserInterface::class);
        $parser = new ExpressionCleanerDecorator($decorated);
        $parser->parse($invalidExpression);
    }
    
    public static function dataParseWithInvalidExpression() {
        return [
            [''],
            [' '],
            ['()'],
            [' () '],
            ['( )'],
            [' ( ) '],
        ];
    }
    
}
