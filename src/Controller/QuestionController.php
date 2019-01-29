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
        $questionRepository=$this->getDoctrine()->getRepository(Question::class);
        //$questions = $questionRepository->findAll(); //ça retourne un tableau d'objets question

        //on va plutôt faire ceci
        $questions = $questionRepository->findBy(
            ['status' => 'debating'], //where
            ['supports' => 'DESC'], //order by
            1000, //limit
            0 //offset
        );

        //dd($questions);


        return $this->render('question/list.html.twig');
    }



}
