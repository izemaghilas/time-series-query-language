<?php

declare(strict_types=1);

namespace App;

use App\AST\ComparisonOperator;
use App\AST\ConjunctionStatement;
use App\AST\DisjunctionStatement;
use App\AST\Hold;
use App\AST\Node;
use App\AST\NullAST;
use App\AST\ParserAST;
use App\AST\PredicateExpression;
use App\AST\PredicateStatement;
use App\AST\Range;
use App\AST\SelectionStatement;
use App\Lexer\Token;
use App\Lexer\Token\Type;

use Exception;

/**
 * Parser
 * CFG (Context Free Grammar)
 * recursive-descent parser (top-down)
 * 
 *      QUERY: keyword_select (PROJECTION)+ keyword_from identifier ( ( keyword_where DISJUNCTION eoq ) | eoq )
 *      PROJECTION: PREDICATE (comma PREDICATE)*
 *      DISJUNCTION: CONJUNCTION (keyword_or CONJUNCTION)*
 *      CONJUNCTION: SELECTION (keyword_and SELECTION)*
 *      SELECTION: ( left_parenthesis DISJUNCTION right_parenthesis ) | ( HOLD PREDICATE )
 *      HOLD: keyword_all | keyword_any
 *      PREDICATE: identifier RANGE? (COMPARISON_OPERATOR EXPRESSION)?
 *      RANGE: left_bracket RANGE_LIMIT? colon RANGE_LIMIT? right_bracket
 *      RANGE_LIMIT: number (dot number)?
 *      COMPARISON_OPERATOR: equal | NOT_EQUAL | less | greater
 *      NOT_EQUAL: exlamation_point equal
 *      EXPRESSION: number (dot number)?
 */

class Parser {
    private static ?Parser $instance = null;
    private Lexer $lexer;
    private Token $currentToken;
    
