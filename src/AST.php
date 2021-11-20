<?php

declare(strict_types=1);

namespace App;

use App\AST\Node;

interface AST {
    public function addNode(Node $node): void;
    public function getChildren(): array;
}