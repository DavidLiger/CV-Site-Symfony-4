<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Titre", type="string", length=255)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=2500)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="Contenu", type="string", length=2500)
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", nullable=true)
     * Stocke le nom du fichier uploadé
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 400,
     *     minHeight = 200,
     *     maxHeight = 400
     *     )
     */
    private $imageName;

    /**
     * @ORM\Column(type="string", nullable=true)
     * Stocke le nom du fichier uploadé
     * @Assert\Image(
     *     minWidth = 200,
     *     maxWidth = 400,
     *     minHeight = 200,
     *     maxHeight = 400
     *     )
     */
    private $imageNamePreview;

    /**
     * @ORM\Column(type="string", nullable=true)
     * Stocke le nom du fichier uploadé
     *
     */
    private $fileName;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     *
     * @return Article
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Article
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }


    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Article
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return Article
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @return UploadedFile | string
     */
    public function getImageName(){
        return $this->imageName ;
    }

    public function setImageName($imageName ){
        $this->imageName = $imageName ;

        return $this;
    }

    /**
     * @return UploadedFile | string
     */
    public function getImageNamePreview(){
        return $this->imageNamePreview ;
    }

    public function setImageNamePreview($imageNamePreview ){
        $this->imageNamePreview = $imageNamePreview ;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(){
        return $this->fileName ;
    }

    public function setFileName($fileName ){
        $this->fileName = $fileName ;

        return $this;
    }
}

