<?php

declare(strict_types=1);

namespace App\AST;

use App\AST\Node;

class Table extends Node {
    private Name $name;

    function __construct(Name $name) {
        $this->name = $name;
    }
    
    public function addNode(Node $node): void {
        // do nothing
    }
}