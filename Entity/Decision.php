<?php declare(strict_types=1);

namespace CollectiveVotingBundle\Entity;
use CollectiveVotingBundle\Model\Processor\DecisionProcessorInterface;

/**
 * End information after the decision about voting is made
 *
 * @see DecisionProcessorInterface
 * @package CollectiveVotingBundle\Entity
 */
class Decision
{
    /**
     * One of DecisionProcessorInterface::DECISION_*
     *
     * @var int $decision
     */
    private $decision;

    /**
     * Option that comrades selected
     *
     * @var mixed $option
     */
    private $finalOption;

    public function __construct(int $decision, $finalOption)
    {
        $this->decision    = $decision;
        $this->finalOption = $finalOption;
    }

    /**
     * @return int
     */
    public function getDecision(): int
    {
        return $this->decision;
    }

    /**
     * @return mixed
     */
    public function getFinalOption()
    {
        return $this->finalOption;
    }

    /**
     * @return bool
     */
    public function isClosingTheVoting()
    {
        return $this->decision === DecisionProcessorInterface::DECISION_PROCESSED;
    }
}