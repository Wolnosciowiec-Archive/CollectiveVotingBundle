<?php

namespace CollectiveVotingBundle\EventSubscriber\CollectiveVoting;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Event\CollectiveVotingEventInterface;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Manager\VotingManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Doctrine\ORM\EntityManager;

/**
 * Event listener on entity save
 * ====================================
 *   Adds support for collective voting to entity publishing
 *   by creating a collective approval process
 *
 * @package PostBundle\EventSubscriber\CollectiveVoting
 */
abstract class AbstractBaseVoteOnSaveSubscriber implements EventSubscriberInterface
{
    /**
     * @var VotingManager $votingManager
     */
    private $votingManager;

    /**
     * @var AuthorizationChecker $checker
     */
    private $authChecker;

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @param VotingManager $vm
     * @param AuthorizationChecker $checker
     * @param EntityManager $em
     */
    public function __construct(VotingManager $vm, AuthorizationChecker $checker, EntityManager $em)
    {
        $this->votingManager = $vm;
        $this->authChecker   = $checker;
        $this->em            = $em;
    }

    /**
     * Get permission that will be
     * skipping the voting process and for
     * example publishing entity immediately
     * =====================================
     *
     * Example: 'publishPage'
     *
     * @return string
     */
    abstract public function getPermissionName();

    /**
     * Get value that needs to be set when the voting will finish
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @return mixed
     */
    abstract public function getVotedOption($subject);

    /**
     * Could the voting proceed? Did'nt we cancel it?
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @return bool
     */
    abstract protected function isVotingSatisfied(CollectiveVotingSubjectInterface $subject): bool;

    /**
     * Constructs the process
     * Could be used to inject the strategy
     *
     * @param CollectiveVotingSubjectInterface $subject
     * @param CollectiveVotingEventInterface   $event
     * @return VotingProcess
     */
    protected function createProcess(CollectiveVotingSubjectInterface $subject, CollectiveVotingEventInterface $event): VotingProcess
    {
        return $this->votingManager->getFactory()->getProcess(
            $subject,
            $event->getContextUser()
        );
    }

    /**
     * @param CollectiveVotingEventInterface $event
     * @return bool
     */
    public function prePersistCreateVoting(CollectiveVotingEventInterface $event)
    {
        /** @var CollectiveVotingSubjectInterface $subject */
        $subject = $event->getSubject();

        if (!$subject->getId()) {
            $this->em->persist($subject);
            $this->em->flush($subject);
        }

        // create voting process
        $process = $this->createProcess($subject, $event);

        // if the user don't need to vote (the politics could be that for example
        // only friends of an organization could vote)
        if ($this->authChecker->isGranted('skipCollectiveVote', $subject)
            && $subject->isVotingSatisfied()) {

            $this->votingManager->finishWithOption(
                $process,
                $event->getContextUser(),
                $this->getVotedOption($subject)
            );

            $event->setResultStatus(true);
            return false;
        }

        // 1) don't trigger the process if the post is not going to be published
        // case: when we uncheck that we want to publish the post for example
        // 2) we have don't a possibility to take action (eg. publish posts)
        // 3) we are not able to vote
        $conditions = [
            'isNotGoingToBeVoted' =>
                !$this->isVotingSatisfied($subject),
            'isUserNotAbleToVote' =>
                $this->getPermissionName()
                && !$this->authChecker->isGranted($this->getPermissionName(), $subject),
            'isNotAbleToVoteGenerally' =>
                !$this->authChecker->isGranted('collectiveVote', $subject),
        ];

        if ($conditions['isNotGoingToBeVoted']
            || $conditions['isUserNotAbleToVote']
            || $conditions['isNotAbleToVoteGenerally']) {

            $this->votingManager->processDecision($process);
            $this->votingManager->save($process);
            return false;
        }

        // adds a vote
        $this->votingManager->vote($process, $event->getContextUser(), $this->getVotedOption($subject));
        $event->setResultStatus(true);

        return true;
    }
}