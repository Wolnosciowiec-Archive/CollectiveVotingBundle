<?php

namespace CollectiveVotingBundle\Entity;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;

/**
 * @package Wolnosciowiec\CollectiveVotingBundle\Entity
 */
class VotingParticipant implements VotingParticipantInterface
{
    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param int $id
     * @return VotingParticipant
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $username
     * @return VotingParticipant
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }
}