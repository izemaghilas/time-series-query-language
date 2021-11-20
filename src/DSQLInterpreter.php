<?php

declare(strict_types=1);

namespace App;

use App\SQLKeywords;

use Exception;

class DSQLInterpreter {
    private static ?DSQLInterpreter $instance = null;

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

    public static function getInstance(): DSQLInterpreter {
        if(static::$instance === null){
            static::$instance = new static();
        }
        return static::$instance;
    }


    /**
     * interpreter entry point
     */
    public function interpret(string $query): string {
        $lexer = Lexer::getInstance();
        $lexer->init($query);
        $parser = Parser::getInstance();
        $parser->init($lexer);
        $ast = $parser->parse();
        $sql = $this->buildSQL($ast);

        return $sql;
    }

    private function buildSQL(AST $ast): string {
        $sql = SQLKeywords::SELECT->value;
        $this->traverseAST($ast, $sql);
        return $sql;
    } 



    private function traverseAST(AST $ast, string $sql): void {
        
    }



}
