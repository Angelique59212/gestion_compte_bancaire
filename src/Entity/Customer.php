<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $name = null;

    #[ORM\Column(length: 70)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getCustomers', 'getBanks'])]
    private ?string $mail = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: BankAccount::class)]
    #[Groups(['getCustomers'])]
    private Collection $bankAccounts;

    public function __construct()
    {
        $this->bankAccounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection<int, BankAccount>
     */
    public function getBankAccounts(): Collection
    {
        return $this->bankAccounts;
    }

    public function addBankAccount(BankAccount $bankAccount): static
    {
        if (!$this->bankAccounts->contains($bankAccount)) {
            $this->bankAccounts->add($bankAccount);
            $bankAccount->setCustomer($this);
        }

        return $this;
    }

    public function removeBankAccount(BankAccount $bankAccount): static
    {
        if ($this->bankAccounts->removeElement($bankAccount)) {
            // set the owning side to null (unless already changed)
            if ($bankAccount->getCustomer() === $this) {
                $bankAccount->setCustomer(null);
            }
        }

        return $this;
    }
}
