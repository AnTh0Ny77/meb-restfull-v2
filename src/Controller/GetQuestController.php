<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use App\Entity\UnlockGames;
use App\Repository\GamesRepository;
use App\Repository\UserRepository;
use App\Repository\QrCodeRepository;
use App\Repository\QuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UnlockGamesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GetQuestController extends AbstractController
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
            $game_id =  $request->query->get('game_id');
            $verify = $urRep->findUnlockedr($user->getId(), intval($game_id));
            if (empty($verify)) {
                return $this->json_response('401', 'this game is not unlocked for user: '.$user->getUsername().'');
            }else{
                $quest = $questRep->findBy(array('game' => intval($game_id)));
                if (empty($quest)) {
                    return $this->json_response('401', 'No quest for the game '. $game_id.'');
                }else{
                    $response = [];
                    foreach ($quest as $key => $value) {
                        $temp = [
                           'name' => $value->getName(),
                           'color' => $value->getColor()
                        ];
                       array_push($response, $temp);
                    }
                    $data = [
                        "quest" => $response,
                    ];
                    $data = new JsonResponse($data, '200');
                    return  $data;
                }
            }
           
        }
      
    }
}
