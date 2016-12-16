<?php

namespace CollectiveVotingBundle\Model\DecisionMaker;
use CollectiveVotingBundle\Entity\VotingProcess;

/**
 * DecisionMakerInterface
 * ======================
 *
 * @package CollectiveVotingBundle\Model\DecisionMaker
 */
interface DecisionMakerInterface
{
    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, array $votesCount);

    /**
     * @param array         $votesCount
     * @return mixed
     */
    public function getFinalOption($votesCount);
}