<?php

namespace App\Controller;

use App\Entity\UnlockGames;
use App\Entity\User;
use App\Repository\QrCodeRepository;
use App\Repository\UnlockGamesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class GetClientController extends AbstractController
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

    public function __invoke(UserRepository $ur , QrCodeRepository $qr , UnlockGamesRepository $ugr , Request $request)
    {
        $user = $this->security->getUser();

        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }


        $user = $ur->findOneBy(array('username' => $user->username));


        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {

            // if ($this->security->isGranted('ROLE_ADMIN')) {
            //     return $this->json_response('403', 'accès denied');
            // }
            $user =  $request->get('data');
            $qrTotaux = $qr->findBy(array('id_client_id' => $user->getId()));
            $qrCours = [];
            foreach($qrTotaux as $key => $value) {
                $unlock = $ugr->findOneBy(array('qr_code_id' => $value->getId()));
                if($unlock instanceof UnlockGames){
                    array_push($qrCours , $unlock);
                }
            }

            $stats = [
                'qr_generate' => $qrTotaux , 
                'Unlock_games' => $qrCours
            ];

            $user->setStat($stats);
            
            if (!$user instanceof User) {
                return $this->json_response('404', 'not found ');
            }else{
                 return $user;
            }
        }
    }

}