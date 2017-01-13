<?php

namespace CollectiveVotingBundle\Tests\Manager;

use CollectiveVotingBundle\Entity\Vote;
use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Entity\Tests\Subject;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use CollectiveVotingBundle\Tests\AbstractBaseProcessTest;

/**
 * @see VotingManager
 * @package CollectiveVotingBundle\Tests\Manager
 */
class VotingManagerTest extends AbstractBaseProcessTest
{
    /**
     * @var VotingParticipantInterface $testUser
     */
    private $testUser;

    public function setUp()
    {
        parent::setUp();

        $this->prepareTestData();

        $this->testUser = $this->getTestUser();
        $this->getPersistanceLayer()->persist($this->testUser);
    }

    /**
     * @see VotingProcessFactory::getProcess()
     */
    public function testGetProcess()
    {
        $subject = new Subject();
        $subject->setId(123);

        $this->assertInstanceOf(
            VotingProcess::class,
            $this->getManager()->getFactory()->getProcess($subject, $this->testUser)
        );
    }

    /**
     * @see VotingManager::vote()
     * @see VotingManager::getVotes()
     * @see VotingManager::getUserVote()
     */
    public function testVote()
    {
        $this->prepareTestData();

        $subject = new Subject();
        $subject->setId(123);

        $process = $this->getManager()->getFactory()->getProcess($subject, $this->testUser);

        // add a vote
        $this->getManager()->vote($process, $this->testUser, 'anarchosyndycalism');
        $this->getPersistanceLayer()->persist($process);
        $this->getPersistanceLayer()->flush($process);

        // verify if the count of votes matches
        $this->assertCount(1, $process->getVotes());
        $this->assertSame(['anarchosyndycalism' => 1], $process->getVotesCount());

        // and verify the vote that we just added
        $this->assertSame('anarchosyndycalism', $process->getVotes()[0]->getVoteOption());

        // getUserVote()
        $this->assertInstanceOf(Vote::class, $this->getManager()->getUserVote($process, $this->testUser));
    }
}