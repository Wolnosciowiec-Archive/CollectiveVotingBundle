<?php

namespace CollectiveVotingBundle\Tests\DecisionMaker\Strategy;

use CollectiveVotingBundle\DecisionMaker\Strategy\WithDurationStrategy;
use CollectiveVotingBundle\Entity\Vote;
use CollectiveVotingBundle\Entity\VotingProcess;

/**
 * @see WithDurationStrategy
 * @package CollectiveVotingBundle\Tests\DecisionMaker\Strategy
 */
class WithDurationStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function provideData()
    {
        return [
            'Success, the date already finished' => [
                (new VotingProcess())
                    ->addVote((new Vote())->setVoteOption('for')->setDateAdded((new \DateTime('now'))->modify('-14 days')))
                    ->addVote((new Vote())->setVoteOption('for')->setDateAdded((new \DateTime('now'))->modify('-14 days'))),
                ['for' => 2],
                true,
            ],

            'Failure, the voting is not ready for the decision yet' => [
                (new VotingProcess())
                    ->addVote((new Vote())->setVoteOption('for')->setDateAdded((new \DateTime('now'))->modify('+1 days')))
                    ->addVote((new Vote())->setVoteOption('for')->setDateAdded((new \DateTime('now'))->modify('+2 days'))),
                ['for' => 2],
                false,
            ],
        ];
    }

    /**
     * @dataProvider provideData
     *
     * @param VotingProcess $process
     * @param $votesCount
     * @param bool $expected
     */
    public function testCouldBeTaken(VotingProcess $process, $votesCount, bool $expected)
    {
        $strategy = new WithDurationStrategy(60 * 60 * 24 * 7);
        $this->assertSame($expected, $strategy->couldBeTaken($process, $votesCount));
    }
}