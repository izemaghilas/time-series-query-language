<?php

declare(strict_types=1);

namespace App\AST;

use Exception;

class PredicateStatement extends Node {

    public function __construct() {
        parent::__construct();
    }

    public function addNode(Node $node): void {
        if(
            $node instanceof Column
            ||
            $node instanceof ComparisonOperator
            ||
            $node instanceof PredicateExpression
        ){
            parent::addNode($node);
        }
        else{
            throw new Exception("node must be type of Column, ComparisonOperator or PredicateExpression");
        }
    }
}