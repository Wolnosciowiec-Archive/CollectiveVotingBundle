<?php

namespace CollectiveVotingBundle\Entity;

use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * VotingProcess
 * =============
 *
 * @package CollectiveVotingBundle\Entity
 */
class VotingProcess
{
    const STATE_OPEN   = 'open';
    const STATE_CLOSED = 'closed';

    /**
     * @var int $id
     */
    protected $id;

    /**
     * @var string $subjectType
     */
    protected $subjectType;

    /**
     * @var int|string $subjectId
     */
    protected $subjectId;

    /**
     * @var Vote[] $votes
     */
    protected $votes = [];

    /**
     * Current state the process is in
     * ===============================
     *   Available states are in class constants
     *   prefixed by STATE_
     *
     * @var string $state
     */
    protected $state = self::STATE_OPEN;

    /**
     * @var \DateTime $dateAdded
     */
    protected $dateAdded;

    /**
     * @var string|null $decisionStrategyName
     */
    protected $decisionStrategyName;

    /**
     * @var UserInterface $startedByUser
     */
    protected $startedByUser;

    /**
     * @var DecisionMakerInterface|null $decisionStrategy
     */
    protected $decisionStrategy;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    /**
     * @return VotingParticipantInterface
     */
    public function getStartedByUser()
    {
        return $this->startedByUser;
    }

    /**
     * @param VotingParticipantInterface $startedByUser
     * @return $this
     */
    public function setStartedByUser($startedByUser)
    {
        $this->startedByUser = $startedByUser;
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
     * @return Vote[]|ArrayCollection
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote
     * @return $this
     */
    public function addVote(Vote $vote)
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
        }

        $this->votes->add($vote);

        return $this;
    }

    /**
     * @param Vote[] $votes
     * @return $this
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @param int|string $subjectId
     * @return $this
     */
    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
        return $this;
    }

    /**
     * @param bool $asServiceName
     * @return string
     */
    public function getSubjectType($asServiceName = false)
    {
        if ($asServiceName) {
            return strtolower(str_replace('\\', '.', $this->subjectType));
        }

        return $this->subjectType;
    }

    /**
     * @param string $subjectType
     * @return $this
     */
    public function setSubjectType($subjectType)
    {
        $this->subjectType = $subjectType;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return $this->state === self::STATE_CLOSED;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        if (!in_array($state, [
            self::STATE_CLOSED,
            self::STATE_OPEN,
        ]))
        {
            throw new \InvalidArgumentException('Invalid value for $state, values are defined '
                . ' in class constants in prefix STATE_, actual: ' . $state);
        }

        $this->state = $state;
        return $this;
    }

    /**
     * @return $this
     */
    public function resetState()
    {
        $this->setState(self::STATE_OPEN);

        foreach ($this->votes as $vote) {
            $vote->setValid(false);
        }

        return $this;
    }

    /**
     * Get votes count
     * ===============
     *   vote_for: 1
     *   vote_against: 2
     *   my_option_name: 3
     *
     * @return array
     */
    public function getVotesCount()
    {
        $votes = [];

        foreach ($this->getVotes() as $vote) {

            if (!$vote->isValid()) {
                continue;
            }

            if (!isset($votes[$vote->getVoteOption()])) {
                $votes[$vote->getVoteOption()] = 0;
            }

            $votes[$vote->getVoteOption()]++;
        }

        return $votes;
    }

    /**
     * @param null|string $decisionStrategyName
     * @return VotingProcess
     */
    public function setDecisionStrategyName($decisionStrategyName)
    {
        $this->decisionStrategyName = $decisionStrategyName;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDecisionStrategyName()
    {
        return $this->decisionStrategyName;
    }

    /**
     * @return DecisionMakerInterface
     */
    public function getDecisionStrategy()
    {
        return $this->decisionStrategy;
    }

    /**
     * Filled by the process factory
     *
     * @param DecisionMakerInterface|null $decisionStrategy
     * @return VotingProcess
     */
    public function setDecisionStrategy($decisionStrategy)
    {
        $this->decisionStrategy = $decisionStrategy;
        return $this;
    }
}