<?php

namespace CollectiveVotingBundle\Model\Event;

use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;

/**
 * @package Wolnosciowiec\CollectiveVotingBundle\Model\Event
 */
interface CollectiveVotingEventInterface
{
    /**
     * @return VotingParticipantInterface
     */
    public function getContextUser();

    /**
     * @return CollectiveVotingSubjectInterface
     */
    public function getSubject();

    /**
     * @return bool
     */
    public function getResultStatus(): bool;

    /**
     * @param bool $status
     * @return $this
     */
    public function setResultStatus(bool $status);
}