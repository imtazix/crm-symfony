<?php

namespace App\Service\User;

use App\Entity\User;

interface UserServiceInterface
{
    public function login(string $identifiant, string $password): ?User;
}
