<?php

declare(strict_types=1);

namespace App\Lexer\Token;

use App\Lexer\Token;
use App\Lexer\Token\Type;

final class StaticFactory {
    public static function factory(string $lexem): Token {
        if($lexem === ""){
            return new Token(Type::EMPTY, "");
        }

        // KEYWORDS
        elseif(preg_match("/^(s|S)(e|E)(l|L)(e|E)(c|C)(t|T)$/", $lexem)){
            return new Token(Type::KEYWORD_SELECT, "");
        }
        elseif(preg_match("/^(f|F)(r|R)(o|O)(m|M)$/", $lexem)){
            return new Token(TYPE::KEYWORD_FROM, "");
        }
        elseif(preg_match("/^(w|W)(h|H)(e|E)(r|R)(e|E)$/", $lexem)){
            return new Token(TYPE::KEYWORD_WHERE, "");
        }
        elseif(preg_match("/^(a|A)(n|N)(y|Y)$/", $lexem)){
            return new Token(Type::KEYWORD_ANY, "");
        }
        elseif(preg_match("/^(a|A)(l|L)(l|L)$/", $lexem)){
            return new Token(TYPE::KEYWORD_ALL, "");
        }
        elseif(preg_match("/^(a|A)(n|N)(d|D)$/", $lexem)){
            return new Token(TYPE::KEYWORD_AND, "");
        }
        elseif(preg_match("/^(o|O)(r|R)$/", $lexem)){
            return new Token(TYPE::KEYWORD_OR, "");
        }
        // SYMBOLS
        elseif($lexem === "["){
            return new Token(Type::LEFT_BRACKET, "");
        }
        elseif($lexem === "]"){
            return new Token(Type::RIGHT_BRACKET, "");
        }
        elseif($lexem === "("){
            return new Token(Type::LEFT_PARENTHESIS, "");
        }
        elseif($lexem === ")"){
            return new Token(Type::RIGHT_PARENTHESIS, "");
        }
        elseif($lexem === "."){
            return new Token(Type::DOT, "");
        }
        elseif($lexem === ","){
            return new Token(Type::COMMA, "");
        }
        elseif($lexem === ":"){
            return new Token(Type::COLON, "");
        }
        elseif($lexem === "="){
            return new Token(Type::EQUAL, "=");
        }
        elseif($lexem === "!"){
            return new Token(Type::EXLAMATION_POINT, "!");
        }
        elseif($lexem === "<"){
            return new Token(Type::LESS, "<");
        }
        elseif($lexem === ">"){
            return new Token(Type::GREATER, ">");
        }
        elseif($lexem === ";"){
            return new Token(Type::EOQ, "");
        }
        
        // IDENTIFIER
        elseif(preg_match("/^([a-zA-Z]|_)+([a-zA-Z]|_|[0-9])*$/", $lexem)){
            return new Token(Type::IDENTIFIER, $lexem);
        }

        // NUMBER
        elseif(preg_match("/^[0-9]*$/", $lexem)){
            return new Token(Type::NUMBER, $lexem);
        }
        
        else {
            return new Token(Type::UNRECOGNIZED, $lexem);
        }
    }
}