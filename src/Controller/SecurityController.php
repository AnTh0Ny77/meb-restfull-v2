<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;

class SecurityController extends AbstractController
{


    #[Route(path: "/api/login", name: "api_login", methods: ['POST'])]
    public function login()
    {
        $User = $this->getUser();
        return $this->json([
            "username" => $User->getUsername(),
            'roles' => $User->getRoles()
        ]);
    }

    #[Route(path: "/login_guest", name: "login_guest")]
    public function check()
    {
        throw new \LogicException('This code should never be reached');
    }

    #[Route(path: "/login_link", name: "login_link")]
    public function loginLink(UserRepository $userRepository , $username , LoginLinkHandlerInterface  $loginLinkHandler )
    {
            $user = $userRepository->findOneBy(['username' => $username]);
           
            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
       
            $loginLink = $loginLinkDetails->getUrl();
       
            $response = [
                "link" => $loginLink
            ];
            $data = new JsonResponse($response, '201');
           
            return  $data;
    }
}
