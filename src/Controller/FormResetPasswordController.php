<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use DateTime;

class FormResetPasswordController extends AbstractController
{
    public function formsPassword(Request $request , UserPasswordHasherInterface $hasher ,  ValidatorInterface $validator , CsrfTokenManagerInterface $csrfTokenManager ,  EntityManagerInterface $entityManager){
        $alert = false ;
        $alertSuccess = false ;
        if ($request->isMethod('POST')) {
       
        $password = $request->request->get('password');
       
        $userId = $request->request->get('id');
        $userRepository = $entityManager->getRepository(User::class); 
        $user = $userRepository->find($userId);

        if (empty($password)) {
            
            return $this->redirectToRoute('app_reset_password', ['error' => 'Merci de renseigner les memes mots de passe']);
        }

        $submittedToken = $request->request->get('_token');

        $userToken = $user->getForgotPasswordToken();

        $expireMoment = $user->getForgotPasswordTokenMustBeVerifiedBefore();

        $currentDateTime = new DateTime("now");

        if ($userToken === $submittedToken ) {
            if ($expireMoment <  $currentDateTime) {
                $alert = 'le lien a expiré';
            }else{
                $user->setPlainPassword($password);
                $errors = $validator->validate($user);

                if (count($errors) > 0) {

                }
                $pass = $hasher->hashPassword($user, $user->getPlainPassword());
                    $user->setPassword($pass);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $alertSuccess = 'le mot de passe a ete changé avec succès !';
            }
        }
       

    }
       return $this->render('resetPassword.html.twig', [
                'alert' => $alert , 
                'alert_success' => $alertSuccess
        ]);
    }
}