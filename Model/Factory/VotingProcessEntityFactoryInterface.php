<?php

namespace CollectiveVotingBundle\Model\Factory;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;

/**
 * VotingProcessEntityFactoryInterface
 * ===================================
 *
 * @package CollectiveVotingBundle\Model\Factory
 */
interface VotingProcessEntityFactoryInterface
{
    const EXCEPTION_SOURCE_ENTITY_NO_LONGER_AVAILABLE = 553;

    /**
     * Get source entity that is a subject in Voting Process
     * =====================================================
     *
     * @param VotingProcess $vp
     * @return CollectiveVotingSubjectInterface|null
     */
    public function getSourceEntity(VotingProcess $vp);

    /**
     * @param object $entity
     * @return array
     */
    public function getOriginalEntityData($entity): array;

    /**
     * @param CollectiveVotingSubjectInterface $object
     * @return VotingProcess
     */
    public function constructProcess(CollectiveVotingSubjectInterface $object);

    /**
     * Return a strategy for this voting process type
     *
     * @return DecisionMakerInterface
     */
    public function constructStrategy(): DecisionMakerInterface;

    /**
     * @param CollectiveVotingSubjectInterface $object
     * @param VotingParticipantInterface $user
     * @param bool $persist
     *
     * @return VotingProcess
     */
    public function createNewProcess(
        CollectiveVotingSubjectInterface $object,
        VotingParticipantInterface $user,
        $persist = false
    );
}