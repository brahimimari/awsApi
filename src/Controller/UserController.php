<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    private $manager;

    private $user;

    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }


    //Add user
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        return new Response('Hello Word ');
    }

    //Add user
    #[Route('/userCreate', name: 'user_create', methods: 'POST')]
    public function userCreate(Request $request): Response
    {
        $data  = json_decode($request->getContent(), true);

        $email = $data["email"];
        $password = $data["password"];
        $email_existe = $this->user->findOneByEmail($email);
        if ($email_existe) {
            return new jsonResponse(
                [
                    'status' => false,
                    'message' => 'Cet email existe deja'
                ]
            );
        } else {
            $user = new User();
            $user->setEmail($email);
            $user->setPassword(sha1($password));
            $this->manager->persist($user);

            $this->manager->flush();
            return new jsonResponse(
                [
                    'status' => true,
                    'message' => 'L\'utilisateur créé avec succès'
                ]
            );
        }
    }

    //Liste des utilisateurs
    #[Route('/api/getAllUsers', name: 'get_allusers', methods: 'GET')]
    public function getAllUsers(): Response
    {
        $users = $this->user->findAll();
        return $this->json(
            $users,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }
}
