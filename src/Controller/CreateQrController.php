<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use App\Entity\UnlockGames;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestRepository;
use Endroid\QrCode\Builder\Builder;
use App\Repository\QrCodeRepository;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Font\NotoSans;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UnlockGamesRepository;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreateQrController extends AbstractController
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

    public function randomKey()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFG-HIJKLMNOPQRSTUVWXYZ1234567890-';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 30; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
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
            $content = json_decode($request->getContent(), true);
            $game = $gr->findOneBy(array('id' => intval($content['game'])));
            if (!$game instanceof Games) {
                return $this->json_response('401', 'game not found');
            }else{
                   $key = $this->randomKey();
                   $date = new DateTime('now');
                   $key = ''. $key;
                   $qr = new QrCode();
                   $qr->setIdClient($user);
                   $qr->setSecret($key);
                   $qr->setQrLock(0);
                   $qr->setIdGame($game);
                   $qr->setTime(3000);
                   $this->em->persist($qr);
                   $this->em->flush();
                   $link = '/api/UnlockGame/unlock?secret='. $key;
                   $response = [
                        "url" =>  $link,
                       
                    ];
                    $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                    
                    $link = $baseurl . '/'. $link;
                    $result = Builder::create()
                    ->writer(new PngWriter())
                    ->writerOptions([])
                    ->data($link)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                    ->size(300)
                    ->margin(10)
                    ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->labelText($game->getName())
                    ->labelFont(new NotoSans(15))
                    ->labelAlignment(new LabelAlignmentCenter())
                    ->build();
                    
                    $response = new QrCodeResponse($result);
                    return  $response;
                    // $data = new JsonResponse($response, '201');
                    // return $data;
                   
                  
            }
        }
    }
}
