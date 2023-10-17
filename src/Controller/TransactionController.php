<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\BankAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TransactionController extends AbstractController
{
    #[Route('/api/transactions/deposit', name: 'app_transaction_deposit')]
    public function deposit(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, BankAccountRepository $repository): JsonResponse
    {
        $content = $request->toArray();
        $bankId = $content['idBankAccount'] ?? -1;
        $bank = $repository->find($bankId);

        if ($bank) {
            $deposit = $serializer->deserialize($request->getContent(), Transaction::class, 'json');

            if ($deposit->getAmount() > 0) {
                $deposit->setDate(new \DateTime());
                $deposit->setTypeTransaction('deposit');
                $deposit->setBanckAccount($bank);

                $current = $bank->getCurrentAccountBalance();
                $newBalance = $current + $deposit->getAmount();
                $bank->setCurrentAccountBalance($newBalance);

                $em->persist($deposit);
                $em->flush();

                $response = ['transaction' => $deposit, 'newAmount' => $newBalance . "€"];

                return new JsonResponse($serializer->serialize($response, 'json', ['groups' => 'getTransactions']), Response::HTTP_OK, [], true);
            }
        }

        return new JsonResponse(['message' => "deposit not found"], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/transactions/debit', name: 'app_transaction_debit')]
    public function debit(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, BankAccountRepository $repository): JsonResponse
    {
        $content = $request->toArray();
        $bankId = $content['idBankAccount'] ?? -1;
        $bank = $repository->find($bankId);

        if ($bank) {
            $debit = $serializer->deserialize($request->getContent(), Transaction::class, 'json');

            if ($debit->getAmount() > 0) {
                $debit->setDate(new \DateTime());
                $debit->setTypeTransaction('debit');
                $debit->setBanckAccount($bank);

                $current = $bank->getCurrentAccountBalance();
                $newBalance = $current - $debit->getAmount();
                $bank->setCurrentAccountBalance($newBalance);

                $em->persist($debit);
                $em->flush();

                $response = ['transaction' => $debit, 'newAmount' => $newBalance . "€"];

                return new JsonResponse($serializer->serialize($response, 'json', ['groups' => 'getTransactions']), Response::HTTP_OK, [], true);
            }
        }

        return new JsonResponse(['message' => "debit not found"], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/transactions/payment', name: 'app_transaction_payment', methods: ['POST'])]
    public function payment(EntityManagerInterface $em, Request $request, SerializerInterface $serializer, BankAccountRepository $repository): JsonResponse
    {
        $content = $request->toArray();
        $bankAccountId = $content['idBankAccount'] ?? -1;
        $account = $repository->find($bankAccountId);

        if ($account) {
            $newBalance = null;
            $payment = $serializer->deserialize($request->getContent(), Transaction::class, 'json');

            if ($payment->getAmount() > 0) {
                $payment->setDate(new \DateTime());
                $payment->setTypeTransaction('payment');
                $payment->setBanckAccount($account);

                $currentBalance = $account->getCurrentAccountBalance();
                if ($currentBalance >= $payment->getAmount()){
                    $newBalance = $currentBalance - $payment->getAmount();
                    $account->setCurrentAccountBalance($newBalance);
                    $em->persist($payment);
                    $em->flush();
                }

                if ($newBalance !== null) {
                    return new JsonResponse(['message' => "Virement effectué", 'newAmount' => $newBalance ."€"], Response::HTTP_OK);
                }
            }
        }

        return new JsonResponse(['message' => "Virement non effectué"], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/transactions/saving', name: 'app_transactions_saving', methods: ['GET'])]
    public function getAccountSaving(Request $request, BankAccountRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $content = $request->toArray();
        $id = $content['idBankAccount'] ?? -1;
        $account = $repository->find($id);

        if ($account->getAccountType() === 'epargne') {
            $currentBalance = $account->getCurrentAccountBalance();
            $interestRate = $account->getInterestRate();

            $saving = ($account->getCurrentAccountBalance() + ($currentBalance * $interestRate) / 100);
            $savingAccount = $serializer->serialize($saving, 'json');
            return new JsonResponse(['message'=> 'Epargne Validé, nouveau montant: ' .$savingAccount . "€"], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => "Epargne non trouvé"], Response::HTTP_NOT_FOUND);
    }

}