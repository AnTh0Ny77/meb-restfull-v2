<?php

namespace App\Controller;

use App\Entity\UnlockGames;
use App\Entity\User;
use App\Repository\QrCodeRepository;
use App\Repository\UnlockGamesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\RankRepository;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PostClientController extends AbstractController
{

    public function __construct(private Security $security, private EntityManagerInterface $em ,  private UserPasswordHasherInterface $passwordHasher)
    {
    }
    public function json_response(string $code, string $message)
    {
        $response = [
            "error" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }

    public function __invoke(UserRepository $ur , Request $request , RankRepository $rankRepository){

        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }
        $user = $ur->findOneBy(array('username' => $user->username));

        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {
            $content = json_decode($request->getContent(), true);
            if ($user->getId() != 914) {
                return $this->json_response('401', 'user not found');
            }

            $insert = new User();
            if (!empty($content['location'])) {
               
                $location = json_decode(str_replace("'", '"', $content['location']), true);
                if (is_array($location)) {
                    $insert->setLocation($location);
                } else {
                    $insert->setLocation([]);
                }
            } else {
                $insert->setLocation([]);
            }

            
            $username_test =  $ur->findOneBy(array('username' => $content['username']));
            if ($username_test instanceof User) {
                return $this->json_response('401', 'username déja utilisé');
            }


            $email_test =  $ur->findOneBy(array('email' => $content['email']));
            if ($email_test instanceof User) {
                return $this->json_response('401', 'mail déja utilisé');
            }
            
            $Rank = $rankRepository->findOneBy(['id' => 1 ]);
            
            $insert->setUsername($content['username']);
            $insert->setEmail($content['email']);
            
            $insert->setPhone($content['phone']);
            $insert->setConfirmed(1);
            $insert->setName('Client');
            $insert->setFirstName('Compte');
            $insert->setRank($Rank);
            $insert->setClientInfiniteQr(intval($content['type']));
            $insert->setBagNumber($content['bag']);
            $insert->setExploreCoin($content['exc']);
            $hashedPassword = $this->passwordHasher->hashPassword($insert,$content['password']);
            $insert->setPassword($hashedPassword);
            $insert->setRoles(['ROLE_CLIENT']);
            $this->em->persist($insert);
            $this->em->flush();

            $response = ["message" => $insert->getEmail()];
            $data = new JsonResponse($response, '200');
            return $data; 
        }
        


        
        return true; 
        
    }

}