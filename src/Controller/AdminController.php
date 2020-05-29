<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Proprietaire;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function home() : Response
 //SELECT count(id) FROM `user` WHERE MONTH(registred_at)="5" and roles like '["ROLE_USER"]' --[\"ROLE_USER\"]
    {   
        $t=[];
        $w=[];
            for($i=1; $i<=12; $i++){
        $user = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.roles = :client')
        ->andwhere('MONTH(u.RegistredAt) = :date')
        ->setParameter('client', '["ROLE_USER"]')
        ->setParameter('date',$i)
        ->getQuery()
        ->getResult();
        array_push($t,$user);
                                 }
  
                foreach($t as $z=>$zvalue){
                    foreach($zvalue as $s=>$svalue){
                         foreach($svalue as $k=>$kvalue){  
                             $n[]=$kvalue; 
                                                        }
                                                     }
                                          }
                                          
         for($j=1; $j<=12; $j++){
        $us= $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('us')
        ->select('count(us.id)')
        ->where('us.roles = :client')
        ->andwhere('MONTH(us.RegistredAt) = :date')
        ->setParameter('client', '["ROLE_PROP"]')
        ->setParameter('date',$j)
        ->getQuery()
        ->getResult();
        array_push($w,$us);
                        }
        foreach($w as $z=>$zvalue){
            foreach($zvalue as $s=>$svalue){
                foreach($svalue as $k=>$kvalue){  
                    $p[]=$kvalue; 
                }
            }
        }
        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
            'pagetitle'=>'',
            'path'=>'home_admin',
            'users'=>$n,
            'p'=>$p
            
        ]);
   
    }

     /**
     * @Route("/listpharmacie", name="listpharmacie_admin")
     */

    public function listpharmacie(PaginatorInterface $paginator, Request $request): Response
    {

        $user = $this->getDoctrine()->getRepository(User::class)->findPharmacie();
        dump($user);
        $totalpharma = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.roles = :client')
        ->setParameter('client', '["ROLE_PROP"]')
        ->getQuery()
        ->getSingleScalarResult();
        
        if($request->isMethod("POST"))
        {
            $email = $request->get('email');
            $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('email'=>$email)); 
        }

        $page = $paginator->paginate(
            $user,
            $request->query->getInt('page', 1),
            1
        );
return $this->render('admin/list-pharmacie.html.twig', [
    'controller_name' => 'AdminController',
    'pagetitle' => 'Liste des Pharmacies',
    'path' => 'listpharmacie_admin',
    'user' => $user,
    'page' => $page,
    'totalpharma' => $totalpharma

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

     public function listclient(PaginatorInterface $paginator, Request $request):Response
    {
        
        $user = $this->getDoctrine()->getRepository(User::class)->findClients();
        dump($user);
        $totalclients = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.roles = :client')
        ->setParameter('client', '["ROLE_USER"]')
        ->getQuery()
        ->getSingleScalarResult();

        if($request->isMethod("POST"))
        {   
            $email = $request->get('email');
            $user = $this->getDoctrine()->getRepository(User::class)->findBy(array('email'=>$email)); 
        
        }

        $page = $paginator->paginate (
            $user,
            $request->query->getInt('page', 1),
            1
        );

        
        return $this->render('admin/list-client.html.twig', [
        'controller_name' => 'AdminController',
        'pagetitle' => 'Liste des Clients',
        'path' => 'listclient_admin',
        'user' => $user,
        'page' => $page,
        'totalclients' => $totalclients
        
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
