<?php

namespace CollectiveVotingBundle\Tests\DecisionMaker\Strategy;

use CollectiveVotingBundle\DecisionMaker\Strategy\PercentageStrategy;
use CollectiveVotingBundle\Entity\VotingProcess;

/**
 * @see PercentageStrategy
 * @package CollectiveVotingBundle\Tests\DecisionMaker\Strategy
 */
class PercentageStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function provideData()
    {
        return [
            'Passed, 5/6 votes, 50% required' => [
                6,
                50,
                [
                    'for'     => 4,
                    'against' => 1,
                ],
                true,
                'for',
            ],

            'Failed, 2/6 votes, 50% required' => [
                6,
                50,
                [
                    'for'     => 2,
                    'against' => 0,
                ],
                false,
                'for',
            ],

            'Passed, 6/6 votes, 100% required' => [
                6,
                100,
                [
                    'red'   => 0,
                    'black' => 6,
                ],
                true,
                'black',
            ],

            'Passed, more votes than required' => [
                6,
                75,
                [
                    'red'   => 5,
                    'black' => 161,
                ],
                true,
                'black',
            ]
        ];
    }

    /**
     * @dataProvider provideData
     *
     * @param int   $maxExpectedVotes
     * @param float $percent
     * @param array $votesCount
     * @param bool  $couldBeTaken
     * @param mixed $finalOption
     */
    public function testCouldBeTaken(int $maxExpectedVotes, float $percent, array $votesCount, bool $couldBeTaken, $finalOption)
    {
        $strategy = new PercentageStrategy($maxExpectedVotes, $percent);

        $this->assertSame($couldBeTaken, $strategy->couldBeTaken(new VotingProcess(), $votesCount));
        $this->assertSame($finalOption, $strategy->getFinalOption($votesCount));
    }
}