<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/', name: 'app_users')]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->getAllUsersData();

        return $this->render('users/users.html.twig', [
            'controller_name' => 'UsersController',
            'users' => $users
        ]);
    }

    #[Route('/top', name: 'app_top')]
    public function top(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->getInvitersTop();
        return $this->render('users/top.html.twig', [
            'controller_name' => 'UsersController',
            'users' => $users
        ]);
    }

}