    private function __construct() {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance(): Parser {
        if(static::$instance === null){
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function init(Lexer $lexer): void {
        $this->lexer = $lexer;
        $this->currentToken = $lexer->nextToken();
    }

    public function parse(): AST {
        if($this->currentToken->getType() === Type::EMPTY){
            return new NullAST();
        }
        else{
            $ast = ParserAST::createAST();
            $this->query($ast);
            return $ast;
        }
    }

    /**
     * 
     */
    private function eat(Type $tokenType): void {
        if($this->currentToken->getType() === $tokenType){
            $this->currentToken = $this->lexer->nextToken();
        }
        else{
            throw new Exception("Error while parsing query.\n\t Expected: {$tokenType->value}, found: {$this->currentToken->getType()->value}");
        }
    }

    private function clean(AST $parent, Node $node): void{
        $children = $node->getChildren();
        if(count($children) === 1){
            $parent->addNode($children[0]);
        }
        else {
            $parent->addNode($node);
        }
    }

    private function check(Node $parent, Node $node): void{
        if(get_class($parent) === get_class($node)){
            $nodeChildren = $node->getChildren();
            foreach($nodeChildren as $child){
                $parent->addNode($child);
            }
        }
        else{
            $this->clean($parent, $node);
        }
    }

    private function getNode(Node $rootNode): SelectionStatement|ConjunctionStatement|DisjunctionStatement {
        $children = $rootNode->getChildren();
        if(count($children) === 1){
            return $children[0];
        }
        return $rootNode;
    }

    /**
     * Grammar production rule "QUERY"
     */
    private function query(ParserAST $ast) {
        $this->eat(Type::KEYWORD_SELECT);
        
        foreach( $this->projection() as $projectionStatement ){
            $ast->addNode($projectionStatement);
        }
        
        $this->eat(Type::KEYWORD_FROM);

        $tableName = $this->currentToken->getValue();
        $this->eat(Type::IDENTIFIER);
        $table = ParserAST::createTable($tableName);
        $ast->addNode($table);

        if($this->currentToken->getType() === Type::KEYWORD_WHERE) {
            $this->eat(Type::KEYWORD_WHERE);
            $disjunctionStatement = $this->disjunction();
            $this->clean($ast, $disjunctionStatement);
            $this->eat(Type::EOQ);
        }
        else{
            $this->eat(Type::EOQ);
        }
    }

    /**
     * Grammar production rule "PROJECTION"
     */
    private function projection(): array {
        $arrayOfProjectionStatements = [];

        $projectionStatement = ParserAST::createProjectionStatement();
        $projectionStatement->addNode( $this->predicate() );
        array_push($arrayOfProjectionStatements, $projectionStatement);

        while($this->currentToken->getType() === Type::COMMA){
            $this->eat(Type::COMMA);
            $projectionStatement = ParserAST::createProjectionStatement();
            $projectionStatement->addNode( $this->predicate() );
            array_push( $arrayOfProjectionStatements, $projectionStatement );
        }
        
        return $arrayOfProjectionStatements;
    }

    /**
     * Grammar production rule "DISJUNCTION"
     */
    private function disjunction(): SelectionStatement|ConjunctionStatement|DisjunctionStatement {

        $disjunctionStatement = ParserAST::createDisjunctionStatement();
        
        $conjunctionStatement = $this->conjunction();
        $this->check($disjunctionStatement, $conjunctionStatement);

        while($this->currentToken->getType() === Type::KEYWORD_OR){
            $this->eat(Type::KEYWORD_OR);
            $conjunctionStatement = $this->conjunction();
            $this->check($disjunctionStatement, $conjunctionStatement);
        }
        
        return $this->getNode($disjunctionStatement);
    }

    /**
     * Grammar production rule "CONJUNCTION"
     */
    private function conjunction(): SelectionStatement|ConjunctionStatement|DisjunctionStatement {
        
        $conjunctionStatement = ParserAST::createConjunctionStatement();
        
        $selectionStatement = $this->selection();
        $this->check($conjunctionStatement, $selectionStatement);
        
        while($this->currentToken->getType() === Type::KEYWORD_AND){
            $this->eat(Type::KEYWORD_AND);
            $selectionStatement = $this->selection();
            $this->check($conjunctionStatement, $selectionStatement);
        }   
        
        return $this->getNode($conjunctionStatement);
    }

    /**
     * Grammar production rule "SELECTION"
     */
    private function selection(): SelectionStatement|ConjunctionStatement|DisjunctionStatement {
        if($this->currentToken->getType() === Type::LEFT_PARENTHESIS){
            $this->eat(Type::LEFT_PARENTHESIS);
            $disjunctionStatement = $this->disjunction();
            $this->eat(Type::RIGHT_PARENTHESIS);

            return $this->getNode($disjunctionStatement);
        }
        else {
            $selectionStatement = ParserAST::createSelectionStatement();
        
            $selectionStatement->addNode( $this->hold() );
            $selectionStatement->addNode( $this->predicate() );
            
            return $selectionStatement;
        }
    }

    /**
     * Grammar production rule "HOLD"
     */
    private function hold(): Hold {
        if($this->currentToken->getType() === Type::KEYWORD_ALL){
            $this->eat(Type::KEYWORD_ALL);
            return ParserAST::createHold("ALL");
        }
        else{
            $this->eat(Type::KEYWORD_ANY);
            return ParserAST::createHold("ANY");
        }
    }

    /**
     * Grammar production rule "PREDICATE"
     */
    private function predicate(): PredicateStatement {
        $predicateStatement = ParserAST::createPredicateStatement();
        
        $columnName = $this->currentToken->getValue();
        $this->eat(Type::IDENTIFIER);
        $column = ParserAST::createColumn($columnName);
        if($this->currentToken->getType() === Type::LEFT_BRACKET){
            $rangeNode = $this->range();
            if(isset($rangeNode)){
                $column->addNode( $rangeNode );
            }
        }
        $predicateStatement->addNode($column);

        if( 
            $this->currentToken->getType() === Type::EQUAL
            ||
            $this->currentToken->getType() === Type::EXLAMATION_POINT
            ||
            $this->currentToken->getType() === Type::LESS
            ||
            $this->currentToken->getType() === Type::GREATER
        ){
            $predicateStatement->addNode( $this->comparison_operator() );
            $predicateStatement->addNode( $this->expression() );
        }
        
        return $predicateStatement;
    }

    /**
     * Grammar production rule "COMPARISON_OPERATOR"
     */
    private function comparison_operator(): ComparisonOperator {
        $comparisonOperatorValue = $this->currentToken->getValue();
        if($comparisonOperatorValue === "!"){
            return $this->not_equal();
        }
        
        if($comparisonOperatorValue === "="){
            $this->eat(Type::EQUAL);
            return ParserAST::createComparisonOperator("=");
        }

        if($comparisonOperatorValue === "<"){
            $this->eat(Type::LESS);
            return ParserAST::createComparisonOperator("<");
        }

        if($comparisonOperatorValue === ">"){
            $this->eat(Type::GREATER);
            return ParserAST::createComparisonOperator(">");
        }
    }

    /**
     *Grammar production rule "NOT_EQUAL"
     */
    private function not_equal(): ComparisonOperator {
        $this->eat(Type::EXLAMATION_POINT);
        $this->eat(Type::EQUAL);
        return ParserAST::createComparisonOperator("!=");
    }

    /**
     * Grammar production rule "EXPRESSION"
     */
    private function expression(): PredicateExpression {
        $value = $this->currentToken->getValue();
        $this->eat(Type::NUMBER);
        if($this->currentToken->getType() === Type::DOT) {
            $this->eat(Type::DOT);
            $value .= ".".$this->currentToken->getValue();
            $this->eat(Type::NUMBER);
        }

        return ParserAST::createPredicateExpression($value);
    }

    /**
     * Grammar production rule "RANGE"
     */
    private function range(): ?Range {
        $lowestRangeValue = null;
        $highestRangeValue = null;

        $this->eat(Type::LEFT_BRACKET);
        if($this->currentToken->getType() === Type::NUMBER){
            $lowestRangeValue = $this->currentToken->getValue();
            $this->eat(Type::NUMBER);
            if($this->currentToken->getType() === Type::DOT){
                $lowestRangeValue.="." . $this->rangeLimit();
            }
        }
        $this->eat(Type::COLON);
        if($this->currentToken->getType() === Type::NUMBER){
            $highestRangeValue = $this->currentToken->getValue();
            $this->eat(Type::NUMBER);
            if($this->currentToken->getType() === Type::DOT){
                $highestRangeValue.="." . $this->rangeLimit();
            }
        }
        $this->eat(Type::RIGHT_BRACKET);

        return ParserAST::createRange($lowestRangeValue, $highestRangeValue);
    }

    /**
     * Grammar production rule "RANGE_LIMIT"
     */
    private function rangeLimit(): string {
        $this->eat(Type::DOT);
        $value = $this->currentToken->getValue();
        $this->eat(Type::NUMBER);
        return $value;
    }
}