<?php

namespace CollectiveVotingBundle\Tests\DecisionMaker\Strategy;
use CollectiveVotingBundle\DecisionMaker\Strategy\WithTimePeriodStrategy;
use CollectiveVotingBundle\Entity\VotingProcess;

/**
 * @see WithTimePeriodStrategy
 * @package CollectiveVotingBundle\Tests\DecisionMaker\Strategy
 */
class WithTimePeriodStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function provideData()
    {
        return [
            'Passing, dates before NOW' => [
                (new \DateTime('now'))->modify('-5 days'),
                (new \DateTime('now'))->modify('-1 day'),
                true,
                [
                    'for' => 2,
                ]
            ],

            'Failing, dates equal to current time' => [
                new \DateTime('now'),
                new \DateTime('now'),
                false,
                [
                    'for' => 1,
                ],
                'Invalid $startDate and $endDate. Dates cannot be equal, and the start should begin before end',
            ],

            'Failing, dates in the future' => [
                (new \DateTime('now'))->modify('+2 days'),
                (new \DateTime('now'))->modify('+7 day'),
                false,
                [
                    'for' => 5,
                ]
            ],
        ];
    }


    /**
     * @dataProvider provideData
     *
     * @param \DateTime   $startDate
     * @param \DateTime   $endDate
     * @param bool        $couldBeTaken
     * @param array       $votes
     * @param string|null $exceptionMessage
     */
    public function testCouldBeMade(
        \DateTime $startDate,
        \DateTime $endDate,
        bool $couldBeTaken,
        array $votes,
        string $exceptionMessage = null
    )
    {
        try {
            $strategy = new WithTimePeriodStrategy($startDate, $endDate);
            $this->assertSame($couldBeTaken, $strategy->couldBeTaken(new VotingProcess(), $votes));
        }
        catch (\Exception $e) {
            $this->assertSame($exceptionMessage, $e->getMessage());
        }
    }
}