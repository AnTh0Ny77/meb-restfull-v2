<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use App\Entity\UnlockGames;
use App\Repository\GamesRepository;
use App\Repository\UserRepository;
use App\Repository\QrCodeRepository;
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

    public function verify_if_unlock($user, $game_id , $qrRep){
        
            $unlock =  $qrRep->findOneBy(array('idGame' => intval($game_id)));
            if(!$unlock instanceof QrCode){
                return $this->json_response('404', 'invalid game id');
            }else{
                dd($unlock);
            }
    }

    public function __invoke(Request $request, GamesRepository $gr, QrCodeRepository $qrRep, UserRepository $ur, UnlockGamesRepository $urRep)
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
            $verify = $this->verify_if_unlock($user , $game_id , $qrRep );
            dd($verify);
        }
      
    }
}
