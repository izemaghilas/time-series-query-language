<?php

declare(strict_types=1);

namespace App\AST;

use App\AST\Node;
use Exception;

class Column extends Node {
    private Name $name;

    function __construct(Name $name) {
        $this->name = $name;
        parent::__construct();
    }

    public function addNode(Node $node): void {
        if($node instanceof Range) {
            parent::addNode($node);
        }
        else{
            throw new Exception("node must be type of Range");
        }
    }
}