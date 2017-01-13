<?php

namespace CollectiveVotingBundle\Tests\DecisionMaker\Strategy;

use CollectiveVotingBundle\DecisionMaker\Strategy\ChainedStrategy;
use CollectiveVotingBundle\DecisionMaker\Strategy\PercentageStrategy;
use CollectiveVotingBundle\DecisionMaker\Strategy\WithTimePeriodStrategy;
use CollectiveVotingBundle\Entity\VotingProcess;

/**
 * @see PercentageStrategy
 * @package CollectiveVotingBundle\Tests\DecisionMaker\Strategy
 */
class ChainedStrategyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see ChainedStrategy::getStrategies()
     * @see ChainedStrategy::addStrategy()
     */
    public function testGetStrategies()
    {
        $chainedStrategy = new ChainedStrategy();
        $this->assertEmpty($chainedStrategy->getStrategies());

        $chainedStrategy->addStrategy(new ChainedStrategy());
        $this->assertNotEmpty($chainedStrategy->getStrategies());
    }

    /**
     * @return array
     */
    public function provideData()
    {
        return [
            'Date (5 days - yesterday) and votes (50%, 4 available) count passing' => [
                (new \DateTime('now'))->modify('-5 days'),
                (new \DateTime('now'))->modify('-1 day'),
                4,
                50,
                true,
                [
                    'for'     => 4,
                    'against' => 0,
                ],
                'for',
            ],

            'Date (now, +7 days) and votes, not passing' => [
                new \DateTime('now'),
                (new \DateTime('now'))->modify('+7 days'),
                4,
                50,
                false,
                [
                    'for'     => 4,
                    'against' => 0,
                ],
                'for',
            ],

            'Not passing because of minimum votes requirement not met' => [
                (new \DateTime('now'))->modify('-5 days'),
                (new \DateTime('now'))->modify('-1 day'),
                4,
                75,
                false,
                [
                    'for'     => 1,
                    'against' => 0,
                ],
                'for',
            ],
        ];
    }

    /**
     * @dataProvider provideData
     *
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int       $votesMaxAmount
     * @param float     $minimumVotesPercentage
     * @param bool      $couldBeTaken
     * @param array     $votes
     * @param mixed     $finalOption
     */
    public function testStrategy(
        \DateTime $startDate,
        \DateTime $endDate,
        int $votesMaxAmount,
        float $minimumVotesPercentage,
        bool $couldBeTaken,
        array $votes,
        $finalOption
    )
    {
        $chainedStrategy = new ChainedStrategy();
        $chainedStrategy->addStrategy(new PercentageStrategy($votesMaxAmount, $minimumVotesPercentage));
        $chainedStrategy->addStrategy(new WithTimePeriodStrategy(
            $startDate,
            $endDate
        ));

        $this->assertSame($couldBeTaken, $chainedStrategy->couldBeTaken(new VotingProcess(), $votes));
        $this->assertSame($finalOption,  $chainedStrategy->getFinalOption($votes));
    }
}