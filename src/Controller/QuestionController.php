<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    //on peut ensuite ajouter en commentaire, la méthode http demandée pour que Symfony trouve la route
    //pour une même url, on peut avoir plusieurs méthodes

    /**
     * @Route("/questions", name="question_list", methods={"GET", "POST"})
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

    //l'id indiqué ici est le nom de ce qu'on veut qui apparaisse dans l'url.
    //on va utiliser ce nom pour le passer en argument dans la fonction. Il faut que ce soit appelé pareil
    //le {id} dans l'url est appelé un jocker (placeholder). On peut tout à fait appeler autre chose, comme le titre
    //en troisième argument de la route, je peux indiquer à quoi je veux que corresponde l'id. Dans ce cas, je veux que ce
    //soit un entier
    /**
     * @Route("/questions/{id}", name="question_detail", requirements={"id" : "\d+"})
     */

    public function detail(int $id)
    {
       $questionRepository=$this->getDoctrine()->getRepository(Question::class);

       //option 1 - findOneBy qui permet d'afficher la ligne spécifiquement l'id
//       $question = $questionRepository->findOneBy([
//           'id' => $id
//       ]);


        //option 2 - find qui prend directement en argument l'id
        $question = $questionRepository->find($id);

        //on vérifie ici qu'on a bien une question, sinon, on gère l'erreur

        if(!$question){
            throw $this->createNotFoundException("cette question n'existe pas");
        }

        return $this->render('question/detail.html.twig',[
            'question'=>$question
        ]);

        //on peut aussi utiliser compact() en mettant en argument les variables qu'on veut passer à la vue
        //compact('question', '...')
        //comme suit:
        //return $this->render('question/detail.html.twig', compact('question'));

    }



}
