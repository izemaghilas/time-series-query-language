<?php

declare(strict_types=1);

namespace App\AST;

class PredicateExpression extends Node {
    private mixed $expression;

    public function __construct(mixed $expression) {
        $this->expression = $expression;
    }

    public function addNode(Node $node): void {
        // do nothing
    }
}