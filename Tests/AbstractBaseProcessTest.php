<?php

namespace CollectiveVotingBundle\Tests;

use CollectiveVotingBundle\Entity\VotingParticipant;
use CollectiveVotingBundle\Model\Entity\VotingParticipantInterface;
use CollectiveVotingBundle\Manager\VotingManager;
use CollectiveVotingBundle\Model\Entity\Tests\Subject;
use CollectiveVotingBundle\Tests\Data\SubjectFactory;
use CollectiveVotingBundle\Tests\Data\SubjectProcessor;
use Doctrine\ORM\EntityManager;
use Wolnosciowiec\AppBundle\Tests\ContainerAwareTestCase;

/**
 * @package CollectiveVotingBundle\Tests
 */
abstract class AbstractBaseProcessTest extends ContainerAwareTestCase
{
    protected function prepareTestData()
    {
        $this->container->set(
            'collectivevoting.factory.collectivevotingbundle.model.entity.tests.subject',
            new SubjectFactory($this->container->get('doctrine.orm.entity_manager'))
        );

        $this->container->set(
            'collectivevoting.processor.collectivevotingbundle.model.entity.tests.subject',
            new SubjectProcessor()
        );
    }

    /**
     * @return VotingManager
     */
    protected function getManager()
    {
        return $this->container->get('collectivevoting.manager');
    }

    /**
     * @return VotingParticipantInterface
     */
    protected function getTestUser()
    {
        return (new VotingParticipant())
            ->setUsername('test-person');
    }

    /**
     * @param int $num
     * @return VotingParticipant
     */
    protected function createUser(int $num)
    {
        return (new VotingParticipant())
            ->setUsername('Test: ' . $num);
    }

    /**
     * @return EntityManager
     */
    protected function getPersistanceLayer()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * Create a process
     */
    public function createProcess()
    {
        $user = $this->getTestUser();
        $this->getPersistanceLayer()->persist($user);

        $this->prepareTestData();
        $process = $this->getManager()->getFactory()->getProcess((new Subject())->setId(161), $user);

        return $process;
    }
}