<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use DateTimeImmutable;
use App\Entity\UnlockGames;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestRepository;
use Symfony\Component\Mime\Address;
use App\Repository\QrCodeRepository;
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

class GetGamesUserController extends AbstractController
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

    public function __invoke(Request $request, GamesRepository $gr, TokenGeneratorInterface $tk, MailerInterface $mailer, QuestRepository $questRep, UserRepository $ur, UnlockGamesRepository $urRep, UploaderHelper $helper)
    {
        $user = $this->security->getUser();
        
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }

        $user = $ur->findOneBy(array('username' => $user->username));
        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {

           
                $game =  $request->get('data');
                if (!$game instanceof Games) {
                    return $this->json_response('404', 'not found ');
                }

                foreach ($game->getQuests() as $quest) {
                    $questScores = $user->getQuestScores();
                    foreach ($questScores as $questScore) {
                        $quest->setUserQuestScore(0);
                        $quest->setUserQuestFinished(0);
                        if ($questScore->getQuestId()->getId() ==  $quest->getId()) {
                            $quest->setUserQuestScore($questScore->getScore());
                            $quest->setUserQuestFinished($questScore->getFinished());
                        }
                        $this->em->flush($quest);
                    }
                    foreach ($quest->getPoi() as $poi) {
                        $poiScores = $user->getPoiScores();
                        foreach ($poiScores as $score) {
                                $poi->setUserPoiScore(0);
                                $poi->setUserPoiFinished(0);
                                
                            if ($poi->getId()== $score->getPoi()->getId()) {
                                $poi->setUserPoiScore($score->getScore());
                                $poi->setUserPoiFinished($score->getFinished());
                            }
                            $this->em->flush($poi);
                        }
                    }
                }
                return $game;
            
        }
    }
}
