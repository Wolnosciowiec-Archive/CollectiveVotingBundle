<?php

namespace CollectiveVotingBundle\Entity;

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
    const STATE_OPEN = 'open';

    // boolean votings
    const STATE_CLOSED_DECLINED = 'declined';
    const STATE_CLOSED_APPROVED = 'approved';

    // string option voting
    const STATE_CLOSED_CLOSED   = 'closed';

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
    protected $votes;

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
     * @var UserInterface $startedByUser
     */
    protected $startedByUser;

    /**
     * @return UserInterface
     */
    public function getStartedByUser()
    {
        return $this->startedByUser;
    }

    /**
     * @param UserInterface $startedByUser
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
     * @return Vote[]
     */
    public function getVotes()
    {
        return $this->votes;
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
        return in_array($this->state, [
            self::STATE_CLOSED_APPROVED,
            self::STATE_CLOSED_DECLINED,
        ]);
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
            self::STATE_CLOSED_DECLINED,
            self::STATE_CLOSED_APPROVED,
            self::STATE_OPEN,
        ]))
        {
            throw new \InvalidArgumentException('Invalid value for $state, values are defined '
                . ' in class constants in prefix STATE_');
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
        return $this;
    }

    /**
     * Get votes count
     * ===============
     *   votes_for: 1
     *   votes_against: 2
     *   summary: 3
     *
     * @return array
     */
    public function getVotesCount()
    {
        $votes = [
            'votes_for'     => 0,
            'votes_against' => 0,
            'summary'       => 0,
        ];

        foreach ($this->getVotes() as $vote) {
            if ($vote->getVoteOption() == '1') {
                $votes['votes_for']++;
            }
            elseif ($vote->getVoteOption() == '0') {
                $votes['votes_against']++;
            }

            $votes['summary']++;
        }

        return $votes;
    }
}