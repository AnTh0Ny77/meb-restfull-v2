<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use App\Entity\Slide;
use App\Entity\Score;
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
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FinishGameController extends AbstractController
{

    public function __construct(private Security $security , private EntityManagerInterface $em)
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

    public function __invoke(Request $request, GamesRepository $gr, UserRepository $ur,  UnlockGamesRepository $urRep)
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
                return $this->json_response('401', 'Game not found');
            }
            $verify = $urRep->findUnlockedr($user->getId(), intval($game->getId()));
            if (empty($verify)) {
                return $this->json_response('401', 'Game not unlocked');
            }
            foreach ($verify as $key => $value) {
                    $value = $urRep->findOneBy(array('qrCode' => intval($value['qr_code_id'])));
                    if ($value instanceof UnlockGames) {
                        $value->setFinish(1);
                        $this->em->persist($value);
                        $this->em->flush();
                        return $this->json_response('200', 'finished !');
                    }else{
                        return $this->json_response('401', 'Wrong configuration ');
                    }
                
            }
            

        }

    }
}