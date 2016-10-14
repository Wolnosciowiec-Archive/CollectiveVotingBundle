<?php

namespace CollectiveVotingBundle\ViewTransformer;

use CollectiveVotingBundle\Entity\VotingProcess;
use CollectiveVotingBundle\Factory\DescriberFactory;
use CollectiveVotingBundle\Factory\VotingProcess\VotingProcessFactory;
use CollectiveVotingBundle\Model\Factory\VotingProcessEntityFactoryInterface;
use CollectiveVotingBundle\Model\Transformer\TransformerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Voting Process Notification Transformer
 * =======================================
 *   In short words - formats data for frontend
 *
 * @package CollectiveVotingBundle\Transformer
 */
class VotingProcessTransformer implements TransformerInterface
{
    /**
     * @var DescriberFactory $describerFactory
     */
    private $describerFactory;

    /**
     * @var VotingProcessFactory $vpFactory
     */
    private $vpFactory;

    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var AuthorizationCheckerInterface $authChecker
     */
    private $authChecker;

    /**
     * @param DescriberFactory $describerFactory
     * @param VotingProcessFactory $vpFactory
     * @param Router $router
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(
        DescriberFactory $describerFactory,
        VotingProcessFactory $vpFactory,
        Router $router,
        AuthorizationCheckerInterface $authChecker
    )
    {
        $this->describerFactory = $describerFactory;
        $this->vpFactory        = $vpFactory;
        $this->router           = $router;
        $this->authChecker      = $authChecker;
    }

    /**
     * @param VotingProcess[] $votingProcesses
     * @param Request $request
     *
     * @return array|void
     */
    public function transformToArray($votingProcesses, Request $request)
    {
        $output = [];

        foreach ($votingProcesses as $votingProcess) {

            // construct subject entity
            try {
                $entity = $this->vpFactory
                    ->getProcessFactory($votingProcess)
                    ->getSourceEntity($votingProcess);
            }
            catch (\LogicException $e) {
                if ($e->getCode() === VotingProcessEntityFactoryInterface::EXCEPTION_SOURCE_ENTITY_NO_LONGER_AVAILABLE) {
                    continue;
                }

                throw $e;
            }

            $describer = $this->describerFactory
                ->getEntityDescriber($votingProcess);


            // route for displaying a correct navigation link
            $route = $describer->getRouteAndParams($entity);
            $route['params']['domain'] = $request->getHost();

            // permissions
            $permissions = $describer->getPermissionParams($entity);

            if ($permissions
                && !$this->authChecker->isGranted($permissions['permission'], $permissions['subject'])) {
                continue;
            }

            // output array
            $output[] = [
                // title
                'title' => $describer->getTitle($entity),

                // dynamic link constructed by router
                'link' => $this->router->generate($route['route'], $route['params']),

                // id
                'id' => $votingProcess->getId(),

                // user
                'user' => $votingProcess->getStartedByUser(),

                // date
                'date' => $votingProcess->getDateAdded(),

                // type
                'type' => $votingProcess->getSubjectType(),

                // votes count
                'votes_count' => $votingProcess->getVotesCount(),

                // votes
                'votes' => $votingProcess->getVotes(),

                // description
                'description' => $describer->getDescription($entity),
            ];
        }

        return $output;
    }
}