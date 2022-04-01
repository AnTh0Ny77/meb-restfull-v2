<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use DateTime;

class PostGuestController extends AbstractController
{

    public function __construct( private UserRepository $userRepository , private EntityManagerInterface $em )
    {
        $this->ur = $userRepository;
    }

    public function __invoke(Request $request , ValidatorInterface $validator)
    {
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
                $errors = $validator->validate($user);
                if (count($errors) > 0) {
                    $errorsString = (string) $errors;
                    $response = [
                        "error" => $errorsString,

                    ];
                    $data = new JsonResponse($response, '401');
                    return $data;
                } else {
                    $this->em->persist($user);
                    $this->em->flush();
                    $response = [
                        "message" => 'The guest has been created',
                        "user name" => $user->getUsername()
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

