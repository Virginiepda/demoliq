<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/inscription", name="app_register")
     */
    public function register(UserPasswordEncoderInterface $encoder, Request $request)
    {
        $user = new User();

        $registerForm = $this->createForm(RegisterType::class, $user);

        $registerForm->handleRequest($request);

        //si le formulaire est soumis...
        if($registerForm->isSubmitted() && $registerForm->isValid()) {
            //on va chercher l'EntityManager
            $em = $this->getDoctrine()->getManager();

            //hashe le mot
            $password = $user->getPassword();
            $hash = $encoder->encodePassword($user, $password);
            $user->setPassword($hash);

            //on enregistre en base

            $em ->persist($user);
            //on execute la requête
            $em->flush();

            $this->addFlash('info', 'Bienvenue sur notre site !');

            //on redirige (deux arguments : nom de la route et la valeur de l'argument que la route prend
            return $this->redirectToRoute('app_login');

        }

        return $this->render('security/register.html.twig', [
            'registerForm' =>$registerForm->createView()
        ]);

    }

}
