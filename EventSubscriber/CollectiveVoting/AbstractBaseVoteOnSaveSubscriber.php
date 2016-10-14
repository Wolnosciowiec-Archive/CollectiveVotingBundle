<?php

namespace CollectiveVotingBundle\EventSubscriber\CollectiveVoting;

use Wolnosciowiec\AppBundle\Event\EventDispatcher\AppEvent;
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
     * @param AppEvent $event
     * @return bool
     */
    public function prePersistCreateVoting(AppEvent $event)
    {
        /** @var CollectiveVotingSubjectInterface $subject */
        $subject = $event->getSubject();

        if (!$subject->getId()) {
            $this->em->persist($subject);
            $this->em->flush($subject);
        }

        // don't trigger the process if the post is not going to be published
        if (!$subject->isVotingSatisfied()) {
            return false;
        }

        // voting is off when:
        // 1) we are not able to vote
        // 2) we have a possibility to take action (eg. publish posts)

        if ($this->getPermissionName()
            && $this->authChecker->isGranted($this->getPermissionName(), $subject)) {
            return false;
        }

        if (!$this->authChecker->isGranted('collectiveVote', $subject)) {
            return false;
        }

        // create voting process
        $process = $this->votingManager->getProcess(
            $subject,
            $event->getContextUser()
        );

        // adds first vote
        $this->votingManager->vote($process, $event->getContextUser(), true);
        $event->setResultStatus(true);

        return true;
    }
}