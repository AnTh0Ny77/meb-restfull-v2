<?php

namespace App\Controller;

use DateTime;
use DateInterval;
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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

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



    public function __invoke(Request $request, ValidatorInterface $validator, RefreshTokenManagerInterface $refreshTokenManager , UserRepository $ur, UnlockGamesRepository $urRep, JWTTokenManagerInterface $JWTManager , UploaderHelper $helper)
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
                        
                        $valid = new DateTime('now');
                        $valid->add(new DateInterval('P30D'));
                        $refreshToken = $refreshTokenManager->create();
                        $refreshToken->setUsername($user->getUsername());
                        $refreshToken->setRefreshToken();
                        $refreshToken->setValid($valid);
                        $refreshTokenManager->save($refreshToken);
                        
                        $response = [
                            "response" => "user has been updated",
                            'token' => $JWTManager->create($user),
                            'refresh_token' => $refreshToken->getRefreshToken()

                        ];
                        $data = new JsonResponse($response, '200');
                        return $data;
                    }
                }
            }
        }
    }
}
