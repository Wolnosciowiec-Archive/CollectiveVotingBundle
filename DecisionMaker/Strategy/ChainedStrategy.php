<?php declare(strict_types=1);

namespace CollectiveVotingBundle\DecisionMaker\Strategy;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use Wolnosciowiec\CollectiveVotingBundle\Model\Exception\AmbiguousResultException;

/**
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class ChainedStrategy implements DecisionMakerInterface
{
    /**
     * @var DecisionMakerInterface[] $strategies
     */
    private $strategies;

    /**
     * @param DecisionMakerInterface $strategy
     * @return $this
     */
    public function addStrategy(DecisionMakerInterface $strategy)
    {
        $this->strategies[] = $strategy;
        return $this;
    }

    /**
     * @param DecisionMakerInterface $strategy
     * @return $this
     */
    public function removeStrategy(DecisionMakerInterface $strategy)
    {
        $search = array_search($strategy, $this->strategies);

        if ($search !== false) {
            unset($this->strategies[$search]);
        }

        return $this;
    }

    /**
     * @return \CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface[]
     */
    public function getStrategies()
    {
        return $this->strategies;
    }

    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, array $votesCount)
    {
        foreach ($this->getStrategies() as $strategy) {
            if ($strategy->couldBeTaken($process, $votesCount) !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $votesCount
     * @throws AmbiguousResultException
     * @return mixed
     */
    public function getFinalOption($votesCount)
    {
        $options = [];

        foreach ($this->getStrategies() as $strategy) {
            try {
                $options[] = $strategy->getFinalOption($votesCount);
            }
            catch (AmbiguousResultException $e) { };
        }

        // allow null values
        $options = array_filter($options, function ($value) { return $value !== null; });

        if (count(array_unique($options)) !== 1) {
            throw new AmbiguousResultException('Some strategies are giving different results');
        }

        return current($options);
    }

}