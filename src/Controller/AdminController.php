<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Proprietaire;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/login", name="login_admin")
     */
    public function login( AuthenticationUtils $authenticationUtils)
    {

        $errors = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'controller_name' => 'AdminController',
            'errors' => $errors,
            'username' => $lastUsername
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

    public function listpharmacie(UserRepository $userRepository)
    {

        $user = $this->getDoctrine()->getRepository(Proprietaire::class)->findAll();
        dump($user);

return $this->render('admin/list-pharmacie.html.twig', [
    'controller_name'=>'AdminController',
    'pagetitle'=>'Liste des Pharmacies',
    'path'=>'listpharmacie_admin',
    'user' => $user

]);

    }

      /**
     * @Route("/listpharmacie/{id}", name="listpharmacie_admin_remove")
     */


    public function deletepharmacie($id) {

        $em = $this->getDoctrine()->getManager(); 
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
       dump($user);
         $em->remove($user);
          $em->flush();
 
         return $this->redirectToRoute('listpharmacie_admin');
    }

    

    /**
     * @Route("/listclient", name="listclient_admin")
     */

     public function listclient(UserRepository $userRepository)
    {
        
         $user = $this->getDoctrine()->getRepository(Client::class)->findAll();
         dump($user);
    return $this->render('admin/list-client.html.twig', [
     'controller_name'=>'AdminController',
       'pagetitle'=>'Liste des Clients',
     'path'=>'listclient_admin',
      'user' => $user
     ]);
    
    }

     /**
     * @Route("/listclient/{id}", name="listclient_admin_remove")
     */


    public function deleteclient($id) {

        $em = $this->getDoctrine()->getManager(); 
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
       dump($user);
         $em->remove($user);
          $em->flush();
 
         return $this->redirectToRoute('listclient_admin');
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
