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
use App\Repository\GameScoreRepository;
use Symfony\Component\Mime\Address;
use App\Repository\QrCodeRepository;
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

class MeController extends AbstractController
{

    public function __construct(private Security $security ,private EntityManagerInterface $em)
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

    public function __invoke(Request $request, GamesRepository $gr, GameScoreRepository $gsp , TokenGeneratorInterface $tk , MailerInterface $mailer , QuestRepository $questRep, UserRepository $ur, UnlockGamesRepository $urRep , UploaderHelper $helper)
    { 
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }

        $user = $ur->findOneBy(array('username' => $user->username));
        $gameScore = $gsp->findBy(array('user' => $user->getId()));

        $user->setUserScoreTotal($gameScore);
        
        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } else {

            if ($user->getConfirmed() != true) {
                return $this->json_response('400', 'user need to be confirmed , see: api/user/guest/confirm');
            } else {
                return $user;
            }
        }
    }
}
