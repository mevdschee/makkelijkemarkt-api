<?php
namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccountData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_SENIOR'];

        for ($i = 0; $i < 9; $i++) {
            $min = sprintf('%02d', $i);

            $account = new Account();
            $account->setNaam("Account$i");
            $account->setEmail("account$i@amsterdam.nl");
            $account->setUsername("account$i@amsterdam.nl");
            $account->setPassword(password_hash("Password$i!", PASSWORD_DEFAULT));
            $account->setRole($roles[$i % count($roles)]);
            $account->setAttempts(0);
            $account->setLastAttempt(new \DateTime("2020-01-01 00:$min:00"));
            $account->setLocked($i % 2 == 0);
            $account->setActive($i < 6);
            $manager->persist($account);
        }

        $manager->flush();

    }
}
