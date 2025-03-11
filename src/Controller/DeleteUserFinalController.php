<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use DateTime;
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


class DeleteUserFinalController extends AbstractController
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
        $content = json_decode($request->getContent(), true);


        $user = $ur->findOneBy(array('email' => $content['email']));

        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {
            $token  = $content['token'];

            $userToken = $user->getForgotPasswordToken();

            $expireMoment = $user->getForgotPasswordTokenMustBeVerifiedBefore();

            $currentDateTime = new DateTime("now");

            if ($userToken === $token and $expireMoment > $currentDateTime ) {

                $asr = $sr->findBy(['User' => $user->getId()]);
                if (!empty($asr)) {
                        foreach ($asr as $value) {
                        $this->em->remove($value);
                        $this->em->flush();
                        }
                }

                $apr = $pr->findBy(['User' => $user->getId()]);
                if (!empty($apr)) {
                    foreach ($apr as $value) {
                        $this->em->remove($value);
                        $this->em->flush();
                    }
                }
                $aqst  = $qst->findBy(['userId' => $user->getId()]);
                if (!empty($aqst)) {
                    foreach ($aqst as $value) {
                        $this->em->remove($value);
                        $this->em->flush();
                    }
                }
                $agst = $gsp->findBy(['user' => $user->getId()]);
                if (!empty($agst)) {
                    foreach ($agst as $value) {
                        $this->em->remove($value);
                        $this->em->flush();
                    }
                }
                $aurep = $urRep->findBy(['idUser' => $user->getId()]);
                if (!empty($aurep)) {
                    foreach ($aurep as $value) {
                        $this->em->remove($value);
                        $this->em->flush();
                    }
                }
                $this->em->remove($user);
                $this->em->flush();

                $response = [ "message" => "Vos données ont bien été supprimées de nos serveurs"];
                $data = new JsonResponse($response, '400');
                return $data;  
            
            }else{
                $response = [ "message" => "lien invalide ou expiré"];
                $data = new JsonResponse($response, '400');
                return $data;  
            }
        }
    }
}
