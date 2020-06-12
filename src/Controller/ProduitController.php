<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Data\SearchData;
use App\Form\SearchForm;
use App\Form\ProduitType;
use App\Entity\Proprietaire;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/proprietaire/stock")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="stock_proprietaire", methods={"GET","POST"})
     */
    public function index(PaginatorInterface $paginator, UserInterface $user, Request $request): Response
    {
        $produit = new Produit();
        $data = new SearchData();
        $form = $this->createForm(ProduitType::class, $produit);
        $form2 = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $form2->handleRequest($request);

        $active_tab1 = "active show";
        $active_tab2 = "";
        $products = $this->getDoctrine()->getRepository(Produit::class)->findSearch($data,$user);

        $page = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            3
        );

        if ($form->isSubmitted() && $form->isValid()) {
            
            $produit->setProprietaire($user->getProprietaire());
            $produit->setCreatedAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit '.$produit->getNom().' a été ajouté avec succès');

            return $this->redirectToRoute('stock_proprietaire');
        }
        elseif ($form->isSubmitted()) {
            $active_tab = $_POST['active_tab'];
            $active_tab2 = ($active_tab ==="tab_2") ? "active show" : "";
            $active_tab1 = ($active_tab !=="tab_2") ? "active show" : "";
        }
        return $this->render('proprietaire/stock.html.twig',[
            'page'=> $page,
            'form2' => $form2->createView(),
            'pagetitle'=>'Stock',
            'form' => $form->createView(),
            'active_tab1' => $active_tab1,
            'active_tab2' => $active_tab2,
        ]);
    }

    /**
     * @Route("/{id}", name="produit_show", methods={"GET"})
     */
    public function show(Produit $produit, UserInterface $user): Response
    {
        $repos = $this->getDoctrine()->getRepository(Produit::class)->find(['id' => $produit->getId()]);
        return $this->render('proprietaire/show.html.twig', [
            'products' => $repos,
            'pagetitle'=>'Stock',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit, UserInterface $user): Response
    {
        $repos = $this->getDoctrine()->getRepository(Produit::class)->findOneBy(['id' => $produit->getId()]);
        $form = $this->createForm(ProduitType::class, $repos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le produit '.$produit->getId().' est bien modifié!');

            return $this->redirectToRoute('stock_proprietaire');
        }

        return $this->render('proprietaire/edit.html.twig', [
            'pagetitle'=>'Stock',
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit, UserInterface $user): Response
    {
        $repos = $this->getDoctrine()->getRepository(Produit::class)->findOneBy(['id' => $produit->getId()]);
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token')) && $repos->getCommandes()->count() == 0) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($repos);
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('alert', 'Le produit a été supprimé');
        }
        else {
            $this->addFlash('alert', 'Le produit est dans une commande');
        }

        return $this->redirectToRoute('stock_proprietaire');
    }
}
