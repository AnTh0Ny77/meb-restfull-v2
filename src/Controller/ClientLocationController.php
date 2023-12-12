<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\SecurityController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\UserRepository;
use App\Repository\ClientLocationRepository;
use App\Entity\User;
use App\Entity\ClientLocation;
use DateTime;

class ClientLocationController extends AbstractController
{

    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $em  , Security $security )
    {
        $this->ur = $userRepository;
        $this->security = $security;
    }

    public function json_response(string $code, string $message)
    {
        $response = [
            "error" => $message,
        ];
        $data = new JsonResponse($response, $code);
        return $data;
    }


    public function __invoke(Request $request,  UserRepository $ur , ClientLocationRepository $cr  )
    {
        $user = $this->security->getUser();
        if (empty($user)) {
            return $this->json_response('401', 'JWT Token  not found');
        }
        $user = $ur->findOneBy(array('username' => $user->username));

        if (!$user instanceof User) {
            return $this->json_response('401', 'user not found');
        } 

        $content = json_decode($request->getContent(), true);
        $insert = new ClientLocation();

        if (!empty($content['default'])) {
            $setZero = $cr->resetBooleanColumnsForUser($user->getId());
            $active  = $cr->findOneBy(array('id' =>$content['default']));
            $active->setBooleanColumn(1);
            $this->em->persist($active);
            $this->em->flush();
            $response = ["message" => 'ok'];
            $data = new JsonResponse($response, '200');
            return $data; 
        }

        if (!empty($content['id'])) {
            $active  = $cr->findOneBy(array('id' =>$content['id']));
            if (!empty($content['booleanColumn']) and $content['booleanColumn'] == '1' ) {
                $setZero = $cr->resetBooleanColumnsForUser($user->getId());
                $active->setBooleanColumn(1);

            }else{
                $active->setBooleanColumn(0);
            }
           
            $active->setTextColumn($content['textColumn']);
            $active->setJsonColumn($content['jsonColumn']);
            $active->setPostal($content['postal']);
            $this->em->persist($active);
            $this->em->flush();
            $response = ["message" => 'ok'];
            $data = new JsonResponse($response, '200');
            return $data; 
        }

        if (!empty($content['delete'])) {
            $active  = $cr->findOneBy(array('id' =>$content['delete']));
            $this->em->remove($active);
            $this->em->flush();
            $response = ["message" => 'ok'];
            $data = new JsonResponse($response, '200');
            return $data;
        }


        $insert->setUser($user);
        $insert->setTextColumn($content['textColumn']);
        $insert->setJsonColumn($content['jsonColumn']);
        $insert->setPostal($content['postal']);
        if (!empty($content['booleanColumn']) and $content['booleanColumn'] == '1' ) {
                $setZero = $cr->resetBooleanColumnsForUser($user->getId());
                $insert->setBooleanColumn(1);
            }else{
                $insert->setBooleanColumn(0);
        }
        $insert->setBooleanColumn($content['booleanColumn']);
        $this->em->persist($insert);
        $this->em->flush();

        $response = ["message" => 'ok'];
        $data = new JsonResponse($response, '200');
        return $data; 


    }

}