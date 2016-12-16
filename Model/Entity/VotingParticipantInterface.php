<?php

namespace CollectiveVotingBundle\Model\Entity;

interface VotingParticipantInterface
{
    /**
     * @return string|int
     */
    public function getUsername();

    /**
     * @return string|int
     */
    public function getId();
}