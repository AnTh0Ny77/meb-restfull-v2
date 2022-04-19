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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateQrController extends AbstractController
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

    public function randomKey()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFG-HIJKLMNOPQRSTUVWXYZ1234567890-';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 30; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    public function __invoke(Request $request, GamesRepository $gr, QuestRepository $questRep, UserRepository $ur, UnlockGamesRepository $urRep)
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }
        $user = $ur->findOneBy(array('username' => $user->username));

        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {
            $content = json_decode($request->getContent(), true);
            $game = $gr->findOneBy(array('id' => intval($content['game'])));
            if (!$game instanceof Games) {
                return $this->json_response('401', 'game not found');
            }else{
                   $key = $this->randomKey();
                   $date = new DateTime('now');
                   $key = ''. $key;
                   $qr = new QrCode();
                   $qr->setIdClient($user);
                   $qr->setSecret($key);
                   $qr->setQrLock(0);
                   $qr->setIdGame($game);
                   $qr->setTime(3000);
                   $this->em->persist($qr);
                   $this->em->flush();
                   $link = '/api/UnlockGame/unlock?secret='. $key;
                   $response = [
                        "url" =>  $link,
                       
                    ];
                    $data = new JsonResponse($response, '201');
                    return  $data;
                  
            }
        }
    }
}
