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

class ResetPasswordController extends AbstractController
{

    public function __construct(private Security $security, private EntityManagerInterface $em)
    {
    }
    public function json_response(string $code, string $message)
    {
        $response = [
            "response" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }

    public function sendRecoveryLink(UserRepository $ur, TokenGeneratorInterface $tk, MailerInterface $mailer,  Request $request)
    {

        $request = json_decode($request->getContent());
        $email = $request->email;
        $user = $ur->findOneBy(array('email' => $email));
        if (!$user instanceof User)
            return $this->json_response('401', 'user not found');

        $user->setforgotPasswordToken($tk->generateToken());
        $user->setforgotPasswordTokenRequestedAt(new DateTimeImmutable('now'));
        $user->setforgotPasswordTokenMustBeVerifiedBefore(new DateTimeImmutable('+ 15 minutes'));
        $this->em->flush($user);

        $email = (new TemplatedEmail())
            ->from('login@explorelab.app')
            ->to(new Address($user->getEmail()))
            ->subject('Votre récupération de mot de passe')
            ->htmlTemplate('emails/passwordRecovery.html.twig')
            ->context([
                'user' => $user
            ]);
        $mailer->send($email);
        return  $user->getEmail();
    }

    public function __invoke(Request $request, GamesRepository $gr, TokenGeneratorInterface $tk, MailerInterface $mailer, QuestRepository $questRep, UserRepository $ur, UnlockGamesRepository $urRep, UploaderHelper $helper)
    {

        $mail = $this->sendRecoveryLink($ur, $tk, $mailer,  $request);
        return $this->json_response('200', 'mail was send at ' . $mail . '');
    }
}
