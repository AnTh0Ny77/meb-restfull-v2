<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use App\Entity\UnlockGames;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestRepository;
use App\Repository\QrCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UnlockGamesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UpdatePasswordController extends AbstractController
{


    public function __construct(private Security $security, private EntityManagerInterface $em , private UserPasswordHasherInterface $passwordHasher)
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



    public function __invoke(Request $request, ValidatorInterface $validator, UserRepository $ur, UnlockGamesRepository $urRep, UploaderHelper $helper)
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }

        $user = $ur->findOneBy(array('username' => $user->username));
        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {
            if ($user->getConfirmed() != true) {
                return $this->json_response('400', 'user need to be confirmed , see: api/user/guest/confirm');
            } else {
                $subject = $request->attributes->get('data');
                if ($subject !=  $user) {
                    return $this->json_response('403', 'cannot handle other user ');
                } else {
                    $content = json_decode($request->getContent(), true);
                    $plain_password = $content['password'];
                    $old_password = $content['actual_password'];
                    $passwordValid = $this->passwordHasher->isPasswordValid($user, $old_password); 
                    if ($passwordValid == false) {
                        return $this->json_response('400', 'invalid credentials');
                    }
                    if ($old_password === $plain_password ){
                        return $this->json_response('400', 'need to be different');
                    }
                    $user->setPlainPassword($plain_password);
                    $errors = $validator->validate($user);
                    if (count($errors) > 0) {
                        $errorsString = (string) $errors;
                        $response = [
                            "error" => $errorsString,
                        ];
                        $data = new JsonResponse($response, '401');
                        return $data;
                    } else {
                        $hashedPassword = $this->passwordHasher->hashPassword($user, $plain_password);
                        $user->setPassword($hashedPassword);
                        $user->setUpdatedAt(new DateTime());
                        $this->em->persist($user);
                        $this->em->flush();
                        $response = [
                            "message" => "password updated",

                        ];
                        $data = new JsonResponse($response, '200');
                        return $data; 
                    }
                }
                
            }
            
    }
    

    }
}