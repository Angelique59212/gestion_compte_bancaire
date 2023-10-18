<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $em->persist($customer);
        $em->flush();

        $jsonCustomer = $serializer->serialize($customer, 'json');
        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }

    #[Route('/api/customer/{id}', name: 'app_customer_delete', methods: ['DELETE'])]
    public function deleteCustomer(int $id, CustomerRepository $repository, EntityManagerInterface $em):JsonResponse
    {
        $customer = $repository->find($id);
        if ($customer) {
            $em->remove($customer);
            $em->flush();
            return new JsonResponse(['message'=>'customer delete'], Response::HTTP_ACCEPTED);
        }
        return new JsonResponse(['message'=>'customer not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/customer/{id}', name: 'app_customer_update', methods: ['PUT'])]
    public function updateCustomer(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, Customer $currentCustomer = null): JsonResponse
    {
        if ($currentCustomer instanceof Customer) {
            $updateCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCustomer]);

            $em->persist($updateCustomer);
            $em->flush();
            return new JsonResponse(['message' => 'customer update'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'customer not found'], Response::HTTP_NOT_FOUND);

    }
}
