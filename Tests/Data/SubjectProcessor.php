<?php

namespace CollectiveVotingBundle\Tests\Data;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Model\Entity\Tests\Subject;
use CollectiveVotingBundle\Model\Processor\DecisionProcessorInterface;

/**
 * @package CollectiveVotingBundle\Tests\Data
 */
class SubjectProcessor implements DecisionProcessorInterface
{
    /**
     * @param VotingProcess $votingProcess
     * @param array $votesCount
     * @param CollectiveVotingSubjectInterface|Subject $entity
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
    ) : int
    {
        /** @var Subject $entity */
        $entity->setApproved($finalOption === 'for');

        return self::DECISION_PROCESSED;
    }

    /**
     * @param VotingProcess $votingProcess
     * @param array $votesCount
     * @param CollectiveVotingSubjectInterface|Subject $entity
     * @param string $state
     * @param array $originalEntityData
     *
     * @return int
     */
    public function processNotReadyState(
        VotingProcess $votingProcess,
        $votesCount,
        CollectiveVotingSubjectInterface $entity,
        $state,
        array $originalEntityData
    ) : int
    {
        if ($entity->isApproved()) {
            $entity->setApproved(false);
            return self::DECISION_RESET_PROCESS;
        }

        return self::DECISION_UPDATE_PERMITTED;
    }
}