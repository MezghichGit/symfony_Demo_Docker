<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')  ")]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Upload de la photo de face pour article
            $photoface = $form->get('photoface')->getData();


            if ($photoface) {
                $originalLogoname = pathinfo($photoface->getClientOriginalName(), PATHINFO_FILENAME);

                $newLogoname =   $originalLogoname.'-'.uniqid().'.'.$photoface->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                   $photoface->move(
                        $this->getParameter('article_directory'),
                        $newLogoname
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $article->setPhotoface($newLogoname);
            }
            //fin Upload

             //Upload de la photo du provider
             $photoprofil = $form->get('photoprofil')->getData();


           if ($photoprofil) {
               $originalLogoname = pathinfo($photoprofil->getClientOriginalName(), PATHINFO_FILENAME);

               $newLogoname =   $originalLogoname.'-'.uniqid().'.'.$photoprofil->guessExtension();

               // Move the file to the directory where brochures are stored
               try {
                   $photoprofil->move(
                       $this->getParameter('article_directory'),
                       $newLogoname
                   );
               } catch (FileException $e) {
                   // ... handle exception if something happens during file upload
               }

               $article->setPhotoprofil($newLogoname);
           }
           //fin Upload
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGENT')  ")]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             //Upload de la photo de face pour article
             $photoface = $form->get('photoface')->getData();


             if ($photoface) {
                 $originalLogoname = pathinfo($photoface->getClientOriginalName(), PATHINFO_FILENAME);
 
                 $newLogoname =   $originalLogoname.'-'.uniqid().'.'.$photoface->guessExtension();
 
                 // Move the file to the directory where brochures are stored
                 try {
                    $photoface->move(
                         $this->getParameter('article_directory'),
                         $newLogoname
                     );
                 } catch (FileException $e) {
                     // ... handle exception if something happens during file upload
                 }
 
                 $article->setPhotoface($newLogoname);
             }
             //fin Upload

              //Upload de la photo du provider
              $photoprofil = $form->get('photoprofil')->getData();


            if ($photoprofil) {
                $originalLogoname = pathinfo($photoprofil->getClientOriginalName(), PATHINFO_FILENAME);

                $newLogoname =   $originalLogoname.'-'.uniqid().'.'.$photoprofil->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $photoprofil->move(
                        $this->getParameter('article_directory'),
                        $newLogoname
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $article->setPhotoprofil($newLogoname);
            }
            //fin Upload

            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
    #[Security("is_granted('ROLE_ADMIN')")]
    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
