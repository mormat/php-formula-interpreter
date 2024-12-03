<?php

use Mormat\FormulaInterpreter\Parser\ParserException;
use Mormat\FormulaInterpreter\Parser\ParserInterface;
use Mormat\FormulaInterpreter\Parser\OperationParser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OperationParserTest extends TestCase {
    
    const BAD_EXPRESSION = '#@?§%&!';
    
    protected OperationParser $parser;
    
    protected function setUp(): void {
        $childParser = $this->createMock(ParserInterface::class);
        $childParser->method('parse')->willReturnCallback(
            function($expression) {
                if ($expression === self::BAD_EXPRESSION) {
                    throw new ParserException($expression);
                }
                return ['parsed' => $expression];
            }
        );
        
        $this->parser = new OperationParser($childParser);
    }
    
    #[DataProvider('dataParseWithValidExpression')]
    public function testParseWithValidExpression(
        $expression, 
        $expectedLeft,
        $expectedOperator,
        $expectedRight,
    ) {
        
        $this->assertEquals(
            array(
                'type'     => 'operation',
                'left'     => ['parsed' =>  $expectedLeft],
                'operator' => $expectedOperator,
                'right'    => ['parsed' => $expectedRight],
            ),
            $this->parser->parse($expression)
        );
        
    }
    
    public static function dataParseWithValidExpression() {
        return array(
            
            ['2+2', '2', '+', '2'],
            ['3+2', '3', '+', '2'],
            ['2-2', '2', '-', '2'],
            ['2*2', '2', '*', '2'],
            ['2/2', '2', '/', '2'],
            ['(1+2)+3', '(1+2)', '+', '3'],
            
            // multiplication priority
            ['1+2*3', '1', '+', '2*3'],
            ['1-2*3', '1', '-', '2*3'],
            ['1*2+3', '1*2', '+', '3'],
            ['1*2-3', '1*2', '-', '3'],
            ['(1+2)*3', '(1+2)', '*', '3'],
            
            // division priority
            ['1+2/3', '1', '+', '2/3'],
            ['1-2/3', '1', '-', '2/3'],
            ['1/2+3', '1/2', '+', '3'],
            ['1/2-3', '1/2', '-', '3'],
            ['(1-2)/3', '(1-2)', '/', '3'],
            
            // 'in' operator
            ['1 in [1, 2]', '1', 'in', '[1, 2]'],
            ['[1]in[2]', '[1]', 'in', '[2]'],
            ['(1)in(2)', '(1)', 'in', '(2)'],
            
            // priority for 'in' operator
            ['1+2 in [3,4]', '1+2', 'in', '[3,4]'],
            ['2*2 in [4,5]', '2*2', 'in', '[4,5]'],
            
            // 'lower than' operator
            ['2<3', '2', '<', '3'],
            ['2+1<3', '2+1', '<', '3'],
            ['1<2-3', '1', '<', '2-3'],
            
            // 'greater than' operator
            ['2>3', '2', '>', '3'],
            ['3-1>2', '3-1','>','2'],
            ['3>2+1', '3', '>', '2+1'],
            
            // 'equal' operator
            ['2=3', '2', '=', '3'],
            ['3+1=3', '3+1', '=', '3'],
            ['3=3+1', '3', '=', '3+1'],
            
            // 'lower or equal' operator
            ['2<=3','2', '<=', '3'],
            ['1+2<=3', '1+2', '<=', '3'],
            ['1<=2-3', '1', '<=', '2-3'],
            
            // 'greater than' operator
            ['2>=3', '2', '>=', '3'],
            ['1>=2+3', '1', '>=', '2+3'],
            ['1+2>=3','1+2', '>=', '3'],
            
            // ignore parenthesis in string
            [ "'(2'='3)'", "'(2'", "=", "'3)'"],
            
            // ignore operators in array
            [ '2*[1 + 1]', '2', '*', '[1 + 1]']
        );
    }
    
    #[DataProvider('dataParseWithInvalidExpression')]
    public function testParseWithInvalidExpression($expression, $unparsable) {
        
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to parse expression %s', $unparsable)
        );
        
        $this->parser->parse($expression);
        
    }
    
    public static function dataParseWithInvalidExpression() {
        return array(
            [ 'whatever',  'whatever' ],
            [ sprintf('%s+2', self::BAD_EXPRESSION), self::BAD_EXPRESSION ],
            [ sprintf('2+%s', self::BAD_EXPRESSION), self::BAD_EXPRESSION ],
            [ '1 in_items', '1 in_items' ],
            [ 'is_in 1', 'is_in 1' ],
            [ "'2 * 3'", "'2 * 3'"]
        );
    }
    
}
