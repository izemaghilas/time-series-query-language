<?php

declare(strict_types=1);

namespace App\AST;

use Exception;

class ConjunctionStatement extends Node{
    function __construct() {
        parent::__construct();
    }

    public function addNode(Node $node): void {
        if($node instanceof SelectionStatement || $node instanceof DisjunctionStatement){
            parent::addNode($node);
        }
        else{
            throw new Exception("node must be type of SelectionStatement or DisjunctionStatement, ".get_class($node)." was given");
        }
    }
}