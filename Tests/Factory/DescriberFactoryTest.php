<?php

namespace CollectiveVotingBundle\Tests\Factory;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Model\Entity\Tests\Subject;
use CollectiveVotingBundle\Tests\Data\SubjectDescriber;
use Wolnosciowiec\AppBundle\Tests\ContainerAwareTestCase;

/**
 * @see DescriberFactory
 * @package CollectiveVotingBundle\Tests\Factory
 */
class DescriberFactoryTest extends ContainerAwareTestCase
{
    /**
     * @return SubjectDescriber
     */
    private function mockDescriberFactory()
    {
        return $this->container->set(
            'collectivevoting.describer.test.subject',
            new SubjectDescriber()
        );
    }

    /**
     * @see DescriberFactory::getEntityDescriber()
     */
    public function testGetEntityDescriber()
    {
        $this->mockDescriberFactory();

        $vp = (new VotingProcess())
            ->setSubjectType('Test\\Subject');

        $describer = $this->container->get('collectivevoting.factory.describer')
            ->getEntityDescriber($vp);

        $this->assertNotEmpty($describer->getDescription(new Subject()));
    }
}