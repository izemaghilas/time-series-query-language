<?php

declare(strict_types=1);

namespace App\Tests;

use App\AST;
use App\AST\ConjunctionStatement;
use App\AST\DisjunctionStatement;
use App\AST\ParserAST;
use App\AST\PredicateStatement;
use App\AST\ProjectionStatement;
use App\AST\SelectionStatement;
use App\Lexer;
use App\Parser;

class ParserTestHelper {

    public static function getAST(string $query): AST {
        $lexer = Lexer::getInstance();
        $lexer->init($query);
        $parser = Parser::getInstance();
        $parser->init($lexer);
        return $parser->parse();
    } 

    public static function createExpectedAST(
        array $projectionStatements, 
        SelectionStatement|ConjunctionStatement|DisjunctionStatement|NULL $selectionStatement, 
        string $tableName
    ): AST {
        $ast = ParserAST::createAST();
        foreach ($projectionStatements as $projectionStatementNode) {
            $ast->addNode($projectionStatementNode);
        }
        $ast->addNode( ParserAST::createTable($tableName) );
        if(isset($selectionStatement)){
            $ast->addNode($selectionStatement);
        }
        return $ast;
    }

    public static function createPredicateStatementNode(
        string $columnName,
        mixed $rangeLowestValue, mixed $rangeHighestValue,
        string $comparisonOperatorValue, 
        mixed $expression
    ): PredicateStatement {

        $predicateStatement = ParserAST::createPredicateStatement();
        $column = ParserAST::createColumn($columnName);
        $range = ParserAST::createRange($rangeLowestValue, $rangeHighestValue);
        if(isset($range)){
            $column->addNode($range);
        }
        $predicateStatement->addNode($column);
        
        if(isset($comparisonOperatorValue) && isset($expression)) {
            $comparisonOperator = ParserAST::createComparisonOperator($comparisonOperatorValue);
            $predicateExpression = ParserAST::createPredicateExpression($expression);
            
            $predicateStatement->addNode($comparisonOperator);
            $predicateStatement->addNode($predicateExpression);
        }

        return $predicateStatement;
    } 

    public static function createProjectionStatementNode(
        string $columnName,
        mixed $rangeLowestValue, mixed $rangeHighestValue,
        string $comparisonOperatorValue, 
        mixed $expression

    ): ProjectionStatement {

        $projectionStatement = ParserAST::createProjectionStatement();
        $predicateStatement = static::createPredicateStatementNode(
            $columnName,
            $rangeLowestValue,
            $rangeHighestValue,
            $comparisonOperatorValue,
            $expression
        );
        $projectionStatement->addNode($predicateStatement);
        return $projectionStatement;
    }
    
    public static function createSelectionStatementNode(
        string $holdValue, 
        PredicateStatement $predicateStatement

    ): SelectionStatement {

        $selectionStatement = ParserAST::createSelectionStatement();
        $hold = ParserAST::createHold($holdValue);
        $selectionStatement->addNode($hold);
        $selectionStatement->addNode($predicateStatement);

        return $selectionStatement;
    }

    public static function createConjunctionStatementNode(array $selectionStatements): ConjunctionStatement {
        $conjunctionStatement = ParserAST::createConjunctionStatement();
        foreach($selectionStatements as $selectionStatementNode){
            $conjunctionStatement->addNode( $selectionStatementNode );
        }

        return $conjunctionStatement;
    }

    public static function createDisjunctionStatementNode(array $selectionStatements): DisjunctionStatement {
        $disjunctionStatement = ParserAST::createDisjunctionStatement();
        foreach($selectionStatements as $selectionStatementNode){
            $disjunctionStatement->addNode( $selectionStatementNode );
        }

        return $disjunctionStatement;
    }
}