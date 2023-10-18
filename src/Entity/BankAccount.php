<?php

namespace App\Entity;

use App\Repository\BankAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BankAccountRepository::class)]
class BankAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $accountNumber = null;

    #[ORM\Column(length: 8)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $accountType = null;

    #[ORM\Column]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?int $currentAccountBalance = null;

    #[ORM\Column]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?bool $overdraft = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?int $interestRate = null;

    #[ORM\ManyToOne(inversedBy: 'bankAccounts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['getBanks'])]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'banckAccount', targetEntity: Transaction::class)]
    #[Groups(['getBanks'])]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getInterestRate(): ?int
    {
        return $this->interestRate;
    }

    public function setInterestRate(?int $interestRate): static
    {
        $this->interestRate = $interestRate;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    public function setAccountType(string $accountType): static
    {
        $this->accountType = $accountType;

        return $this;
    }

    public function getCurrentAccountBalance(): ?int
    {
        return $this->currentAccountBalance;
    }

    public function setCurrentAccountBalance(int $currentAccountBalance): static
    {
        $this->currentAccountBalance = $currentAccountBalance;

        return $this;
    }

    public function isOverdraft(): ?bool
    {
        return $this->overdraft;
    }

    public function setOverdraft(bool $overdraft): static
    {
        $this->overdraft = $overdraft;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setBanckAccount($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getBanckAccount() === $this) {
                $transaction->setBanckAccount(null);
            }
        }

        return $this;
    }
}
