<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\SecurityController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use DateTime;

class ConfirmGuestController extends AbstractController
{

    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $em  , Security $security )
    {
        $this->ur = $userRepository;
        $this->security = $security;
    }

   

    public function __invoke(Request $request, ValidatorInterface $validator, UserPasswordHasherInterface $hasher)
    {


        $user = $this->security->getUser();
        $identifier = $request->attributes->get('data');
        $content = json_decode($request->getContent(), true);
        dd($user);


    }
}
