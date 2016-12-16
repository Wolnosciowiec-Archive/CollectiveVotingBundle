<?php

namespace Wolnosciowiec\CollectiveVotingBundle\Tests\Data;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return 'Test';
    }

    public function getPassword()
    {
        return 'example-password';
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        return '';
    }
}