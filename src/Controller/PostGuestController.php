<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\SecurityController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use DateTime;

class PostGuestController extends AbstractController
{

    public function __construct( private UserRepository $userRepository , private EntityManagerInterface $em )
    {
        $this->ur = $userRepository;
    }

    public function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); 
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 12; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); 
    }

    public function __invoke(Request $request , ValidatorInterface $validator ,UserPasswordHasherInterface $hasher , LoginLinkHandlerInterface $loginLinkHandler){
        $content = json_decode($request->getContent(), true);
        if (!empty($content['key'])){
            $user = new User;
            $max = $this->ur->returnMaxGuest();
            if (!empty($max)) {
                $max = explode('_', $max["max"]);
                $max = intval($max[1] +1 );
                $guest_name = 'Guest_' . $max;
                $user->setUsername($guest_name);
                $date = new \DateTime("now");
                $user->setCreatedAt($date);
                $user->setConfirmed(0);
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    $response = [
                        "error" => $errorsString,

                    ];
                    $data = new JsonResponse($response, '401');
                    return $data;
                } else {
                    $plain_pass = $this->randomPassword();
                    $pass = $hasher->hashPassword($user, $plain_pass);
                    $user->setPassword($pass);
                    $this->em->persist($user);
                    $this->em->flush();
                    
                    $request_link = $this->forward('App\Controller\SecurityController::loginLink', [
                        'userRepository'  => $this->ur,
                        'username' => $guest_name,
                        'loginLinkHandler' => $loginLinkHandler
                    ]);
                    
                    $link = json_decode($request_link->getContent());
                    $link = $link->link;
                    $response = [
                        "message" => 'The guest has been created',
                        "user name" => $user->getUsername() , 
                        "link" => $link
                    ];
                    $data = new JsonResponse($response, '201');
                    return  $data;
                }
              
            }else{
                $response = [
                    "message" => 'unabeld to generate name guest name'
                ];
                $data = new JsonResponse($response, '401');
                return  $data;
            }  
        } else{
            $response = [
                "message" => 'wrong key'
            ];
            $data = new JsonResponse($response, '401');
            return  $data;
        }
    }

}

