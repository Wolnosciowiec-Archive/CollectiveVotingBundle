<?php

namespace CollectiveVotingBundle\Manager;

use CollectiveVotingBundle\DecisionMaker\DecisionMaker;
use CollectiveVotingBundle\Entity\Vote;
use CollectiveVotingBundle\Factory\VotingProcess\VotingProcessFactory;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
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
    // when voting for yes|no then it means approved
    const DECISION_PASSED          = 'passed';

    // declined
    const DECISION_DECLINED        = 'declined';

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
     * Get existing process or start a new one
     *
     * @param CollectiveVotingSubjectInterface $object
     * @param UserInterface $contextUser
     *
     * @throws \Exception
     * @return VotingProcess
     */
    public function getProcess($object, UserInterface $contextUser)
    {
        $factory = $this->factory->getEntityProcessFactory($object);
        $process = $factory->constructProcess($object);

        if (!$process instanceof VotingProcess) {
            $process = $factory->createProcess($object, $contextUser, true);
        }

        return $process;
    }

    /**
     * Participate in a Voting Process
     * ===============================
     *
     * @param VotingProcess $process
     * @param UserInterface $user
     * @param bool|int $voteOption
     *
     * @return array
     */
    public function vote(VotingProcess $process, UserInterface $user, $voteOption)
    {
        $vote = $this->getUserVote($process, $user);

        if (!$vote instanceof Vote) {
            $vote = new Vote();
            $vote->setVoter($user);
            $vote->setVotingProcess($process);
            $vote->setDateAdded(new \DateTime('now'));
        }

        $vote->setVoteOption((bool)$voteOption ? 1 : 0);
        $this->em->persist($vote);
        $this->em->flush($vote);

        if ($this->decisionMaker->couldBeTaken($process)) {
            $decision = $this->decisionMaker->decide($process, DecisionMaker::STATE_READY) ? self::DECISION_PASSED : self::DECISION_DECLINED;

            // apply the decision (declined or approved)
            $process->setState($decision === self::DECISION_DECLINED ? VotingProcess::STATE_CLOSED_DECLINED : VotingProcess::STATE_CLOSED_APPROVED);
            $this->em->flush($process);
        }
        else {
            $decision = $this->decisionMaker->decide($process, DecisionMaker::STATE_NOT_READY) ? self::DECISION_CHANGES_APPLIED : self::DECISION_PENDING;
        }

        return [
            'vote' => $vote,
            'decision' => $decision,
        ];
    }

    /**
     * Get user vote
     * =============
     *
     * @param VotingProcess $process
     * @param UserInterface $user
     *
     * @return Vote
     */
    public function getUserVote(VotingProcess $process, UserInterface $user)
    {
        return $this->voteRepository->findOneBy([
            'votingProcess' => $process,
            'voter'         => $user,
        ]);
    }
}