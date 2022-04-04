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
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use App\Repository\UnlockGamesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

class PutUserController extends AbstractController
{


    public function __construct(private Security $security, private EntityManagerInterface $em)
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

                $content = json_decode($request->getContent(), true);
                if (empty($content['username'])) {
                    return $this->json_response('400', 'username required');
                } elseif (empty($content['email'])) {
                    return $this->json_response('400', 'email required');
                }elseif (empty($content['name'])) {
                    return $this->json_response('400', 'name required');
                } elseif (empty($content['firstname'])) {
                    return $this->json_response('400', 'firstname required');
                } else {
                    $user->setEmail($content['email']);
                    $user->setUsername($content['username']);
                    $user->setName($content['name']);
                    $user->setFirstName($content['firstname']);
                    $user->setUpdatedAt(new DateTime());
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
                            "response" => "user has been updated",

                        ];
                        $data = new JsonResponse($response, '200');
                        return $data;
                    }
                }
            }
        }
    }
}
