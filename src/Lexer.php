<?php

declare(strict_types=1);

namespace App;

use App\Lexer\Token;
use App\Lexer\Token\StaticFactory;
use Exception;

class Lexer {

    private static ?Lexer $instance = null;
    private string $query;
    private int $position;

    private const SEPERATOR_SYMBOLS = ["[", "]", "(", ")", ",", ":", ".", ";", "=", "!", "<", ">"];

    private function __construct() {
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }


    public static function getInstance(): Lexer {
        if(static::$instance === null){
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function init(string $query): void {
        $this->query = trim($query);
        $this->position = 0;
    }

    public function nextToken(): ?Token {
        $lexem = $this->buildLexem();
        return StaticFactory::factory($lexem);
    }

    private function buildLexem(): string {
        $lexem = "";
        while(true){
            $character = $this->nextCharacter();

            if(in_array($character, static::SEPERATOR_SYMBOLS) && $lexem !== "") {
                //if not deincremneted, the seperator symbol will be skipped on next call to buildLexem
                $this->position-=1;
                return $lexem;
            }
            else if((ctype_space($character) || $character === "") && $lexem !== ""){
                return $lexem;
            }
            else if( ( in_array($character, static::SEPERATOR_SYMBOLS) || $character === "" ) && $lexem === "" ){
                return $character;
            }
            else if(!ctype_space($character)){
                $lexem .= $character;
            }
        }
    }

    private function nextCharacter(): string {
        $character = "";
        if($this->position < strlen($this->query)){
            $character = $this->query[$this->position];
            $this->position+=1;
        }
        return $character;
    }
}