<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Produit;
use App\Form\StockType;
use App\Form\ProduitType;
use App\Entity\Proprietaire;
use App\Repository\ProduitRepository;
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
    public function index(UserInterface $user, Request $request): Response
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        $active_tab1 = "active show";
        $active_tab2 = "";
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $image */

            $image = $form->get('produit')->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', "");
                
                $newFilename = $originalFilename.'-'.uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $stock->getProduit()->setImage($newFilename);
            }
            
            $stock->setProprietaire($user->getProprietaire());
            //dump($user->getProprietaire()->getProduits());die;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stock->getProduit());
            $entityManager->persist($stock);
            $entityManager->flush();

            return $this->redirectToRoute('stock_proprietaire');
        }
        elseif ($form->isSubmitted()) {
            $active_tab = $_POST['active_tab'];
            $active_tab2 = ($active_tab ==="tab_2") ? "active show" : "";
            $active_tab1 = ($active_tab !=="tab_2") ? "active show" : "";
        }
        return $this->render('proprietaire/stock.html.twig',[
            'stocks' => $user->getProprietaire()->getProduits(),
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
        $repos = $this->getDoctrine()->getRepository(Stock::class)->findOneBy(['produit' => $produit, 'proprietaire' => $user->getProprietaire()]);
        return $this->render('proprietaire/show.html.twig', [
            'stock' => $repos,
            'pagetitle'=>'Stock',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit, UserInterface $user): Response
    {
        $repos = $this->getDoctrine()->getRepository(Stock::class)->findOneBy(['produit' => $produit, 'proprietaire' => $user->getProprietaire()]);
        $form = $this->createForm(StockType::class, $repos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $image */

            $image = $form->get('produit')->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', "");
                $newFilename = $originalFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }
                $produit->setImage($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

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
        $repos = $this->getDoctrine()->getRepository(Stock::class)->findOneBy(['produit' => $produit, 'proprietaire' => $user->getProprietaire()]);
        if ($this->isCsrfTokenValid('delete'.$repos->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($repos);
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('stock_proprietaire');
    }
}
