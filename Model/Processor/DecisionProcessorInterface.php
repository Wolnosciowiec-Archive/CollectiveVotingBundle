<?php

namespace CollectiveVotingBundle\Model\Processor;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;

/**
 * DecisionProcessorInterface
 * ==========================
 *   Process decision eg. publish an article
 *
 * @package CollectiveVotingBundle\Model\Processor
 */
interface DecisionProcessorInterface
{
    // reset process eg. after entity update
    const DECISION_RESET_PROCESS = 1;

    // entity update is allowed
    const DECISION_UPDATE_PERMITTED = 2;

    // positive decision
    const DECISION_PROCESSED = 3;

    /**
     * @param VotingProcess $votingProcess
     * @param array $votesCount
     * @param CollectiveVotingSubjectInterface $entity
     * @param array $originalEntityData
     * @param mixed $finalOption
     *
     * @return int
     */
    public function processDecision(
        VotingProcess $votingProcess,
        $votesCount,
        CollectiveVotingSubjectInterface $entity,
        array $originalEntityData,
        $finalOption
    ) : int;

    /**
     * When decision could not be yet made
     * but the state of the object could be
     * for example restored if necessary
     * ====================================
     *
     * @param VotingProcess $votingProcess
     * @param array $votesCount
     * @param CollectiveVotingSubjectInterface $entity
     * @param string $state
     * @param array $originalEntityData
     *
     * @return int Returns the decision
     */
    public function processNotReadyState(
        VotingProcess $votingProcess,
        $votesCount,
        CollectiveVotingSubjectInterface $entity,
        $state,
        array $originalEntityData
    ) : int;
}