<?php

namespace CollectiveVotingBundle\DecisionMaker\Strategy;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;

/**
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class PercentageStrategy extends CommonStrategy implements DecisionMakerInterface
{
    /**
     * @var int $maxPossibleVotesAmount
     */
    private $maxPossibleVotesAmount;

    /**
     * @var float $minimumVotesPercent
     */
    private $minimumVotesPercent;

    /**
     * @param int   $maxPossibleVotesAmount
     * @param float $minimumVotesPercent
     */
    public function __construct(int $maxPossibleVotesAmount, float $minimumVotesPercent)
    {
        $this->maxPossibleVotesAmount = $maxPossibleVotesAmount;
        $this->minimumVotesPercent    = $minimumVotesPercent;
    }

    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, array $votesCount)
    {
        $finalOption = null;

        try {
            $finalOption = $this->getFinalOption($votesCount);
        }
        catch (AmbiguousResultException $e) {
            return false;
        }

        return ($votesCount[$finalOption] / $this->maxPossibleVotesAmount) * 100 >= $this->minimumVotesPercent;
    }
}