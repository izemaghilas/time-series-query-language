<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\Lexer;
use App\Lexer\Token;
use App\Lexer\Token\Type;

// White list testing => test known tokens

class LexerTest extends TestCase { 

    /**
     * provide query to lexer
     * @return Token
     */
    private function feedQueryToLexer(string $query): Token {
        $lexer = Lexer::getInstance();
        $lexer->init($query);

        return $lexer->nextToken();
    }
    
    /**
     * @test
     */
    public function should_return_KEYWORD_SELECT_Token_on_query_contains_select_characters(): void {
        $query = "select";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_SELECT, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_SELECT, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_KEYWORD_FROM_Token_on_query_contains_from_characters(): void {
        $query = "from";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_FROM, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_FROM, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_KEYWORD_WHERE_Token_on_query_contains_where_characters(): void {
        $query = "where";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_WHERE, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_WHERE, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_KEYWORD_ANY_Token_on_query_contains_any_characters(): void {
        $query = "any";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_ANY, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_ANY, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_KEYWORD_ALL_Token_on_query_contains_all_characters(): void {
        $query = "all";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_ALL, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_ALL, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_KEYWORD_AND_Token_on_query_contains_and_characters(): void {
        $query = "and";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_AND, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_AND, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_KEYWORD_OR_Token_on_query_contains_or_characters(): void {
        $query = "or";

        $tokenInLowerCaseKeyword = $this->feedQueryToLexer($query);
        $tokenInUpperCaseKeyword = $this->feedQueryToLexer(strtoupper($query));

        $this->assertSame(Type::KEYWORD_OR, $tokenInLowerCaseKeyword->getType());
        $this->assertSame(Type::KEYWORD_OR, $tokenInUpperCaseKeyword->getType());

        $this->assertEmpty($tokenInLowerCaseKeyword->getValue());
        $this->assertEmpty($tokenInUpperCaseKeyword->getValue());
    }

    /**
     * @test
     */
    public function should_return_LEFT_BRACKET_Token_on_query_contains_left_bracket(): void {
        $query = "[";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::LEFT_BRACKET, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_RIGHT_BRACKET_Token_on_query_contains_right_bracket(): void {
        $query = "]";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::RIGHT_BRACKET, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_LEFT_PARENTHESIS_Token_on_query_contains_left_parenthesis(): void {
        $query = "(";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::LEFT_PARENTHESIS, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_RIGHT_PARENTHESIS_Token_on_query_contains_right_parenthesis(): void {
        $query = ")";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::RIGHT_PARENTHESIS, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_COMMA_Token_on_query_contains_comma(): void {
        $query = ",";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::COMMA, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_COLON_Token_on_query_contains_colon(): void {
        $query = ":";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::COLON, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_EQUAL_Token_on_query_contains_equal(): void {
        $query = "=";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::EQUAL, $token->getType());
        $this->assertSame("=", $token->getValue());
    }

    /**
     * @test
     */
    public function should_return_EXLAMATION_POINT_Token_on_query_contains_not_equal(): void {
        $query = "!";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::EXLAMATION_POINT, $token->getType());
        $this->assertSame("!", $token->getValue());
    }

    /**
     * @test
     */
    public function should_return_LESS_Token_on_query_contains_less(): void {
        $query = "<";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::LESS, $token->getType());
        $this->assertSame("<", $token->getValue());
    }

    /**
     * @test
     */
    public function should_return_GREATER_Token_on_query_contains_greater(): void {
        $query = ">";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::GREATER, $token->getType());
        $this->assertSame(">", $token->getValue());
    }

    /**
     * @test
     */
    public function should_return_DOT_Token_on_query_contains_dot(): void {
        $query = ".";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::DOT, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_EOQ_Token_on_query_contains_end_of_query(): void {
        $query = ";";

        $token = $this->feedQueryToLexer($query);
        $this->assertSame(Type::EOQ, $token->getType());
        $this->assertEmpty($token->getValue());
    }

    /**
     * @test
     */
    public function should_return_NUMBER_Token_on_query_contains_number_type_integer(): void {
        $query1 = "5";
        $query2 = "12";
        $query3 = "532131";

        $token1 = $this->feedQueryToLexer($query1);
        $token2 = $this->feedQueryToLexer($query2);
        $token3 = $this->feedQueryToLexer($query3);

        $this->assertSame(Type::NUMBER, $token1->getType());
        $this->assertSame(Type::NUMBER, $token2->getType());
        $this->assertSame(Type::NUMBER, $token3->getType());
    
        $this->assertSame("5", $token1->getValue());
        $this->assertSame("12", $token2->getValue());
        $this->assertSame("532131", $token3->getValue());
    }

    /**
     * @test
     * 
     * identifier can be column name or table name
     */
    public function should_return_IDENTIFIER_Token_on_query_contains_column_name_and_table_name(): void {
        $query1 = "users";
        $query2 = "USERS";
        $query3 = "created_AT";
        $query4 = "_birthDate2"; // hh
        $query5 = "___UPDATed_at____2021_test"; // hh
        
        $token1 = $this->feedQueryToLexer($query1);
        $token2 = $this->feedQueryToLexer($query2);
        $token3 = $this->feedQueryToLexer($query3);
        $token4 = $this->feedQueryToLexer($query4);
        $token5 = $this->feedQueryToLexer($query5);

        $this->assertSame(Type::IDENTIFIER, $token1->getType());
        $this->assertSame(Type::IDENTIFIER, $token2->getType());
        $this->assertSame(Type::IDENTIFIER, $token3->getType());
        $this->assertSame(Type::IDENTIFIER, $token4->getType());
        $this->assertSame(Type::IDENTIFIER, $token5->getType());

        $this->assertSame($query1, $token1->getValue());
        $this->assertSame($query2, $token2->getValue());
        $this->assertSame($query3, $token3->getValue());
        $this->assertSame($query4, $token4->getValue());
        $this->assertSame($query5, $token5->getValue());

    }

}