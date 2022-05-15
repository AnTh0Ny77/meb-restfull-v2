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

    public function json_response(string $code, string $message)
    {
        $response = [
            "error" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }


    public function __invoke(Request $request, ValidatorInterface $validator, UserRepository $ur,  UserPasswordHasherInterface $hasher)
    {

        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }
        $user = $ur->findOneBy(array('username' => $user->username));

        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {
            $content = json_decode($request->getContent(), true);
            
            if (empty($content['username'])) {
                return $this->json_response('400', 'username required');
            }
            elseif (empty($content['email'])) {
                return $this->json_response('400', 'email required');
            }
            elseif (empty($content['password'])) {
                return $this->json_response('400', 'password required');
            }
            elseif (empty($content['name'])) {
                return $this->json_response('400', 'name required');
            }
            elseif (empty($content['firstname'])) {
                return $this->json_response('400', 'firstname required');
            }else{
                $user->setEmail($content['email']);
                $user->setUsername($content['username']);
                $user->setName($content['name']);
                $user->setPlainPassword($content['password']);
                $user->setFirstName($content['firstname']);
                $user->setConfirmed(1);
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    $response = [
                        "error" => $errorsString,

                    ];
                    $data = new JsonResponse($response, '400');
                    return $data;
                }else{
                    $pass = $hasher->hashPassword($user, $user->getPlainPassword());
                    $user->setPassword($pass);
                    $this->em->persist($user);
                    $this->em->flush();
                    $response = [
                        "response" => "user has been updated",

                    ];
                    $data = new JsonResponse($response, '200');
                    return $data;
                }
               
            }
        }
        
       


    }
}
