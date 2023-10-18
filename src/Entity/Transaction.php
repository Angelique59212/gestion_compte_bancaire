<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getTransactions','getBanks'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['getTransactions','getBanks'])]
    private ?int $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getTransactions','getBanks'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 20)]
    #[Groups(['getTransactions','getBanks'])]
    private ?string $typeTransaction = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    private ?BankAccount $banckAccount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTypeTransaction(): ?string
    {
        return $this->typeTransaction;
    }

    public function setTypeTransaction(string $typeTransaction): static
    {
        $this->typeTransaction = $typeTransaction;

        return $this;
    }

    public function getBanckAccount(): ?BankAccount
    {
        return $this->banckAccount;
    }

    public function setBanckAccount(?BankAccount $banckAccount): static
    {
        $this->banckAccount = $banckAccount;

        return $this;
    }
}
