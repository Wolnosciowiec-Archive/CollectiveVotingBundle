<?php

namespace CollectiveVotingBundle\Entity;
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
     * @param UserInterface $voter
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
}