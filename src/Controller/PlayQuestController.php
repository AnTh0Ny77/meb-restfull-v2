<?php 
namespace App\Controller;

use App\Entity\User;
use App\Entity\Games;
use App\Entity\Quest;
use App\Entity\Score;
use App\Entity\Slide;
use App\Entity\QrCode;
use App\Entity\GameScore;
use App\Entity\QuestScore;
use App\Entity\UnlockGames;
use App\Repository\UserRepository;
use App\Repository\GameScoreRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestRepository;
use App\Repository\QrCodeRepository;
use App\Repository\QuestScoreRepository;
use App\Repository\ScoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UnlockGamesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlayQuestController extends AbstractController
{

    public function __construct(private Security $security , private EntityManagerInterface $em){
    }

    public function json_response(string $code, string $message){
        $response = [
            "error" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }


     public function set_game_score(Games $game ,User $user , GameScoreRepository $gr , QuestScoreRepository $qsr ){
       
        $questArray = $game->getQuests();
        $score = 0 ;
        foreach ($questArray as $quest) {
            if ($quest instanceof Quest) {
                $questScore = $qsr->findOneBy(['questId' => $quest->getID(), 'userId' => $user->getId()]);
                if ($questScore instanceof QuestScore) {
                    $score += $questScore->getScore();
                }
            }
        }
        $gameScore = $gr->findOneBy(['game' => $game->getID(), 'user' => $user->getId()]);
        if ($gameScore instanceof GameScore) {
            $gameScore->setScore($score);
        }else{
            $gameScore = new GameScore();
            $gameScore->setScore($score);
            $gameScore->setUser($user);
            $gameScore->setGame($game);
        }
            $this->em->persist($gameScore);
            $this->em->flush();
    }

    public function __invoke(Request $request, QuestScoreRepository $qr ,  GameScoreRepository $grsp , 
    ScoreRepository $sr , GamesRepository $gr, QuestRepository $questRep, 
    UserRepository $ur, UnlockGamesRepository $urRep, UploaderHelper $helper)
    {
    
        $user = $this->security->getUser();
        if (empty($user))
            return $this->json_response('401', 'JWT Token  not found');
        
        $user = $ur->findOneBy(array('username' => $user->username));

        if (!$user instanceof User) 
            return $this->json_response('401', 'user not found');

        $quest =  $request->get('data');

        if (!$quest instanceof Quest) 
            return $this->json_response('400', 'Slide not found');

        $verify_quest = $qr->findOneBy(['questId' => $quest->getID(), 'userId' => $user->getId()]);

        
        $game = $quest->getGame();
        $verify = $urRep->findUnlockedr($user->getId(), intval($game->getId()));


        $answer = json_decode($request->getContent());

        if (empty($answer->isAccepted))
            return $this->json_response('400', 'isAccepted cannot be null or empty ');
        
        if ($answer->isAccepted == true){
            $array_poi = $quest->getPoi();
            if (empty($array_poi))
                return $this->json_response('400', 'No POI for this quest');
            
           
            $quest_score = 0 ;
            foreach ($array_poi as $poi){
                $array_slide = $poi->getSlides();

                if (empty($array_slide))
                    return $this->json_response('400', ' POI : '.$poi->getId().' has no slide in database');

                $poi_score = 0 ;
                foreach ($array_slide as $slide){
                    $score = $sr->findby(['Slide' => $slide->getID() , 'User' => $user->getId()]);
                    if (!empty($score)) {
                        foreach ($score as $key => $value) {
                            $poi_score += $value->getPoint();
                        }
                    }
                }
               $quest_score += $poi_score;
            }
            if (!$verify_quest instanceof QuestScore) {
                $questscore = new QuestScore();
            } else $questscore = $verify_quest;
           
           
            $questscore->setUserId($user);
            $questscore->setQuestId($quest);
            $questscore->setScore($quest_score + 10);
           
            $questscore->setFinished(1);
          
            $this->em->persist($questscore);
            $this->em->flush();
           
            $game = $gr->findOneBy(['id' => $quest->getGame()->getId()]);
            
            $this->set_game_score($game, $user, $grsp, $qr );
            $response = [
                "message" => 'Congratulations '.$quest->getName().' finished',
                "score" => $quest_score
            ];
            $data = new JsonResponse($response, '200');
            return $data;  
        }else{
            return $this->json_response('200', 'wrong answer');
        }
    }

}