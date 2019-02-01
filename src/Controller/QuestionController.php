<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Question;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\QuestionType;
use App\Utils\Helper;
use PhpParser\Node\Stmt\Throw_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    //on peut ensuite ajouter en commentaire, la méthode http demandée pour que Symfony trouve la route
    //pour une même url, on peut avoir plusieurs méthodes
    //on a le GET puisqu'on va chercher les infos
    //on a aussi mis POST car on va pouvoir ensuite ajouter des commentaires

    /**
     * @Route("/questions", name="question_list", methods={"GET", "POST"})
     */
    public function list()
    {
    //il créé une instance de notre classe, pour aller chercher les infos dans la base
        //il nous permet de faire des SELECT
//        $questionRepository=$this->getDoctrine()->getRepository(Question::class);
        //$questions = $questionRepository->findAll(); //ça retourne un tableau d'objets question



        //on va plutôt faire ceci
        //équivaut à SELECT * FROM question WHERE status = 'debating' ORDER BY supports DESC LIMIT 1000
        //cette méthode n'étant plus valable, nous allons créer notre propre requête
//        $questions = $questionRepository->findBy(
//            ['status' => 'debating'], //where
//            ['supports' => 'DESC'], //order by
//            1000, //limit
//            0 //offset
//        );

        $questionRepository = $this->getDoctrine()->getRepository(Question::class);

//voici la méthode que nous avons créé dans le QuestionRepository nous permet de faire des SELECT
        $questions = $questionRepository->findListQuestions();

        //dd($questions);

//pour passer les arguments dans twig afin de pouvoir les afficher, on les passe dans la fonction render en 2ème argument
        return $this->render('question/list.html.twig', [
            "questions" => $questions,
        ]);
    }

    /**
     * @Route("/questions/fermées", name="closed-question_list", methods={"GET", "POST"})
     */
    public function listClosedQuestion()
    {
        $questionRepository = $this->getDoctrine()->getRepository(Question::class);

//voici la méthode que nous avons créé dans le QuestionRepository nous permet de faire des SELECT
        $questions = $questionRepository->findClosedQuestions();

        //dd($questions);

//pour passer les arguments dans twig afin de pouvoir les afficher, on les passe dans la fonction render en 2ème argument
        return $this->render('question/list-closed.html.twig', [
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
    public function detail(int $id, Request $request)
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





        // créé une instance de message à associer au formulaire
        $message = new Message();

        //on va tout de suite le rattacher à la question. Il a maintenant un objet question associé
        $message->setQuestion($question);

        //créer le formulaire
        $messageForm= $this->createForm(MessageType::class, $message);

        //récuperer les données présentes dans la requête et les injecter dans le protected function dispatchMessage($message): Envelope
//NE PAS OUBLIER D'AJOUTER UN CONSTRUCTEUR POUR INITIALISER LES VARIABLES PAR DEFAUT: CLAPS, DATE ET ISPUBLISHED

        $messageForm->handleRequest($request);

        //si le formulaire est soumis et valide
        //récupérer l'entitymanager
        //sauvegarder l'instance
        //executer
        if($messageForm->isSubmitted() && $messageForm->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

        //créer un message protected function addFlash(string $type, string $message)
            $this->addFlash('info', 'Merci pour votre participation !');

            //redirige vers la page actuelle (pour vider le message)
            //ainsi tout est effacé, le POst n'est plus engagé et ainsi la personne ne peut plus reposter une
            //deuxième fois le même formulaire
            return $this->redirectToRoute('question_detail', ['id'=> $question->getId()] );
        }


        //    recupère le message repository

        $messageRepository=$this->getDoctrine()->getRepository(Message::class);

        //récupère les 200 messages les plus récents
        //si c'est findALl, on ne peut pas controler le tri, c'est trié du plus récent au plus ancien
        //si on veut trier comme on veut, il est préférable de faire un findby
        $messages=$messageRepository->findBy(
            [
                'isPublished' => true,
                'question' =>$question
            ],
            ['dateCreated' =>'DESC'],
            200
        );




        return $this->render('question/detail.html.twig',[
            'question'=>$question,
            'message'=>$message,
            'messages'=>$messages,
            'messageForm'=>$messageForm->createView()
        ]);

        //on peut aussi utiliser compact() en mettant en argument les variables qu'on veut passer à la vue
        //compact('question', '...')
        //comme suit:
        //return $this->render('question/detail.html.twig', compact('question'));

    }


    /**
     * @Route(
     *     "/questions/ajouter",
     *      name="question_add",
     *     methods={"GET", "POST"}
     * )
     */
    public function create(Request $request)
    {

        //ON peut faire une vérification sur le role du user ici
        //option 1
//        if($this->isGranted("ROLE_USER")){
//            Throw $this->createAccessDeniedException("pas le droit");
//        }
//                //option2
//        $this->denyAccessUnlessGranted("ROLE_USER");



        //ça crée une instance de Question, ça hydrate la classe et ça renvoie ensuite en BDD
        $question = new Question();

        //on créée une instance du formulaire, on lui passe déjà la question vide en attribut,
        //comme ça, il sait déjà où chercher et où afficher les données

        $questionForm = $this->createForm(QuestionType::class, $question);

        //on hydrate les données du formulaire dans la bdd

        $questionForm->handleRequest($request);

        if($questionForm->isSubmitted() && $questionForm->isValid()){

            //méthode qui complète twig ayant caché le formulaire.
            //seul l'utilisateur connecté peut poster un commentaire

            $this->denyAccessUnlessGranted("ROLE_USER");



            //on va chercher l'EntityManager
            $em = $this->getDoctrine()->getManager();  //on peut aussi le passer en argument de la fonction
            //public function create (EntityManager Manager)
            //on demande ensuite à Doctrine de sauvegarder
            $em ->persist($question);
            //on execute la requête
            $em->flush();

            //On créée un message flash à afficher sur la prochaine page indiquant que tout s'est bien passé
            //deux arguments - le label et le message

            $this->addFlash('info', 'Merci pour votre participation !');

            //on redirige (deux arguments : nom de la route et la valeur de l'argument que la route prend
            return $this->redirectToRoute('question_detail', ['id'=> $question->getId()] );


            //il faut maintenant penser à indiquer où le message doit être affiché.
            //dans notre cas, le mettre dans base.html.twig est tout indiqué
        }


        return $this->render('question/create.html.twig', [
                'questionForm' =>$questionForm->createView()
        ]);



        //pour supprimer:
        //$em = remove($question);
        //$em =flush();
    }



//


}
