<?php

namespace App\Controller;

use App\Repository\BankAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BankAccountController extends AbstractController
{
    #[Route('/api/bankAccount/', name: 'app_bankAccount', methods: ['GET'])]
    public function getAll(BankAccountRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $bankAccountList = $repository->findAll();
        $jsonBankAccount = $serializer->serialize($bankAccountList, 'json', ['groups'=>'getBanks']);
        return new JsonResponse($jsonBankAccount, Response::HTTP_OK, [], true);
    }

    #[Route('/api/bankAccount/{id}', name: 'app_bankAccount_id', methods: ['GET'])]
    public function getById(int $id, BankAccountRepository $repository, SerializerInterface $serializer):JsonResponse
    {
        $bankAccount = $repository->find($id);
        if ($bankAccount) {
            $jsonBankAccount = $serializer->serialize($bankAccount, 'json',['groups'=>'getBanks']);
            return new JsonResponse($jsonBankAccount,Response::HTTP_OK, [], true);
        }
        return new JsonResponse(['message'=>'bank not found'], Response::HTTP_NOT_FOUND);
    }



}
