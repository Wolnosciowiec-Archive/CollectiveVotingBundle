<?php declare(strict_types=1);

namespace CollectiveVotingBundle\DecisionMaker\Strategy;
use CollectiveVotingBundle\Entity\Vote;
use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\DecisionMaker\DecisionMakerInterface;
use Wolnosciowiec\CollectiveVotingBundle\Model\Exception\AmbiguousResultException;

/**
 * @package CollectiveVotingBundle\DecisionMaker\Strategy
 */
class WithDurationStrategy implements DecisionMakerInterface
{
    /**
     * @var int $maxSeconds
     */
    private $maxSeconds = 60 * 60 * 24 * 7; // 7 days

    /**
     * @param int $maxSeconds
     */
    public function __construct(int $maxSeconds)
    {
        $this->maxSeconds = $maxSeconds;
    }

    /**
     * @param VotingProcess $process
     * @return int
     */
    private function getFirstVoteDate(VotingProcess $process)
    {
        $dates = array_map(function (Vote $vote) {

            if (!$vote->isValid()) {
                return null;
            }

            return $vote->getDateAdded()->getTimestamp();
        }, $process->getVotes()->toArray());

        return min($dates);
    }

    /**
     * @return int
     */
    public function getMaxSeconds()
    {
        return $this->maxSeconds;
    }

    /**
     * @param VotingProcess $process
     * @param array $votesCount
     *
     * @return bool
     */
    public function couldBeTaken(VotingProcess $process, array $votesCount)
    {
        // after the end date we are able to finally decide
        return time() >= ($this->getFirstVoteDate($process) + $this->getMaxSeconds());
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
            'but to add a duration condition to the voting');
    }
}