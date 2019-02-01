<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/moncompte", name="app_account", methods={"GET", "POST"})
     */
    public function account(){

//        $mailUtilisateurCourant = $this->getUser()->getEMail();
//        $passwordUtilisateurCourant = $this->getUser()->getPassword();
//
//
//        $userbdd = new User();
//
////
//        $userRepository = $this->getDoctrine()->getRepository(User::class);
//
//        $userbdd = $userRepository->findBy([
//            'id' => $id
//        ]);
//
//        if($this->getUser()){
//
//            if($mailUtilisateurCourant == $user->getEmail() && $passwordUtilisateurCourant == $user->getPassword()) {
//
        return $this->render('user/account.html.twig');
//            }
//
//        }

    }
}
