Collective Voting Bundle
========================

Allows to create voting for an action, so every allowed person
could vote on specific action.

There is a possibility to create multiple decision strategies, basing on known data like
votes count and some minimum/maximum percentage of votes.

Creating a vote subject
=======================

A subject must implement the `CollectiveVotingSubjectInterface`, so it could be just a class, or your entity that implements this interface.

Besides the subject class you would also need to create:

1. Factory  (will be constructing subjects while having only the voting process object)
2. Processor (process the vote, if passed then for example publish the post)
3. Describer (describe how to display the object in the approval dashboard)


# Step 1: Factory

You need to register a new factory class that will construct your subject objects.

Example service name: `collectivevoting.factory.appbundle.entity.content.page`
Every service has to have the prefix of `collectivevoting.factory.`, the name is arbitrary.

If you will be using entity as a subject, then *there is a ready to use base class* for entities,
so you don't have to implement it by your own.

```php
<?php

namespace YourApp\AppBundle;

class PageVotingProcessFactory extends AbstractBaseVotingProcessFactory
    implements VotingProcessEntityFactoryInterface { };
```

# Step 2: Decision processor

Decision processor will execute a proper action you want to execute right after
the voting will close.

For example you want to publish the post when the majority will tell that it should be published.

Namespace of the service should be: `collectivevoting.processor.{{ your name here }}`

Example of processor:

```php
<?php

namespace Wolnosciowiec\AppBundle\Service\Processor\CollectiveVoting;

use CollectiveVotingBundle\Model\Processor\DecisionProcessorInterface;
use CollectiveVotingBundle\Model\Entity\CollectiveVotingSubjectInterface;
use CollectiveVotingBundle\Entity\VotingProcess;
use Wolnosciowiec\AppBundle\Model\Entity\Content\Page;

/**
 * Page Decision Processor
 * =======================
 *   When voting will end and decision will be made
 *   then processDecision() will be ran
 *
 * @package Wolnosciowiec\AppBundle\Service\Processing\CollectiveVoting
 */
class PageDecisionProcessor implements DecisionProcessorInterface
{
    /**
     * @param VotingProcess $votingProcess
     * @param array $votesCount
     * @param CollectiveVotingSubjectInterface|Page $entity
     * @param array $originalEntityData
     * @param mixed $finalOption
     *
     * @return bool
     */
    public function processDecision(
        VotingProcess $votingProcess,
        $votesCount,
        CollectiveVotingSubjectInterface $entity,
        array $originalEntityData,
        $finalOption
    )
    {
        /** @var Page $entity */
        $entity->setPublished($finalOption === 'votes_for');

        return true;
    }

    /**
     * @param VotingProcess $votingProcess
     * @param array $votesCount
     * @param CollectiveVotingSubjectInterface|Page $entity
     * @param string $state
     * @param array $originalEntityData
     *
     * @return bool
     */
    public function processNotReadyState(
        VotingProcess $votingProcess,
        $votesCount,
        CollectiveVotingSubjectInterface $entity,
        $state,
        array $originalEntityData
    )
    {
        if ($entity->isPublished() && $this->isChanged($originalEntityData, $entity)) {
            $entity->setPublished(false);
            return false;
        }

        return true;
    }

    /**
     * @param array $originalEntityData
     * @param Page $entity
     * @return bool
     */
    private function isChanged($originalEntityData, Page $entity)
    {
        $dataSet = [
            'title'     => $entity->getTitle(),
            'content'   => $entity->getContent(),
            'published' => $entity->isPublished(),
            'urlName'  => $entity->getUrlName(),
        ];

        foreach ($dataSet as $field => $newValue) {
            if ($originalEntityData[$field] != $newValue) {
                return true;
            }
        }

        return false;
    }
}
```


Permissions
===========

- skipCollectiveVote: User can simply end voting and accept or decline the proposition. This still allows voting, but when saving the subject object its possible
  to activate it (for example if the approval is for its activation)
- collectiveVote: Possibility for user to vote on any action

Todo
====
- Unit tests
- Documentation
- Examples