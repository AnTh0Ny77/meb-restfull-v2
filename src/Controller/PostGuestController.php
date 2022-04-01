<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use DateTime;

class PostGuestController extends AbstractController
{

    public function __construct(private Security $security, private UserRepository $userRepository , private EntityManagerInterface $em )
    {
        
    }

    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        if (!empty($content['key'])){
            $user = new User;
            $user->setUsername('GuestMyeb_12345');
            $date = new \DateTime("now");
            $user->setCreatedAt($date);
            $this->em->persist($user);
            $this->em->flush();
            $response = [
                "message" => 'The guest has been created',
                "user name" => $user->getUsername()
            ];
            $data = new JsonResponse($response, '201');
            return  $data; 
        } else{
            $response = [
                "message" => 'wrong key'
            ];
        }
    }

}

