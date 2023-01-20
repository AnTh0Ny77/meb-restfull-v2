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
use App\Repository\GameScoreRepository;
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
use App\Entity\GameScore;

class GetScoreControllerClass extends AbstractController
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

    public function __invoke(GameScoreRepository $gsr , UserRepository $ur)
    {
       
      $users = $ur->findAll();
      $results = [];
      foreach ($users as $key => $value) {
          $user_total = 0 ;
          $score_for_user =  $gsr->findBy([ 'user' => $value->getId()]);
          foreach($score_for_user as $score) {
                $user_total += $score->getScore();
          }
		  if($user_total > 0){
			  $results[$value->getUsername()] = $user_total;
		  }
      
      }
   
    uasort($results, function ($a, $b) {
    return $b <=> $a;
    });
    $data = new JsonResponse($results, '200');
    return $data;    
    }
}
