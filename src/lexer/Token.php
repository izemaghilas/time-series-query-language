<?php

declare(strict_types=1);

namespace App\Lexer;

use App\Lexer\Token\Type;

class Token {
    private Type $type;
    private string $value;

    function __construct(Type $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): Type {
        return $this->type;
    }

    public function getValue(): string {
        return $this->value;
    }
}