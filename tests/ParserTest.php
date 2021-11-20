<?php

declare(strict_types=1);

namespace App\Tests;

use App\AST;
use App\AST\NullAST;

use App\Tests\ParserTestHelper;

use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase {
    // Note: order of nodes in AST: ProjectionStatementNode TableNode SelectionStatementNode

    /**
     * @dataProvider nullASTProvider
     */
    public function testEmptyQuery(AST $ast, string $NULL_AST_CLASS_NAME): void {
        $this->assertInstanceOf($NULL_AST_CLASS_NAME, $ast);
    }

    public function nullASTProvider(): array {
        return [
            [
                ParserTestHelper::getAST(""),
                NullAST::class, 
            ],
            [
                ParserTestHelper::getAST(" "),
                NullAST::class 
            ],
            [
                ParserTestHelper::getAST("       "),
                NullAST::class
            ],
            [
                ParserTestHelper::getAST("    "),
                NullAST::class, 
            ],
            [
                ParserTestHelper::getAST("                          "),
                NullAST::class
            ]
        ];
    }


    /**
     * @dataProvider projectionProvider
     * @dataProvider multipleProjectionStatementsProvider
     * @dataProvider selectionProvider
     * @dataProvider conjunctionProvider
     * @dataProvider disjunctionProvider
     * @dataProvider conjunctionDisjunctionProvider
     * @dataProvider parenthesisProvider
     */
    public function testParseQuery(AST $ast, AST $expectedAST): void {
        $this->assertEquals($expectedAST, $ast);
    }

    public function projectionProvider(): array {

        return [
            [
                ParserTestHelper::getAST("       select   column_name   from    table_name ;     "),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, null, "", null)],
                    null, 
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name [ 10.25 : 30 ] from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", 10.25, 30, "", null)],
                    null, 
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name [ 10.25 : ] from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", 10.25, null, "", null)],
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name[ : 30.80] from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, 30.80, "", null)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name [ : ] from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, null, "", null)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name < 25 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, null, "<", 25)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name > 18.15 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, null, ">", 18.15)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name = 10.25 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, null, "=", 10.25)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name != 15 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", null, null, "!=", 15)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name[20:30] = 15 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", 20, 30, "=", 15)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name[20:30] != 15 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", 20, 30, "!=", 15)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name[20:30] < 15 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", 20, 30, "<", 15)], 
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name[20:30] > 15.85 from table_name  ;"),
                ParserTestHelper::createExpectedAST(
                    [ParserTestHelper::createProjectionStatementNode("column_name", 20, 30, ">", 15.85)], 
                    null,
                    "table_name"
                )
            ],

        ];
    }
    
    public function multipleProjectionStatementsProvider(): array {
        return [
            [
                ParserTestHelper::getAST("select column_name1[20:30], column_name2  from table_name ;"),
                ParserTestHelper::createExpectedAST(
                    [ 
                        ParserTestHelper::createProjectionStatementNode("column_name1", 20, 30, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name2", null, null, "", null)
                    ],
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name1[20:30], column_name2[20:], column_name3[:30]=5  from table_name ;"),
                ParserTestHelper::createExpectedAST(
                    [ 
                        ParserTestHelper::createProjectionStatementNode("column_name1", 20, 30, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name2", 20, null, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name3", null, 30, "=", 5)
                    ],
                    null,
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name1<6, column_name2!=25.5, column_name3>5  from table_name ;"),
                ParserTestHelper::createExpectedAST(
                    [ 
                        ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "<", 6),
                        ParserTestHelper::createProjectionStatementNode("column_name2", null, null, "!=", 25.5),
                        ParserTestHelper::createProjectionStatementNode("column_name3", null, null, ">", 5)
                    ],
                    null,
                    "table_name"
                )
            ],
        ];
    }

    public function selectionProvider(): array {
        return [
            [
                ParserTestHelper::getAST("select column_name1<6 from table_name where any column_name2 > 10 ;"),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "<", 6) ],
                    ParserTestHelper::createSelectionStatementNode(
                        "any", 
                        ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 10)
                    ),
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name1<6, column_name2 [20: 30] from table_name where all column_name2[:20] ;"),
                ParserTestHelper::createExpectedAST(
                    [ 
                        ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "<", 6),
                        ParserTestHelper::createProjectionStatementNode("column_name2", 20, 30, "", null)
                    ],
                    ParserTestHelper::createSelectionStatementNode(
                        "all",
                        ParserTestHelper::createPredicateStatementNode("column_name2", null, 20, "", null)
                    ),
                    "table_name"
                )
            ]
        ];
    }

    public function conjunctionProvider(): array {
        return [
            [
                ParserTestHelper::getAST("select column_name1<6 from table_name where any column_name2 > 10 and all column_name3 = 2.5 ;"),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "<", 6) ],
                    ParserTestHelper::createConjunctionStatementNode(
                        [
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 10)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name3", null, null, "=", 2.5)
                            )
                        ]
                    ),
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name1<6 from table_name where any column_name2 > 10 and all column_name3 = 2.5 and any column_name4!=9 ;"),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "<", 6) ],
                    ParserTestHelper::createConjunctionStatementNode(
                        [
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 10)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name3", null, null, "=", 2.5)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name4", null, null, "!=", 9)
                            )
                        ]
                    ),
                    "table_name"
                )
            ]

        ];
    }

    public function disjunctionProvider(): array {
        return [
            [
                ParserTestHelper::getAST("select column_name1<6 from table_name where any column_name2 > 10 or all column_name3 = 2.5 ;"),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "<", 6) ],
                    ParserTestHelper::createDisjunctionStatementNode(
                        [
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 10)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name3", null, null, "=", 2.5)
                            )
                        ]
                    ),
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST("select column_name1[10.2:25.3]<6 from table_name where any column_name2 > 10 or all column_name3 = 2.5 or any column_name4 [:23]> 6.5 ;"),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", 10.2, 25.3, "<", 6) ],
                    ParserTestHelper::createDisjunctionStatementNode(
                        [
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 10)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name3", null, null, "=", 2.5)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name4", null, 23, ">", 6.5)
                            )
                        ]
                    ),
                    "table_name"
                )
            ]
        ];
    }

    public function conjunctionDisjunctionProvider(): array {
        $query1 = "select column_name1[10.2:25.3]<6 from table_name"
            ." where all column_name1<10 and any column_name1>5 "
            ." or "
            ." any column_name1!=50 and all column_name2!=2.5 ; ";

        $query2 = "   select   column_name1[10.2:25.3]<6 from table_name"
            ." where all column_name1   <    10 and any column_name1>5 "
            ." or "
            ." any column_name1!=50 or     all column_name2!=2.5 ;    ";
        
        $query3 = "   select   column_name1[10.2:25.3]<6 from table_name"
        ." where all column_name1   <    10"
        ." or ".
        " any column_name1>5 and any column_name1!=50"
        ."  or  "
        ."  all column_name2!=2.5 ;    ";

        return [
            [
                ParserTestHelper::getAST($query1),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", 10.2, 25.3, "<", 6) ],
                    ParserTestHelper::createDisjunctionStatementNode([
                        ParserTestHelper::createConjunctionStatementNode([
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "<", 10)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, ">", 5)
                            )
                        ]),
                        ParserTestHelper::createConjunctionStatementNode([
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "!=", 50)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 2.5)
                            )
                        ])
                    ]),
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST($query2),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", 10.2, 25.3, "<", 6) ],
                    ParserTestHelper::createDisjunctionStatementNode([
                        ParserTestHelper::createConjunctionStatementNode([
                            ParserTestHelper::createSelectionStatementNode(
                                "all", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "<", 10)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, ">", 5)
                            )
                        ]),
                        ParserTestHelper::createSelectionStatementNode(
                            "any", 
                            ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "!=", 50)
                        ),
                        ParserTestHelper::createSelectionStatementNode(
                            "all", 
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 2.5)
                        )
                    ]),
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST($query3),
                ParserTestHelper::createExpectedAST(
                    [ ParserTestHelper::createProjectionStatementNode("column_name1", 10.2, 25.3, "<", 6) ],
                    ParserTestHelper::createDisjunctionStatementNode([
                        ParserTestHelper::createSelectionStatementNode(
                            "all", 
                            ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "<", 10)
                        ),
                        ParserTestHelper::createConjunctionStatementNode([
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, ">", 5)
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "any", 
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "!=", 50)
                            )
                        ]),
                        ParserTestHelper::createSelectionStatementNode(
                            "all", 
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 2.5)
                        )
                    ]),
                    "table_name"
                )
            ]
        ];
    }

    public function parenthesisProvider(): array {
        $query1 = "select column_name1, column_name2 from table_name"
        ." where "
        ." all column_name2 > 5"
        ." and "
        ."( any column_name2 != 2 and all column_name1 = 5.2 );";

        $query2 = "select column_name1, column_name2 from table_name"
        ." where "
        ." all column_name2 > 5"
        ." and "
        ."( any column_name2 != 2 or all column_name1 = 5.2 );";

        $query3 = "select column_name1, column_name2 from table_name"
        ." where "
        ." (all column_name2 > 5)"
        ." or "
        ."( any column_name2 != 2 or all column_name1 = 5.2 );";

        $query4 = "select column_name1, column_name2 from table_name"
        ." where "
        ." (all column_name2 > 5 "
        ." and".
        " (all column_name2 = 2 or any column_name1 != 6 and (any column_name2!=10 or all column_name1>15)));";

        return [
            [
                ParserTestHelper::getAST($query1),
                ParserTestHelper::createExpectedAST(
                    [
                        ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name2", null, null, "", null)
                    ], 
                    ParserTestHelper::createConjunctionStatementNode([
                        ParserTestHelper::createSelectionStatementNode(
                            "all",
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 5) 
                        ),
                        ParserTestHelper::createSelectionStatementNode(
                            "any",
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 2) 
                        ),
                        ParserTestHelper::createSelectionStatementNode(
                            "all",
                            ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "=", 5.2) 
                        ),
                    ]), 
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST($query2),
                ParserTestHelper::createExpectedAST(
                    [
                        ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name2", null, null, "", null)
                    ], 
                    ParserTestHelper::createConjunctionStatementNode([
                        ParserTestHelper::createSelectionStatementNode(
                            "all",
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 5) 
                        ),
                        ParserTestHelper::createDisjunctionStatementNode([
                            ParserTestHelper::createSelectionStatementNode(
                                "any",
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 2) 
                            ),
                            ParserTestHelper::createSelectionStatementNode(
                                "all",
                                ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "=", 5.2) 
                            ),
                        ])
                    ]), 
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST($query3),
                ParserTestHelper::createExpectedAST(
                    [
                        ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name2", null, null, "", null)
                    ], 
                    ParserTestHelper::createDisjunctionStatementNode([
                        ParserTestHelper::createSelectionStatementNode(
                            "all",
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 5) 
                        ),
                        ParserTestHelper::createSelectionStatementNode(
                            "any",
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 2) 
                        ),
                        ParserTestHelper::createSelectionStatementNode(
                            "all",
                            ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "=", 5.2) 
                        ),
                    ]), 
                    "table_name"
                )
            ],
            [
                ParserTestHelper::getAST($query4),
                ParserTestHelper::createExpectedAST(
                    [
                        ParserTestHelper::createProjectionStatementNode("column_name1", null, null, "", null),
                        ParserTestHelper::createProjectionStatementNode("column_name2", null, null, "", null)
                    ],
                    ParserTestHelper::createConjunctionStatementNode([
                        ParserTestHelper::createSelectionStatementNode(
                            "all",
                            ParserTestHelper::createPredicateStatementNode("column_name2", null, null, ">", 5)
                        ),
                        ParserTestHelper::createDisjunctionStatementNode([
                            ParserTestHelper::createSelectionStatementNode(
                                "all",
                                ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "=", 2)
                            ),
                            ParserTestHelper::createConjunctionStatementNode([
                                ParserTestHelper::createSelectionStatementNode(
                                    "any",
                                    ParserTestHelper::createPredicateStatementNode("column_name1", null, null, "!=", 6)
                                ),
                                ParserTestHelper::createDisjunctionStatementNode([
                                    ParserTestHelper::createSelectionStatementNode(
                                        "any",
                                        ParserTestHelper::createPredicateStatementNode("column_name2", null, null, "!=", 10)
                                    ),
                                    ParserTestHelper::createSelectionStatementNode(
                                        "all",
                                        ParserTestHelper::createPredicateStatementNode("column_name1", null, null, ">", 15)
                                    )
                                ])
                            ])

                        ])
                    ]),
                    "table_name"
                )
            ]
        ];
    }
}