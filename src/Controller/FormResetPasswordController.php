<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FormResetPasswordController extends AbstractController
{
    public function formsPassword(Request $request){

        $submittedToken = $request->request->get('token');
        
        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
        // ... do something, like deleting an object
        }

       return $this->render('resetPassword.html.twig', [
        ]);
    }
}