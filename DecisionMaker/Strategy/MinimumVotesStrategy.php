<?php declare(strict_types=1);

namespace CollectiveVotingBundle\DecisionMaker\Strategy;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use Wolnosciowiec\CollectiveVotingBundle\Model\Exception\AmbiguousResultException;

/**
 * MinimumVotesStrategy
 * ====================
 *
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class MinimumVotesStrategy extends CommonStrategy implements DecisionMakerInterface
{
    /**
     * @var int $minimumVotesRequired
     */
    private $minimumVotesRequired = 3;

    /**
     * @param int $minimumVotesRequired
     */
    public function __construct(int $minimumVotesRequired)
    {
        $this->minimumVotesRequired = $minimumVotesRequired;
    }

    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, array $votesCount)
    {
        try {
            $this->getFinalOption($votesCount);
        } catch (AmbiguousResultException $e) {
            return false;
        }

        foreach ($votesCount as $count) {
            if ((int)$count >= $this->getMinimumRequiredVotesAmount()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    protected function getMinimumRequiredVotesAmount()
    {
        return $this->minimumVotesRequired;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setMinimumRequiredVotesAmount(int $amount)
    {
        $this->minimumVotesRequired = $amount;
        return $this;
    }
}