<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Repository\BankAccountRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    #[Route('/api/bankAccount/add', name: 'app_bankAccount_add', methods: ['POST'])]
    public function addBankAccount(CustomerRepository $repository,Request $request, EntityManagerInterface $em, SerializerInterface $serializer):JsonResponse
    {
        $bank = $serializer->deserialize($request->getContent(), BankAccount::class, 'json');
        $content = $request->toArray();
        $customerId = $content['idCustomer'] ?? -1;
        $customer = $repository->find($customerId);
        if ($customer) {
            $bank->setCustomer($customer);

            $em->persist($bank);
            $em->flush();
            return new JsonResponse($serializer->serialize($bank, 'json', ['groups' => 'getBanks']), Response::HTTP_CREATED, [], true);
        }
        return new JsonResponse(['message' => "bank not found"], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/bankAccount/{id}', name: 'app_bankAccount_delete', methods: ['DELETE'])]
    public function deleteBankAccount(int $id, BankAccountRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $bank = $repository->find($id);
        if ($bank) {
            $em->remove($bank);
            $em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['message' =>'bank not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/bankAccount/{id}', name: 'app_bankAccount_update', methods: ['PUT'])]
    public function updateBank(Request $request, SerializerInterface $serializer, BankAccount $currentBank, EntityManagerInterface $em): JsonResponse
    {
        $updateBank = $serializer->deserialize($request->getContent(), BankAccount::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE=>$currentBank]);
        $em->persist($updateBank);
        $em->flush();
        return new JsonResponse(['message'=>'bank update'], Response::HTTP_OK);
    }

    #[Route('api/bankAccount/searchCustomer/{name}', name: 'app_book_searchCustomer', methods: ['GET'])]
    public function searchBankAccount(string $name, BankAccountRepository $repository, SerializerInterface $serializer):JsonResponse
    {
        $names = $repository->findName($name);
        $jsonNames = $serializer->serialize($names, 'json', ['groups' => 'getCustomers']);

        return new JsonResponse($jsonNames, Response::HTTP_OK, [], true);
    }
}
