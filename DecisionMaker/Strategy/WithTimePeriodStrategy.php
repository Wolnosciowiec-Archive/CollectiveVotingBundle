<?php

namespace CollectiveVotingBundle\DecisionMaker\Strategy;
use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use Wolnosciowiec\CollectiveVotingBundle\Model\Exception\AmbiguousResultException;

/**
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class WithTimePeriodStrategy implements DecisionMakerInterface
{
    /**
     * @var \DateTime $startDate
     */
    private $startDate;

    /**
     * @var \DateTime $endDate
     */
    private $endDate;

    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     */
    public function __construct(\DateTime $startDate, \DateTime $endDate)
    {
        if ($endDate->getTimestamp() <= $startDate->getTimestamp()) {
            throw new \InvalidArgumentException(
                'Invalid $startDate and $endDate. ' .
                'Dates cannot be equal, and the start should begin before end');
        }

        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    /**
     * @param \DateTime $startDate
     * @return $this
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     * @return $this
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, array $votesCount)
    {
        if ($this->getStartDate()->getTimestamp() > time()) {
            return false;
        }

        // after the voting will end we are able to decide the result
        return time() >= $this->getEndDate()->getTimestamp();
    }

    /**
     * @param array $votesCount
     * @throws AmbiguousResultException
     * @return void
     */
    public function getFinalOption($votesCount)
    {
        throw new AmbiguousResultException(
            'This strategy is not able to find a vote, ' .
            'but to add a time condition to the voting');
    }
}