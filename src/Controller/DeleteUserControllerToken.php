<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use DateTimeImmutable;
use App\Entity\UnlockGames;
use App\Repository\GameScoreRepository;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\PoiScoreRepository;
use App\Repository\QuestRepository;
use Symfony\Component\Mime\Address;
use App\Repository\QrCodeRepository;
use App\Repository\QuestScoreRepository;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UnlockGamesRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


class DeleteUserControllerToken extends AbstractController
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

    public function __invoke(Request $request, 
     UserRepository $ur, UnlockGamesRepository $urRep,
      GameScoreRepository $gsp , QuestScoreRepository $qst , ScoreRepository $sr , PoiScoreRepository $pr  ,  TokenGeneratorInterface $tk )
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }

        $user = $ur->findOneBy(array('username' => $user->username));
        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {

            $userUri =  $request->get('data');

            if (!$userUri instanceof User)
                return $this->json_response('401', 'User not found');

            if ($userUri->getId() !=  $user->getId() )
                return $this->json_response('401', 'cannot handle other users');
                 

            $user->setforgotPasswordToken($tk->generateToken());
            $user->setforgotPasswordTokenRequestedAt(new DateTimeImmutable('now'));
            $user->setforgotPasswordTokenMustBeVerifiedBefore(new DateTimeImmutable('+ 15 minutes'));
            $this->em->flush($user);


            $response = [
                "token" => $user->getForgotPasswordToken()
            ];
            
            $data = new JsonResponse($response, '200');
            return $data;  
        }
    }
}
