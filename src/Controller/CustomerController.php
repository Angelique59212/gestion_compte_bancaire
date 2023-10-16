<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerController extends AbstractController
{
    #[Route('/api/customer', name: 'app_customer',methods: ['GET'])]
    public function getAll(CustomerRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $customerList = $repository->findAll();
        $jsonCustomerList = $serializer->serialize($customerList, 'json',['groups' =>'getCustomers']);

        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/customer/{id}', name: 'app_customer_id', methods: ['GET'])]
    public function getById(int $id, CustomerRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $customer = $repository->find($id);
        if ($customer) {
            $jsonCustomer = $serializer->serialize($customer,'json',['groups' =>'getCustomers']);

            return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);

        }
        return new JsonResponse(['message'=>'customer not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/customer/add', name: 'app_customer_add', methods: ['POST'])]
    public function addCustomer(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {

    }
}
