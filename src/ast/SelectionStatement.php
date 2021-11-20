<?php

declare(strict_types=1);

namespace App\AST;

use Exception;

class SelectionStatement extends Node {
    
    function __construct() {
        parent::__construct();
    }

    public function addNode(Node $node): void {
        if($node instanceof PredicateStatement || $node instanceof Hold){
            parent::addNode($node);
        }
        else{
            throw new Exception("node must be type of PredicateStatement");
        }
    }
}