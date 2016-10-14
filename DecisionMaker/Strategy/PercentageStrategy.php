<?php

namespace CollectiveVotingBundle\DecisionMaker\Strategy;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;

/**
 * PercentageStrategy
 * ==================
 *
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class PercentageStrategy implements DecisionMakerInterface
{
    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, $votesCount)
    {
        // minimum positive votes count
        if ($votesCount['votes_for'] < 5) {
            return false;
        }

        return $votesCount['votes_for'] * 100 / ($votesCount['votes_for'] + $votesCount['votes_against']) >= 75;
    }
}