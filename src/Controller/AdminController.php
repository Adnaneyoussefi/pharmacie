<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/login", name="login_admin")
     */
    public function login()
    {
        return $this->render('admin/login.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

     /**
     * @Route("/home", name="home_admin")
     */

    public function home() 
    {
        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
            'pagetitle'=>'',
            'path'=>'home_admin',

        ]);
    }

     /**
     * @Route("/listpharmacie", name="listpharmacie_admin")
     */

    public function listpharmacie()
    {
return $this->render('admin/list-pharmacie.html.twig', [
    'controller_name'=>'AdminController',
    'pagetitle'=>'Liste des Pharmacies',
    'path'=>'listpharmacie_admin',
]);

    }

    /**
     * @Route("/listclient", name="listclient_admin")
     */

    public function listclient()
    {
return $this->render('admin/list-client.html.twig', [
    'controller_name'=>'AdminController',
    'pagetitle'=>'Liste des Clients',
    'path'=>'listclient_admin',
]);
    }

    
    /**
     * @Route("/parametres", name="parametres_admin")
     */

    public function parametres()
    {
return $this->render('admin/parametres.html.twig', [
    'controller_name'=>'AdminController',
    'pagetitle'=>'parametres',
    'path'=>'parametres_admin',
]);
    }
}
