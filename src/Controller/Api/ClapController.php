<?php

namespace App\Controller\Api;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;



class ClapController extends AbstractController
{
    /**
     * @Route("/api/message/{id}/clap", name="api_clap_post", methods={"POST"})
     */
    public function addClap($id)
    {
        //on va chercher le message donné

        $em= $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Message::class);

        $message=$repo->find($id);
        //on modifie le claps et on ajoute 1
        $message->setClaps($message->getClaps()+1);

        //puis on met le nouveau résultat en base de données

        $em->flush();


        //je renvoie une réponse ensuite, je vais retourner du Json, il renvoie un tableau,
        //on y met ce qu'on veut retourner

        return new JsonResponse([
            "status"=>"ok",
            "message"=>"",
            "data"=> [
                "claps"=> $message->getClaps()   //on va récupérer la données data.claps et on va aller l'afficher
                                                //voir dans app.js
            ]
        ]);



    }


}