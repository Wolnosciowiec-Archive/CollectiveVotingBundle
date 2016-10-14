<?php

namespace CollectiveVotingBundle\Model\Factory;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @param CollectiveVotingSubjectInterface $object
     * @return VotingProcess
     */
    public function constructProcess(CollectiveVotingSubjectInterface $object);

    /**
     * @param CollectiveVotingSubjectInterface $object
     * @param UserInterface $user
     * @param bool $persist
     *
     * @return VotingProcess
     */
    public function createProcess(CollectiveVotingSubjectInterface $object, UserInterface $user, $persist = false);
}