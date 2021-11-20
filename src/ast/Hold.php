<?php

declare(strict_types=1);

namespace App\AST;

use Exception;

class Hold extends Node {
    private static array $HOLDS = ["ANY", "ALL"];
    private string $value;

    public function __construct(string $value){
        if( in_array(strtoupper($value), static::$HOLDS) ){
            $this->value = strtoupper($value);
        }
        else{
            throw new Exception("Unsupported hold '{$value}'");
        }
    }
}