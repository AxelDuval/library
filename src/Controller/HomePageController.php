<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Books;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class HomePageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $books = $entityManager->getRepository(Books::class)->findAll();


        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'books' => $books,
        ]);
    }

    #[Route('/create', name: 'book_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $book = new Books();
        $book->setTitle("Titre du Livre");
        $book->setAuthor("Nom de l'auteur");
        // $book->setDate(new DateTime("now"));
        $book->setDescription("Résumé");
        $book->setPublisher("Maison d'édition");
        $book->setCategory("Catégorie");
        $book->setStatus(false);
        $book->setLoanDate(null);
        $book->setDueDate(null);
        $book->setImageName(null);
        $book->setImageFile(null);
        $book->setLastUser(null);

        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('author', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('description', TextareaType::class, ["attr" => ["class" => "form-control"]])
            ->add('publisher', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('category', TextType::class, ["attr" => ["class" => "form-control"]])
            ->add('save', SubmitType::class, ["label" => "Envoyer", "attr" => ["class" => "btn btn-primary"]])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $datas = $form->getData();
            $datas->setDate(new \DateTime("now"));


            $em = $doctrine->getManager();
            $em->persist($datas);
            $em->flush();
            $this->addFlash('success', 'Votre livre a bien été ajouté');


            return $this->redirectToRoute('home');
        }

        return $this->renderForm('home_page/create.html.twig', [
            'form' => $form,
        ]);
    }
}
