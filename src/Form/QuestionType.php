<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Subject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       //je peux spécifier le type du champ en deuxième argument, mais cela ne sert pas à grand chose car Symfony
        //sait déjà quel type de champ
        // exp: ->add('title', TextType::class)

        //on va ensuite ajouter un troisième argument, équivalent au label de l'input

        //pour affciher le formulaire, on retourne dans le controller et on va créer une instance de form

        $builder
            ->add('title', TextType::class,
                ['label' => "Votre question"])
            ->add('description', TextareaType::class)
            ->add('subjects', EntityType::class, [
                'multiple'=>true,           //multiple indique qu'on peut avoir plusieurs choix
                'expanded'=>true,
                'class'=>Subject::class    //on précise à quelle classe correspond le subjects ,
                                            // ici, on fait un echo. Du coup, afin d'afficher
                                            // les noms, je vais ajouter un toString à la classe Subject,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
