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

class GetUnlockedGamesController extends AbstractController
{

    public function __construct(private Security $security)
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

    public function __invoke(Request $request, GamesRepository $gr, QrCodeRepository $qrRep, UserRepository $ur, UnlockGamesRepository $urRep)
    {
        $user = $this->security->getUser();
        if (empty($user)){
            return $this->json_response('401', 'JWT Token  not found');
        }else{
            $user = $ur->findOneBy(array('username' => $user->username));
            if (!$user instanceof User) {
                return $this->json_response('401', 'user not found');
            } else {
                $list = $urRep->findByUserQr($user->getId());
                if (empty($list)){
                    return $this->json_response('400', 'you don t have any unlocked games');
                }else{
                    $array_game = [];
                    foreach ($list as $key => $value){

                        $qr_code = $qrRep->findOneBy(["id" => $value['qr_code_id']]);
                        if ($qr_code instanceof QrCode) {
                            $phone = $qr_code->getIdClient()->getPhone();
                        } else $phone = null;
                        
                        $game = [
                            
                            "id" => $value['id_game_id'],
                            "name" => $value['name'], 
                            "destination" => $value['destination'],
                            "date" => $value['date'] , 
                            "finish" => $value['finish'],
                            "phone" => $phone
                        ];
                        array_push($array_game,$game);
                    }
                    $response = [
                        "response" => $array_game,
                    ];
                    $data = new JsonResponse($response, '200');
                    return $data;
                }   
            }
        }  
    }
}
