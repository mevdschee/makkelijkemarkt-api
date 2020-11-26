<?php
namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountData extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_SENIOR'];

        for ($i = 0; $i < 9; $i++) {
            $min = sprintf('%02d', $i);

            $account = new Account();
            $account->setNaam("Account$i");
            $account->setEmail("account$i@amsterdam.nl");
            $account->setUsername("account$i@amsterdam.nl");
            $account->setPassword($this->passwordEncoder->encodePassword($account, "Password$i!"));
            $account->setRole($roles[$i % count($roles)]);
            $account->setAttempts($i % 5);
            $account->setLastAttempt(new \DateTime("2020-01-01 00:$min:00"));
            $account->setLocked($i % 2 == 0);
            $account->setActive($i < 6);
            $manager->persist($account);
        }

        $manager->flush();

    }
}
