<?php

//le namespace existe pour éviter les collusions de nom.
namespace App\Controller;

//on met un use qui permet de faire un alias de la classe qu'on a besoin d'utiliser. On pourra l'appeler juste
//avec AbstractController (le dernier mot de la classe)

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    //ceci est une annotation, qui configure la route où sera appelée cette méthode
    //le name dans l'annotation est unique, c'est l'identifiant . C'est lui qu'on appelle pour faire référence
    //à cette page
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        //la fonction render va toujours chercher un fichier dans templates

        return $this->render('default/home.html.twig');
    }

    /**
     * @Route("/foire-aux-questions", name="faq"))
     */
    public function faq()
    {
        return $this->render('default/faq.html.twig');
    }

    /**
     * @Route("/conditions-générales-utilisation", name="cgu"))
     */
    public function cgu()
    {
        return $this->render('default/cgu.html.twig');
    }

    /**
     * @Route("/fonctionnement", name="how-it-works"))
     */
    public function howItWorks()
    {
        return $this->render('default/how-it-works.html.twig');
    }



}
