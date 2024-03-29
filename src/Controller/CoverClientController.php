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
use App\Repository\QrCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use App\Repository\UnlockGamesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

class CoverClientController extends AbstractController
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



    public function __invoke(Request $request, ValidatorInterface $validator, UserRepository $ur, UnlockGamesRepository $urRep , UploaderHelper $helper )
    {
                    $user = $this->security->getUser();
                    if (empty($user)){
                        return $this->json_response('401', 'JWT Token  not found');
                    }

                    $user = $ur->findOneBy(array('username' => $user->username));
                
                    if (!$user instanceof User) {
                        return $this->json_response('401', 'user not found');
                    } else {

                    if ($user->getId() != 914) {
                        return $this->json_response('401', 'user not found');
                    }
                    $content = $request->get('user__id');

                    if (empty($content)) {
                       return $this->json_response('401', 'user not found ');
                    }

                    $temp = $ur->findOneBy(['email' => $content]);
                    if (!$temp instanceof User) {
                         return $this->json_response('401', 'user not found');
                    }
                    $temp->setFile($request->files->get('cover'));
                    if (!$request->files->get('cover') instanceof File) {
                        return $this->json_response('403', 'cover cannot be empty');
                    }
                    $temp->setUpdatedAt(new DateTime('now'));
                    $errors = $validator->validate($temp);
                    if (count($errors) > 0) {
                        $errorsString = (string) $errors;
                        $response = [
                            "error" => $errorsString,

                        ];
                        $data = new JsonResponse($response, '401');
                        return $data;
                    } else{
                        $this->em->persist($temp);
                        $this->em->flush();
                        $path =  '' .$helper->asset($temp, 'file');
                        $temp->setCoverPath($path);
                        $this->em->persist($temp);
                        $this->em->flush();
                        $response = [
                            "message" => 'cover has been updated',
                            'cover ' =>  $path
                        ];
                        $data = new JsonResponse($response, '201');
                        return  $data;
                    }
            }
    }
}