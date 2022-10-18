<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Games;
use App\Entity\QrCode;
use App\Entity\Transac;
use App\Entity\UnlockGames;
use App\Repository\UserRepository;
use App\Repository\GamesRepository;
use App\Repository\QuestRepository;
use Endroid\QrCode\Builder\Builder;
use App\Repository\QrCodeRepository;
use App\Repository\TransacRepository;
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

class ReloadExcController extends AbstractController
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

    public function __invoke(Request $request,  UserRepository $ur , TransacRepository $tr)
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }
        $user = $ur->findOneBy(array('username' => $user->username));

        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {

            if (!in_array("ROLE_CLIENT", $user->getRoles())){
                return $this->json_response('401', 'User nedd to be a client ');
            }
           
            $content = json_decode($request->getContent(), true);

            if (empty($content['amount'])) {
                return $this->json_response('401', 'amount cannot be empty');
            } 

            $actual_sold = floatval($user->getExploreCoin());
            $new_sold = $actual_sold + floatval($content['amount']/100);
            $user->setExploreCoin($new_sold);
            $transaction = new Transac();
            $transaction->setUser($user);
            $transaction->setAmount(floatval($content['amount']/100));
            $date = new DateTime('now');
            $transaction->setCreatedAt($date);
            $this->em->persist($transaction);
            $this->em->persist($user);
            $this->em->flush();
            return $this->json_response('201', 'the transaction has beeen registred');
        }
    }
}