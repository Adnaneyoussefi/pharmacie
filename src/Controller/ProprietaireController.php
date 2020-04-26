<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/proprietaire")
 */

class ProprietaireController extends AbstractController
{
    /**
     * @Route("/", name="home_proprietaire")
     */
    public function home()
    {
        return $this->render('proprietaire/acceuil.html.twig',[
            'pagetitle'=>'Home',
            'path'=>'home_proprietaire',
        ]);
    }

     /**
     * @Route("/compte", name="compte_proprietaire")
     */
    public function compte()
    {
        return $this->render('proprietaire/compte.html.twig',[
            'pagetitle'=>'Compte',
            'path'=>'compte_proprietaire',
        ]);
    }

     /**
     * @Route("/stock", name="stock_proprietaire")
     */
    public function stock()
    {
        return $this->render('proprietaire/stock.html.twig',[
            'pagetitle'=>'Stock',
            'path'=>'stock_proprietaire',
        ]);
    }
}
