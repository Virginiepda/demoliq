<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    /**
     * @Route("/questions", name="question_list")
     */
    public function list()
    {
    //il créé une instance de notre classe, pour aller chercher les infos dans la base
        //il nous permet de faire des SELECT
        $questionRepository=$this->getDoctrine()->getRepository(Question::class);
        //$questions = $questionRepository->findAll(); //ça retourne un tableau d'objets question



        //on va plutôt faire ceci
        //équivaut à SELECT * FROM question WHERE status = 'debating' ORDER BY supports DESC LIMIT 1000
        $questions = $questionRepository->findBy(
            ['status' => 'debating'], //where
            ['supports' => 'DESC'], //order by
            1000, //limit
            0 //offset
        );

        //dd($questions);

//pour passer les arguments dans twig afin de pouvoir les afficher, on les passe dans la fonction render en 2ème argument
        return $this->render('question/list.html.twig', [
            "questions" => $questions,
        ]);
    }


//    public function detail()
//    {
//        $questionRepository=$this
//    }



}
