<?php

namespace CollectiveVotingBundle\DecisionMaker\Strategy;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;

/**
 * MinimumVotesStrategy
 * ====================
 *
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class MinimumVotesStrategy implements DecisionMakerInterface
{
    const MINIMUM_VOTES = 3;

    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, $votesCount)
    {
        if ($votesCount['votes_for'] === $votesCount['votes_against']) {
            return false;
        }

        return $votesCount['votes_for'] >= self::MINIMUM_VOTES
                || $votesCount['votes_against'] >= self::MINIMUM_VOTES;
    }
}