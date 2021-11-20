<?php

namespace App\Lexer\Token;

enum Type: string {

    // KEYWORDS
    case KEYWORD_SELECT = 'KEYWORD_SELECT';
    case KEYWORD_FROM = 'KEYWORD_FROM';
    case KEYWORD_WHERE = 'KEYWORD_WHERE';
    case KEYWORD_ANY = 'KEYWORD_ANY';
    case KEYWORD_ALL = 'KEYWORD_ALL';
    case KEYWORD_AND = 'KEYWORD_AND';
    case KEYWORD_OR = 'KEYWORD_OR';
    
    case IDENTIFIER = 'IDENTIFIER';
    case NUMBER = 'NUMBER';

    // SYMBOLS
    case LEFT_BRACKET = 'LEFT_BRACKET';
    case RIGHT_BRACKET = 'RIGHT_BRACKET';
    case LEFT_PARENTHESIS = 'LEFT_PARENTHESIS';
    case RIGHT_PARENTHESIS = 'RIGHT_PARENTHESIS';
    case DOT = 'DOT';
    case COMMA = 'COMMA';
    case COLON = 'COLON';
    case EQUAL = 'EQUAL';
    case EXLAMATION_POINT = 'EXLAMATION_POINT';
    case LESS = 'LESS';
    case GREATER = 'GREATER';
    case EOQ = 'EOQ'; //End Of Query ";"
    
    case EMPTY = 'EMPTY'; // if lexem => ""
    case UNRECOGNIZED = 'UNRECOGNIZED';
}