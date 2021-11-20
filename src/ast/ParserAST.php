<?php

declare(strict_types=1);

namespace App\AST;

use App\AST;
use App\AST\Node;
use Exception;

class ParserAST extends Node {

    function __construct() {
        parent::__construct();
    }
    public function addNode(Node $node): void {
        if(
            $node instanceof ProjectionStatement
            ||
            $node instanceof SelectionStatement
            ||
            $node instanceof ConjunctionStatement
            ||
            $node instanceof DisjunctionStatement
            ||
            $node instanceof Table
        ){
            parent::addNode($node);
        }
        else{
            throw new Exception("node must be type of ProjectionStatement, SelectionStatement, ConjunctionStatement, DisjunctionStatement or Table");
        }
    }

    public static function createAST(): AST {
        return new ParserAST();
    }

    public static function createRange(mixed $lowest, mixed $highest): ?Range {
        if(isset($lowest) === false && isset($highest) === false){
            return null;
        }
        return new Range($lowest, $highest);
    }

    public static function createColumn(string $name): Column {
        return new Column( new Name($name) );
    }

    public static function createComparisonOperator(string $value): ComparisonOperator {
        return new ComparisonOperator($value);
    }

    public static function createPredicateExpression(mixed $expression): PredicateExpression {
        return new PredicateExpression($expression);
    }

    public static function createTable(string $name): Table {
        return new Table( new Name($name) );
    }
    
    public static function createPredicateStatement(): PredicateStatement {   
        return new PredicateStatement();
    }

    public static function createProjectionStatement(): ProjectionStatement {
        return new ProjectionStatement();
    }

    public static function createSelectionStatement(): SelectionStatement {
        return new SelectionStatement();
    }

    public static function createConjunctionStatement(): ConjunctionStatement {
        return new ConjunctionStatement();
    }

    public static function createDisjunctionStatement(): DisjunctionStatement {
        return new DisjunctionStatement();
    }

    public static function createHold(string $value): Hold {
        return new Hold($value);
    }
}   