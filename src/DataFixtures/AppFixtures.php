<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $customer = [];
        for ($i = 0 ; $i <5 ; $i++) {
            $customer[$i] = new Customer();
            $customer[$i]->setName($faker->lastName());
            $customer[$i]->setFirstname($faker->firstName());
            $customer[$i]->setAddress($faker->address());
            $customer[$i]->setMail($faker->email());
            $customer[$i]->setPhoneNumber($faker->phoneNumber());

            $manager->persist($customer[$i]);
        }

        $bankAccount = [];
        for ($i = 0; $i < 5 ; $i++) {
            $bankAccount[$i] = new BankAccount();
            $bankAccount[$i]->setAccountNumber($faker->creditCardNumber());
            $bankAccount[$i]->setAccountType($faker->randomElement(['courant', 'epargne']));
            $bankAccount[$i]->setCurrentAccountBalance(rand(0,5000));
            $bankAccount[$i]->setOverdraft($faker->boolean());
            if ($bankAccount[$i]->getAccountType() === 'epargne') {
                $bankAccount[$i]->setInterestRate($faker->numberBetween(1, 15));
            }
            $bankAccount[$i]->setCustomer($customer[array_rand($customer)]);

            $manager->persist($bankAccount[$i]);
        }


        $manager->flush();
    }
}
