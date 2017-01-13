<?php

namespace CollectiveVotingBundle\DecisionMaker\Strategy;
use Wolnosciowiec\CollectiveVotingBundle\Model\Exception\AmbiguousResultException;

/**
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
abstract class CommonStrategy
{
    /**
     * @param array $votesCount
     *
     * @throws AmbiguousResultException
     * @return mixed
     */
    public function getFinalOption($votesCount)
    {
        $max       = null;
        $maxOption = null;
        $maxDupes  = [];

        foreach ($votesCount as $option => $amount) {
            if ($max < $amount) {
                $max       = $amount;
                $maxOption = $option;
                $maxDupes  = [];
            }
            elseif ($max === $amount) {
                $maxDupes[] = $option;
            }
        }

        if (count($maxDupes) > 0) {
            throw new AmbiguousResultException(
                'There are more results that have the same amount of votes: ' . implode(', ', $maxDupes)
            );
        }

        return $maxOption;
    }
}