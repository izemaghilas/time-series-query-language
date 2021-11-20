<?php

declare(strict_types=1);

namespace App\Tests;

use App\DSQLInterpreter;
use PHPUnit\Framework\TestCase;

// TODO:: the interpreter returns sql query not ast
// the parser is the reponsible of generating AST as Intermediate representation

final class DSQLInterpreterTest extends TestCase {   
    
    private function interpretQuery(string $query): string {
        $DSQLinterpreter = DSQLInterpreter::getInstance();
        return $DSQLinterpreter->interpret($query);
    }

    /**
     * @dataProvider simpleQueriesProvider
     */
    public function testInterpreter(string $query, string $sql): void {
        $this->assertSame($query, $sql);
    }

    public function simpleQueriesProvider(): array {
        $query1 = "select column_name from table_name;";
        
        return [
            [
                $this->interpretQuery($query1),
                "SELECT column_name FROM table_name;"
            ]
        ];
    }

}
