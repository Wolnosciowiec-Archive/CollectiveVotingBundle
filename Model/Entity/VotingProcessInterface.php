<?php

namespace CollectiveVotingBundle\Model\Entity;

interface VotingProcessInterface
{
    /**
     * @return VotingParticipantInterface
     */
    public function getStartedByUser();

    /**
     * @param VotingParticipantInterface $startedByUser
     * @return $this
     */
    public function setStartedByUser($startedByUser);

    /**
     * @return \DateTime
     */
    public function getDateAdded();

    /**
     * @param \DateTime $dateAdded
     * @return $this
     */
    public function setDateAdded($dateAdded);

    /**
     * @return Vote[]
     */
    public function getVotes();

    /**
     * @param Vote[] $votes
     * @return $this
     */
    public function setVotes($votes);

    /**
     * @return int|string
     */
    public function getSubjectId();

    /**
     * @param int|string $subjectId
     * @return $this
     */
    public function setSubjectId($subjectId);

    /**
     * @param bool $asServiceName
     * @return string
     */
    public function getSubjectType($asServiceName = false);

    /**
     * @param string $subjectType
     * @return $this
     */
    public function setSubjectType($subjectType);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isClosed();

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     * @return $this
     */
    public function setState($state);

    /**
     * @return $this
     */
    public function resetState();

    /**
     * Get votes count
     * ===============
     *   vote_for: 1
     *   vote_against: 2
     *   my_option_name: 3
     *
     * @return array
     */
    public function getVotesCount();
}