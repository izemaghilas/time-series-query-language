<?php

declare(strict_types=1);

namespace App\AST;

use App\AST;

class Name{
    private string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}