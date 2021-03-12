<?php

use PHPUnit\Framework\TestCase;

/**
 * Class ArrayMatchTest
 */
class ArrayMatchTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testIfMatchAndReplaceEqualsExpectedData()
    {
        $patterns = $this->getTestData()['patterns'];
        $array = $this->getTestData()['array'];
        $expectedMatches = $this->getExpectedData()['matches'];
        $expectedArray = $this->getExpectedData()['array'];

        $i = 0;
        foreach ($patterns as $pattern => $value) {
            if (!array_key_exists('shouldMatch', $value)) {
                throw new Exception('Incorrect test data supplied.');
            }

            $hasMatches = ArrayMatch::match($pattern, $array, $matches);

            if ($value['shouldMatch']) {
                $this->assertTrue($hasMatches);
            } else {
                $this->assertFalse($hasMatches);
            }

            $this->assertEquals($expectedMatches[$i], $matches);

            if ($matches && array_key_exists('replaceWith', $value)) {
                foreach ($matches as $match) {
                    ArrayMatch::replace($match['track'], $value['replaceWith'], $array);
                }
            }

            $i++;
        }

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @return array[]
     */
    private function getTestData()
    {
        return [
            'patterns' => [
                '*.foo' => ['replaceWith' => 'replaced foo', 'shouldMatch' => true],
                '*.hello.*.world' => ['replaceWith' => 'replaced hello world', 'shouldMatch' => true],
                '*.date' => ['replaceWith' => 'replaced date', 'shouldMatch' => true],
                'foo.bar' => ['replaceWith' => 'shouldn\'t be replaced', 'shouldMatch' => false],
                'blocks.*.page_link' => ['replaceWith' => 'replaced blocks', 'shouldMatch' => true],
            ],
            'array' => [
                [false => [['date' => new DateTime('2021-01-01 13:00:00')]]],
                [['date' => '2']],
                [['date' => '3']],
                ['bla' => [
                    'foo' => 'string',
                    'bar' => ['array 1', 'array 2']
                ]],
                'test' => [
                    'hello' => [
                        [
                            [
                                'world' => null,
                                'world2' => 'foo.bar'
                            ]
                        ],
                        [
                            'world' => 'foo.bar'
                        ]
                    ]
                ],
                [
                    [
                        'foo' => [
                            0 => 'value 1',
                            1 => 2,
                            2 => new DateTime('2021-01-01 13:00:00')
                        ],
                    ]
                ],
                'bar' => 456,
                'blocks' => [
                    [
                        'page_link' => 1
                    ],
                    [
                        'page_link' => 2
                    ],
                    [
                        'page_link' => 3
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array[]
     */
    private function getExpectedData()
    {
        return [
            'matches' => [
                [
                    [
                        'track' => [3, 'bla', 'foo'],
                        'value' => 'string',
                    ],
                    [
                        'track' => [4, 0, 'foo'],
                        'value' => [
                            0 => 'value 1',
                            1 => 2,
                            2 => new DateTime('2021-01-01 13:00:00')
                        ]
                    ]
                ],
                [
                    [
                        'track' => ['test', 'hello', 0, 0, 'world'],
                        'value' => null
                    ],
                    [
                        'track' => ['test', 'hello', 1, 'world'],
                        'value' => 'foo.bar'
                    ]
                ],
                [
                    [
                        'track' => [0, 0, 0, 'date'],
                        'value' => new DateTime('2021-01-01 13:00:00')
                    ],
                    [
                        'track' => [1, 0, 'date'],
                        'value' => '2'
                    ],
                    [
                        'track' => [2, 0, 'date'],
                        'value' => '3'
                    ],
                ],
                null,
                [
                    [
                        'track' => ['blocks', 0, 'page_link'],
                        'value' => 1
                    ],
                    [
                        'track' => ['blocks', 1, 'page_link'],
                        'value' => 2
                    ],
                    [
                        'track' => ['blocks', 2, 'page_link'],
                        'value' => 3
                    ],
                ]
            ],
            'array' => [
                [false => [['date' => 'replaced date']]],
                [['date' => 'replaced date']],
                [['date' => 'replaced date']],
                ['bla' => [
                    'foo' => 'replaced foo',
                    'bar' => ['array 1', 'array 2']
                ]],
                'test' => [
                    'hello' => [
                        [
                            [
                                'world' => 'replaced hello world',
                                'world2' => 'foo.bar'
                            ]
                        ],
                        [
                            'world' => 'replaced hello world'
                        ]
                    ]
                ],
                [
                    [
                        'foo' => 'replaced foo'
                    ]
                ],
                'bar' => 456,
                'blocks' => [
                    [
                        'page_link' => 'replaced blocks'
                    ],
                    [
                        'page_link' => 'replaced blocks'
                    ],
                    [
                        'page_link' => 'replaced blocks'
                    ]
                ]
            ]
        ];
    }
}
