<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="text")
     */
    private $content;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $claps;


    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;


    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Question", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;


    public function __construct()
    {
        $this->setDateCreated(new \DateTime());
        $this->setClaps(0);
        $this->setIsPublished(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }


    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }


    public function getClaps(): ?int
    {
        return $this->claps;
    }


    public function setClaps(?int $claps): self
    {
        $this->claps = $claps;

        return $this;
    }


    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }


    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }


    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
