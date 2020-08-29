<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
 
use App\Repository\ArticleRepository; 
use App\Repository\CategorieRepository; 
use App\Entity\Article;
use Doctrine\Common\Persistence\ObjectManager;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('default/home.html.twig', [
            'title' => "Bienvenue les copains !", 
            'age' => 31
            ]);
    }

    /**
     * @Route("/index/{value}", name="index")
     */
    public function index($value, Request $request, ArticleRepository $artRepo, CategorieRepository $catRepo)
    {
        $articles = $artRepo->findByCategory($value);
        $categorie = $catRepo->loadCategorieByName($value);
        return $this->render('default/index.html.twig', [
            'controller_name' => 'MainController', 
            'articles'=>$articles,
            'categorie'=>$categorie
        ]);
    }

    /**
     * @Route("/downloads", name="downloads")
     */
    public function indexDownload(Request $request, ArticleRepository $repo, CategorieRepository $catRepo)
    {
        $value = "Téléchargements";
        $articles = $repo->findByCategory($value);
        $categorie = $catRepo->loadCategorieByName($value);
        return $this->render('default/indexDownload.html.twig', [
            'controller_name' => 'MainController', 
            'articles'=>$articles,
            'categorie'=>$categorie
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager){

        $categorie = $article->getCategorie();
        return $this->render('default/show.html.twig',[
            'article'=>$article,
            'categorie'=>$categorie
            ]);

    }

    /**
     * @Route("/downloads/{id}", name="download_show")
     */
    public function download(Article $article, Request $request, ObjectManager $manager){

        $categorie = $article->getCategorie();

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            $this->addFlash('failure', "Vous devez vous connecter pour pouvoir télécharger");
            return $this->redirect($this->generateUrl('security_login'));
        };
        return $this->render('default/download.html.twig',[
            'article'=>$article,
            'categorie'=>$categorie
            ]);

    }
}
