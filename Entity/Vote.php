<?php

namespace CollectiveVotingBundle\Entity;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Vote
 * ====
 *
 * For mapping see: Vote.orm.yml
 *
 * @package CollectiveVotingBundle\Entity
 */
class Vote
{
    const NAME_APPROVE   = 'vote_for';
    const NAME_DECLINE   = 'vote_against';
    const NAME_UNDECIDED = 'vote_abstain';

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var UserInterface $voter
     */
    protected $voter;

    /**
     * @var VotingProcess $votingProcess
     */
    protected $votingProcess;

    /**
     * @var string $voteOption By default it should be for example "Y" or "N"
     */
    protected $voteOption;

    /**
     * @var \DateTime $dateAdded
     */
    protected $dateAdded;

    /**
     * @var bool $valid
     */
    protected $valid = true;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserInterface
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param VotingParticipantInterface $voter
     * @return $this
     */
    public function setVoter($voter)
    {
        $this->voter = $voter;
        return $this;
    }

    /**
     * @return VotingProcess
     */
    public function getVotingProcess()
    {
        return $this->votingProcess;
    }

    /**
     * @param VotingProcess $votingProcess
     * @return $this
     */
    public function setVotingProcess($votingProcess)
    {
        $this->votingProcess = $votingProcess;
        return $this;
    }

    /**
     * @return string
     */
    public function getVoteOption()
    {
        return $this->voteOption;
    }

    /**
     * @param string $voteOption
     * @return $this
     */
    public function setVoteOption($voteOption)
    {
        $this->voteOption = $voteOption;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param \DateTime $dateAdded
     * @return $this
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
        return $this;
    }

    /**
     * @param boolean $valid
     * @return Vote
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->valid;
    }
}