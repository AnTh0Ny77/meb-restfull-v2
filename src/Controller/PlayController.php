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

class PlayController extends AbstractController
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

    public function __invoke(Request $request, GamesRepository $gr, QuestRepository $questRep, UserRepository $ur, UnlockGamesRepository $urRep, UploaderHelper $helper)
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }

        $user = $ur->findOneBy(array('username' => $user->username));
        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {

            $slide =  $request->get('data');
            if (!$slide instanceof Slide) {
                return $this->json_response('401', 'Slide not found');
            }
            $game = $slide->getPoi()->getQuest()->getGame();

            $verify = $urRep->findUnlockedr($user->getId(), intval($game->getId()));
           
            if (empty($verify)) {
                return $this->json_response('401', 'the game must be unlocked ');
            }
           
            if (intval($verify[0]['finish']) == 1) {
                return $this->json_response('401', 'the game is already finished ');
            }
            if ( $slide->getTypeSlide()->getId() == 6) {
                return $this->json_response('400', 'please use the correct request for slide type: '. $slide->getTypeSlide()->getName().'');
            }
           
           
            switch (intval($slide->getTypeSlide()->getId())) {
                case 1:
                case 3:
                    $score = new Score();
                    $score->setSlide($slide);
                    $score->setUser($user);
                    $score->setPoint(0);
                    $score->setValue('');
                    $message = $slide->getTextSuccess();
                    $response = [
                        "message" => $message,
                        "score" => null
                    ];
                    $data = new JsonResponse($response, '200');
                    return $data;  
                    break;
                case 2:
                    if (empty($slide->getResponse())) {
                        return $this->json_response('500', 'wrong data base configutation for slide type (empty response list)');
                    }
                    $answer = json_decode($request->getContent());
                    if (empty($answer)) {
                        return $this->json_response('400', 'answer cannot be empty ');
                    }
                    $true = false ;
                    if ($slide->getSolution() === $answer->answer) {
                        $true = true;
                    }
                   if ($true){
                        $score = new Score();
                        $score->setSlide($slide);
                        $score->setUser($user);
                        $message = $slide->getTextSuccess();
                        $score->setPoint(1);
                        $score->setValue($answer->answer);
                   }else{
                        $score = new Score();
                        $score->setSlide($slide);
                        $message = $slide->getTextFail();
                        $score->setUser($user);
                        if ($slide->getPenality()) {
                            $score->setPoint(-1);
                        }else{
                            $score->setPoint(0);
                        }
                        
                        $score->setValue($answer->answer);
                   }
                    $this->em->persist($score);
                    $this->em->flush();
                    $response = [
                        "message" => $message,
                        "score" => $score->getPoint()
                    ];
                    $data = new JsonResponse($response, '200');
                    return $data;  
                   
                    break;
                case 4:
                    if (empty($slide->getSolution())) {
                        return $this->json_response('500', 'wrong data base configutation for slide type (empty response list)');
                    }
                    $answer = json_decode($request->getContent());
                    if (empty($answer->answer)) {
                        return $this->json_response('400', 'answer cannot be null or empty ');
                    }
                    if (empty($answer->isAccepted)) {
                        return $this->json_response('400', 'isAccepted cannot be null or empty ');
                    }
                    // $solution_array = explode(';',  $slide->getSolution());
                    $true = false;
                    if ($answer->isAccepted == true) {
                        $true = true;
                    }
                   
                    // foreach ($solution_array as $key => $value) {
                    //     if (strtoupper($value) == strtoupper($answer->answer)) {
                    //         $true = true;
                    //     }
                    // }
                    if ($true) {
                        $score = new Score();
                        $score->setSlide($slide);
                        $score->setUser($user);
                        $message = $slide->getTextSuccess();
                        $score->setPoint(1);
                        $score->setValue($answer->answer);
                    } else {
                        $score = new Score();
                        $score->setSlide($slide);
                        $message = $slide->getTextFail();
                        $score->setUser($user);
                        if ($slide->getPenality()) {
                            $score->setPoint(-1);
                        } else {
                            $score->setPoint(0);
                        }

                        $score->setValue($answer->answer);
                    }
                    $this->em->persist($score);
                    $this->em->flush();
                    $response = [
                        "message" => $message,
                        "score" => $score->getPoint()
                    ];
                    $data = new JsonResponse($response, '200');
                    return $data;  
                   
                    break;
                case 5:
                    if (empty($slide->getSolution())) {
                        return $this->json_response('500', 'wrong data base configutation for slide type (empty response list)');
                    }
                    $answer = json_decode($request->getContent());
                    if (empty($answer)) {
                        return $this->json_response('400', 'answer cannot be null or empty ');
                    }
                    $true = false;
                    if (intval($slide->getSolution()) == intval($answer->answer)) {
                            $true = true;
                    }
                    if ($true) {
                        $score = new Score();
                        $score->setSlide($slide);
                        $score->setUser($user);
                        $message = $slide->getTextSuccess();
                        $score->setPoint(1);
                        $score->setValue($answer->answer);
                    } else {
                        $score = new Score();
                        $score->setSlide($slide);
                        $message = $slide->getTextFail();
                        $score->setUser($user);
                        if ($slide->getPenality()) {
                            $score->setPoint(-1);
                        } else {
                            $score->setPoint(0);
                        }

                        $score->setValue($answer->answer);
                    }
                    $this->em->persist($score);
                    $this->em->flush();
                    $response = [
                        "message" => $message,
                        "score" => $score->getPoint()
                    ];
                    $data = new JsonResponse($response, '200');
                    return $data;

                    break;
                default:
                    return $this->json_response('500', 'wrong data base configutation for slide type');
                    break;
            }
           
        }
    }
}
