<?php

namespace Tests\Integration;

use EthicalJobs\Quantify\Arr;

class ArrTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_can_purge_an_array_of_a_key()
    {
        $subject = [
            [
                'animals' => [
                    'dogs' => [
                        'kelpie',
                        'labradore',
                        'pomeranian',
                    ],
                ],
                'things' => [
                    'chairs' => 22,
                    'i' => 22,
                    'cups' => 21232,
                    'knives' => 922,
                ],
                'shallow' => 111,                                 
            ],         
        ];

        $this->assertEquals(Arr::purgeKeys('i', $subject), [
            [
                'animals' => [
                    'dogs' => [
                        'kelpie',
                        'labradore',
                        'pomeranian',
                    ],
                ],
                'things' => [
                    'chairs' => 22,
                    'cups' => 21232,
                    'knives' => 922,
                ],
                'shallow' => 111,
            ],               
        ]);

    }
}
