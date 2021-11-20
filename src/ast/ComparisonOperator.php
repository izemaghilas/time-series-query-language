<?php

declare(strict_types=1);

namespace App\AST;

use Exception;

class ComparisonOperator extends Node{
    private static array $OPERATORS = ["=", "!=", "<", ">"];

    private string $value;

    public function __construct(string $value) {
        if( in_array($value, static::$OPERATORS) ){
            $this->value = $value;
        }
        else{
            throw new Exception("Unsupported comparison operator '{$value}'");
        }
    }

    public function addNode(Node $node): void {
        // do nothing
    }

}