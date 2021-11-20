<?php
declare(strict_types=1);

namespace App\AST;

use App\AST;
use Exception;

class Node implements AST {
    // array of Node
    private array $children;

    function __construct() {
        $this->children = array();
    }

    public function addNode(Node $node): void {
        if(isset($node)){
            array_push($this->children, $node);
        }
        else{
            throw new Exception("node should be set");
        }
    }

    public function getChildren(): array {
        return $this->children;
    }
}