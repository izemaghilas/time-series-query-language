<?php

declare(strict_types=1);

namespace App\AST;

class NullAST extends Node {
    public function addNode(Node $node): void {
        // do nothing
    }
}