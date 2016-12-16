<?php

namespace Wolnosciowiec\CollectiveVotingBundle\Tests\Functional;

use CollectiveVotingBundle\DecisionMaker\Strategy\MinimumVotesStrategy;
use CollectiveVotingBundle\Entity\VotingParticipant;
use CollectiveVotingBundle\Manager\VotingManager;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use CollectiveVotingBundle\Model\Entity\Tests\Subject;
use CollectiveVotingBundle\Tests\AbstractBaseProcessTest;
use CollectiveVotingBundle\Tests\Data\SubjectFactory;
use CollectiveVotingBundle\Tests\Data\SubjectProcessor;
use Doctrine\ORM\EntityManager;
use Wolnosciowiec\AppBundle\Tests\ContainerAwareTestCase; // @todo: Move to the bundle

/**
 * @package Wolnosciowiec\CollectiveVotingBundle\Tests\Functional
 */
class CreationAndApprovalTest extends AbstractBaseProcessTest
{
    /**
     * Vote multiple times for same option by the same participant
     */
    public function testVotingMultipleTimesForSameOption()
    {
        $vp = $this->createProcess();

        // vote 3 times for same option
        for ($i = 1; $i <= 3; $i++) {
            $this->getManager()->vote($vp, $vp->getStartedByUser(), 'for');
            $this->getPersistanceLayer()->persist($vp);
            $this->getPersistanceLayer()->flush($vp);
        }

        $this->assertSame(['for' => 1], $vp->getVotesCount());
    }

    /**
     * Functionally create a process, add some votes,
     * verify the state, add some more votes and verify the result
     */
    public function testDecisionMaking()
    {
        $vp       = $this->createProcess();
        $vp->setDecisionStrategy((new MinimumVotesStrategy())->setMinimumRequiredVotesAmount(3));
        $decision = null;

        for ($i = 1; $i <= 4; $i++) {
            $user = $this->createUser($i);

            $voteOptionName = in_array($i, [1, 2]) ? 'against' : 'for';
            $decision = $this->getManager()->vote($vp, $user, $voteOptionName);

            $this->assertFalse($decision->isClosingTheVoting());
            $this->assertNull($decision->getFinalOption());
        }

        // last vote (cannot do it inside of the loop, as there are asserts that would not work on last level)
        $user = $this->createUser($i);
        $decision = $this->getManager()->vote($vp, $user, 'for');

        // first option
        $this->assertArrayHasKey('for', $vp->getVotesCount());
        $this->assertSame(3, $vp->getVotesCount()['for']);

        // second option
        $this->assertArrayHasKey('against', $vp->getVotesCount());
        $this->assertSame(2, $vp->getVotesCount()['against']);

        // result of the voting
        $this->assertTrue($decision->isClosingTheVoting());
        $this->assertSame('for', $decision->getFinalOption());
    }
}