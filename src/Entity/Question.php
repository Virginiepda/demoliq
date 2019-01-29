<?php

namespace App\Entity;

//ce use gère les contraintes de validation

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

//le repository est là où on va pouvoir écrire nos éventuelles requêtes plus complexes.
/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{

    //na pas oublier d'ajouter au-dessus  @ORM\HasLifecycleCallbacks()
    //ajouter ceci qui permet de garder en mémoire les champs en automatique
    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        //comme il attend de recevoir un datetime, on implémente alors un objet datetime, je lui mets un \ pour qu'il
        //aille chercher la classe à la racine
        $this->setCreationDate(new \DateTime());
        $this->setSupports(0);
        $this->setStatus('debating');
    }


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    //je vais préciser mes contraintes devant chaque attribut choisi
    //je gère le fait que le champ title n'est pas renseigné
    //je spécifie la longueur maximale
    //je fais cela sur les champs qui viennent du formulaire automatiquement

    /**
     * @Assert\NotBlank(message="merci de renseigner un intitulé de question")
     * @Assert\Length(
     *     min="15",
     *     max="255",
     *     minMessage="15 caractères minimum ",
     *     maxMessage="255 caractères maximum"
     * )
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Assert\Length(
     *     min="3",
     *     max="100000",
     *     minMessage="3 caractères minimum ou rien",
     *     maxMessage="100000 caractères maximum"
     * )
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $supports;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $status;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getSupports(): ?int
    {
        return $this->supports;
    }

    public function setSupports(int $supports): self
    {
        $this->supports = $supports;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
