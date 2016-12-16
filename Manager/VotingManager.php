<?php

namespace CollectiveVotingBundle\Manager;

use CollectiveVotingBundle\DecisionMaker\DecisionMaker;
use CollectiveVotingBundle\Entity\Decision;
use CollectiveVotingBundle\Entity\Vote;
use CollectiveVotingBundle\Factory\VotingProcess\VotingProcessFactory;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use CollectiveVotingBundle\Model\Processor\DecisionProcessorInterface;
use Doctrine\ORM\EntityManager;
use CollectiveVotingBundle\Entity\VotingProcess;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * VotingManager
 * =============
 *  Manages the whole process of voting
 *
 * @package CollectiveVotingBundle\Manager
 */
class VotingManager
{
    // when the process is going to be normally finished
    const DECISION_FINISHED        = 'approved';

    // subject entity was changed
    const DECISION_CHANGES_APPLIED = 'updated';

    // still pending a decision
    const DECISION_PENDING         = 'pending';

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var VotingProcessFactory $factory
     */
    protected $factory;

    /**
     * @var \Doctrine\ORM\EntityRepository $voteRepository
     */
    protected $voteRepository;

    /**
     * @var DecisionMaker $decisionMaker
     */
    protected $decisionMaker;

    /**
     * @param EntityManager $em
     * @param VotingProcessFactory $factory
     * @param DecisionMaker $decisionMaker
     */
    public function __construct(EntityManager $em, VotingProcessFactory $factory, DecisionMaker $decisionMaker)
    {
        $this->em = $em;
        $this->voteRepository = $em->getRepository('CollectiveVotingBundle:Vote');
        $this->factory = $factory;
        $this->decisionMaker = $decisionMaker;
    }

    /**
     * Participate in a Voting Process
     * ===============================
     *
     * @param VotingProcess              $process
     * @param VotingParticipantInterface $user
     * @param string                     $voteOption
     *
     * @return Decision
     */
    public function vote(VotingProcess $process,
                         VotingParticipantInterface $user,
                         $voteOption)
    {
        $this->reopenTheProcess($process);
        $vote = $this->getUserVote($process, $user, false);

        if (!$vote instanceof Vote) {
            $vote = new Vote();
            $vote->setVoter($user);
            $vote->setVotingProcess($process);
        }

        $vote->setValid(true);
        $vote->setVoteOption($voteOption);
        $vote->setDateAdded(new \DateTime('now'));
        $process->addVote($vote);

        return $this->processDecision($process);
    }

    /**
     * Reopen the process when it's closed, but we are taking an action
     *
     * @param VotingProcess $process
     */
    public function reopenTheProcess(VotingProcess $process)
    {
        // re-open the closed process, remove old votes
        if ($process->isClosed()) {
            $process->resetState();
        }
    }

    /**
     * Urgently finish the voting process with a decision
     * ==================================================
     *
     * Example usage cases:
     *   - Users are trying to vote up an article that is breaking the statute (eg. sympathizing with political party shit)
     *   - Users with public access are able to vote on article, when collective notices enough votes, then they could
     *     instantly approve the right option
     *
     * @param VotingProcess              $process
     * @param VotingParticipantInterface $user
     * @param mixed                      $voteOption
     *
     * @return Decision
     */
    public function finishWithOption(
        VotingProcess $process,
        VotingParticipantInterface $user,
        $voteOption)
    {
        $this->vote($process, $user, $voteOption);
        return $this->processDecision($process, $voteOption);
    }

    /**
     * Update the process by verifying if decision
     * could be made, should be called after voting or creation of the object
     * (vote method is calling it automatically right after vote)
     *
     * @param VotingProcess $process
     * @param mixed|null    $forceCloseProcessWithOption
     * @return Decision
     */
    public function processDecision(VotingProcess $process, $forceCloseProcessWithOption = null)
    {
        $couldBeTaken = $this->decisionMaker->couldBeTaken($process);

        // decide, take action
        $decision = $this->decisionMaker->decide(
            $process,
            ($couldBeTaken || $forceCloseProcessWithOption) ? DecisionMaker::STATE_READY : DecisionMaker::STATE_NOT_READY,
            $forceCloseProcessWithOption
        );

        if ($couldBeTaken || $forceCloseProcessWithOption) {

            // update the status of the process
            $decision->getDecision() === DecisionProcessorInterface::DECISION_PROCESSED || $forceCloseProcessWithOption
                ? $this->closeTheVoting($process)
                : $this->keepVotingOpen($process);
        }

        // when the decision cannot be made because the subject was changed
        if ($decision->getDecision() === DecisionProcessorInterface::DECISION_RESET_PROCESS
            && !$forceCloseProcessWithOption) {
            $process->resetState();
        }

        return $decision;
    }

    /**
     * @param VotingProcess $process
     * @return VotingProcess
     */
    protected function closeTheVoting(VotingProcess $process)
    {
        $process->setState(VotingProcess::STATE_CLOSED);

        foreach ($process->getVotes() as $vote) {
            $vote->setValid(false);
        }

        return $process;
    }

    /**
     * @param VotingProcess $process
     */
    protected function keepVotingOpen(VotingProcess $process)
    {
        $process->setState(VotingProcess::STATE_OPEN);
    }

    /**
     * Get user vote
     * =============
     *
     * @param VotingProcess              $process
     * @param VotingParticipantInterface $user
     * @param bool                       $activeOnly
     *
     * @return Vote|null
     */
    public function getUserVote(
        VotingProcess $process,
        VotingParticipantInterface $user,
        $activeOnly = true)
    {
        foreach ($process->getVotes() as $vote) {

            if ($activeOnly === true && !$vote->isValid()) {
                continue;
            }

            if ($vote->getVoter()->getUsername() === $user->getUsername()) {
                return $vote;
            }
        }

        return null;
    }

    /**
     * @param VotingProcess $process
     */
    public function save(VotingProcess $process)
    {
        $this->em->persist($process);

        foreach ($process->getVotes() as $vote) {
            $this->em->persist($vote);
        }

        $this->em->flush();
    }

    /**
     * @return VotingProcessFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
}