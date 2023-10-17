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
    #[Route('/api/transaction/deposit', name: 'app_transaction')]
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

                $response = ['transaction' => $deposit, 'newAmount' => $newBalance];

                return new JsonResponse($serializer->serialize($response, 'json', ['groups' => 'getTransactions']), Response::HTTP_OK, [], true);
            }
        }

        return new JsonResponse(['message' => "deposit not found"], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/transaction/debit', name: 'app_transaction_debit')]
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

                $response = ['transaction' => $debit, 'newAmount' => $newBalance];

                return new JsonResponse($serializer->serialize($response, 'json', ['groups' => 'getTransactions']), Response::HTTP_OK, [], true);
            }
        }

        return new JsonResponse(['message' => "debit not found"], Response::HTTP_BAD_REQUEST);
    }
}