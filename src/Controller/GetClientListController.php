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

class GetClientListController extends AbstractController
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

    public function __invoke(UserRepository $ur , Request $request){

        $content = json_decode($request->getContent(), true);

        if (empty($content)) {
           return $this->json_response('422', 'Mauvaise requete');
        }
        if (empty($content['terces']) or $content['terces'] != "TEstEawviyupPPlFRT789@VoYaJoUersesS77125!87@DeYaoURtH@?7854ZAEEcWWW.www.ExplorE") {
           return $this->json_response('433', 'opération impossible');
        }
        $list = $ur->findByRole('ROLE_CLIENT');
        
        return $list; 
        
    }

}