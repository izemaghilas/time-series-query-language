<?php

declare(strict_types=1);

namespace App\AST;

class Range extends Node {
    private mixed $lowest;
    private mixed $highest;

    public function __construct(mixed $lowest, mixed $highest) {
        $this->lowest = $lowest;
        $this->highest = $highest;
    }

    public function addNode(Node $node): void {
        // do nothing
    }
}