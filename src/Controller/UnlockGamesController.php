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
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UnlockGamesController extends AbstractController
{

   
    public function __construct(private Security $security , private EntityManagerInterface $em )
    {
      
    }

    public function json_response(string $code, string $message){
        $response = [
            "error" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }

    public function __invoke(Request $request, GamesRepository $gr , QrCodeRepository $qrRep  , UserRepository $ur , UnlockGamesRepository $urRep)
    {
        $user = $this->security->getUser();
        if (empty($user)){
            return $this->json_response('401', 'JWT Token  not found');
        }
        $user = $ur->findOneBy(array('username' => $user->username));
       
        if(!$user instanceof User){
            return $this->json_response('401' ,'user not found');
        }else{
            $secret =  $request->query->get('secret');
            if (empty($secret)) {
                return $this->json_response('400', 'secret parameter not found');
            }else{
                $match =   $qrRep->findOneBy(array('secret' => $secret));
                if (!$match instanceof QrCode) {
                    return $this->json_response('404', 'invalid secret');
                }else{
                    $unlock = $urRep->findOneBy(array('qrCode' => $match->getId()));
                    if ($unlock instanceof UnlockGames) {
                        return $this->json_response('404', 'secret has already been used');
                    }else{
                       
                        $verify = $urRep->findUnlockedr($user->getId(), $match->getIdGame()->getId());
                        if (!empty($verify)) {
                            return $this->json_response('401', 'Game already unlocked');
                        }
                        $newGame = new UnlockGames();
                        $newGame->setIdUser($user);
                        $newGame->setFinish(0);
                        $newGame->setQrCode($match);
                        $date = new DateTime('now +24 hours');
                        $newGame->setDate($date);
                        $this->em->persist($newGame);
                        $this->em->flush();
                        $game = $gr->findOneBy(array('id' => $match->getIdGame()));
                        if (!$game instanceof Games ){
                            return $this->json_response('404', 'game doesnt exist');
                        }
                        else{
                            $response = [
                                "message" => 'congratulations! game : ' . $game->getName() . ' has been unlocked ',
                            ];
                            $data = new JsonResponse($response, '200');
                            return $data;
                        }  
                    }    
                }
            }    
        }
        return $user;
    }
}
