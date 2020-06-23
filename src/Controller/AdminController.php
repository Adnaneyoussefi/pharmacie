<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Visite;
use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Proprietaire;
use App\Entity\Reclamation;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
     * @Route("/logout", name="admin_logout")
     */
    public function logout()
    {
        session_destroy();
        return $this->redirectToRoute('login_admin');


    }
     /**
     * @Route("/home", name="home_admin")
     */

    public function home() : Response
    {   
    //statistique nombre d inscription client/pharmacie
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
        //nombre totale des pharmacies
        $totalpharma = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.roles = :client')
        ->setParameter('client', '["ROLE_PROP"]')
        ->getQuery()
        ->getSingleScalarResult();
        //nombre totale des clients
        $totalclients = $this->getDoctrine()->getRepository(User::class)->createQueryBuilder('u')
        ->select('count(u.id)')
        ->where('u.roles = :client')
        ->setParameter('client', '["ROLE_USER"]')
        ->getQuery()
        ->getSingleScalarResult();
        //nombre de visite du page Client/index
        $nb_visite = $this->getDoctrine()->getManager(); 
      $nb = $nb_visite->getRepository(Visite::class)->findOneBy(['id' => '1']);
        //nombre des pharmacies/clients par region
       $client_Oriental = $this->getDoctrine()->getRepository(Client::class)->OrientaleCli();
       $prop_Oriental = $this->getDoctrine()->getRepository(Proprietaire::class)->OrientaleProp();
       $TangerCli= $this->getDoctrine()->getRepository(Client::class)->TangertetouanAlhoceimaCli();
       $TangerProp= $this->getDoctrine()->getRepository(Proprietaire::class)->TangertetouanAlhoceimaProp();
       $FesMeknesCli = $this->getDoctrine()->getRepository(Client::class)->FesMeknesCli();
       $FesMeknesProp = $this->getDoctrine()->getRepository(Proprietaire::class)->FesMeknesProp();
       $CasablancaSettatCli = $this->getDoctrine()->getRepository(Client::class)->CasablancaSettatCli();
       $CasablancaSettatProp = $this->getDoctrine()->getRepository(Proprietaire::class)->CasablancaSettatProp();
       $RabatSaleKenitraCli = $this->getDoctrine()->getRepository(Client::class)->RabatSaleKenitraCli();
       $RabatSaleKenitraProp = $this->getDoctrine()->getRepository(Proprietaire::class)->RabatSaleKenitraProp();
       //RECLAMATIONS
       $totalreclamationsprop = $this->getDoctrine()->getRepository(Reclamation::class)->createQueryBuilder('r')
        ->select('count(r.id)')
        ->where('r.emmeteur = :emmeteur')
        ->setParameter('emmeteur', 'prop')
        ->getQuery()
        ->getSingleScalarResult();
        $totalreclamationsclient = $this->getDoctrine()->getRepository(Reclamation::class)->createQueryBuilder('r')
        ->select('count(r.id)')
        ->where('r.emmeteur = :emmeteur')
        ->setParameter('emmeteur', 'client')
        ->getQuery()
        ->getSingleScalarResult();

        return $this->render('admin/home.html.twig', [
            'controller_name' => 'AdminController',
            'pagetitle'=>'',
            'path'=>'home_admin',
            'users'=>$n,
            'p'=>$p,
            'totalpharma'=> $totalpharma,
            'totalclients'=> $totalclients,
            'nombre_visite'=> $nb->getNbVisite(),
            'orientalP'=>$prop_Oriental,
            'orientalC'=>$client_Oriental,
            'orientaltotal'=>$client_Oriental+$prop_Oriental,
            'reclamation'=>$totalreclamationsclient+$totalreclamationsprop,
            'TangerCli'=>$TangerCli,
            'TangerProp'=>$TangerProp,
            'tangerTotale'=>$TangerCli+$TangerProp,
            'FesMeknesCli'=>$FesMeknesCli,
            'FesMeknesProp'=>$FesMeknesProp,
            'FesMeknesTotale'=>$FesMeknesProp+$FesMeknesCli,
            'CasablancaSettatCli'=>$CasablancaSettatCli,
            'CasablancaSettatProp'=>$CasablancaSettatProp,
            'CasablancaSettatTotale'=>$CasablancaSettatCli+$CasablancaSettatProp,
            'RabatSaleKenitraProp'=>$RabatSaleKenitraProp,
            'RabatSaleKenitraCli'=>$RabatSaleKenitraCli,
            'RabatSaleKenitraTotale'=>$RabatSaleKenitraProp+$RabatSaleKenitraCli,
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
        $totalreclamationsprop = $this->getDoctrine()->getRepository(Reclamation::class)->createQueryBuilder('r')
        ->select('count(r.id)')
        ->where('r.emmeteur = :emmeteur')
        ->setParameter('emmeteur', 'prop')
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
            3
        );
return $this->render('admin/list-pharmacie.html.twig', [
    'controller_name' => 'AdminController',
    'pagetitle' => 'Liste des Pharmacies',
    'path' => 'listpharmacie_admin',
    'user' => $user,
    'page' => $page,
    'totalpharma' => $totalpharma,
    'totalreclamationsprop' => $totalreclamationsprop

]);

    }
    /**
     * @Route("/reclamationpharmacie/", name="list_reclamation_pharmacie")
     */
    public function listreclamationpharmacie(PaginatorInterface $paginator, Request $request){

        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findreclmationsprop();


        $page = $paginator->paginate (
            $reclamation,
            $request->query->getInt('page', 1),
            5
        );
       
    return $this->render('admin/list-reclamation-pharmacie.html.twig', [
    'controller_name' => 'AdminController',
    'pagetitle' => 'Liste des reclamation pharmacie',
    'reclamation'=>$reclamation,
    'page'=>$page
    ]);
}

       /**
     * @Route("/reclamationpharmacie/{id}", name="list_reclamation_pharmacie_remove")
     */

    public function deletreclamationpharmacie($id) {

        $em = $this->getDoctrine()->getManager(); 
        $reclamation = $em->getRepository(Reclamation::class)->findOneBy(['id' => $id]);
       dump($reclamation);
         $em->remove($reclamation);
          $em->flush();
          $this->addFlash('success', 'Vous avez supprimer la reclamation!');
    
         return $this->redirectToRoute('list_reclamation_pharmacie');
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
          $this->addFlash('success', 'Vous avez supprimer le compte avec succés !');
         return $this->redirectToRoute('listpharmacie_admin');
    }

     /**
     * @Route("/listpharmacie/{id}/activate", name="listpharmacie_admin_activer")
     */


    public function activatepharmacie($id) {

        $em = $this->getDoctrine()->getManager(); 
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
       dump($user);
       if($user->getIsActive() === false){
           $user->setIsActive(true);
           $user->setStatut('Activé');
         $em->persist($user);
          $em->flush();
          $this->addFlash('success', 'Vous avez activé le compte avec succées !');
           } else {
            $this->addFlash('alert', 'Ce compte est déjà activé!');
    }
    return $this->redirectToRoute('listpharmacie_admin');

    }

     /**
     * @Route("/listpharmacie/{id}/desactivate", name="listpharmacie_admin_desactiver")
     */


    public function desactivatepharmacie($id) {

        $em = $this->getDoctrine()->getManager(); 
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
       dump($user);
       if($user->getIsActive()=== true){
           $user->setIsActive(false);
           $user->setStatut('Desactivé');
         $em->persist($user);
          $em->flush();
          $this->addFlash('success', 'Vous avez desactiver le compte avec succées !');
           } else {
            $this->addFlash('alert', 'ce compte est déjà desactivé!');
    }
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
        $totalreclamationsclient = $this->getDoctrine()->getRepository(Reclamation::class)->createQueryBuilder('r')
        ->select('count(r.id)')
        ->where('r.emmeteur = :emmeteur')
        ->setParameter('emmeteur', 'client')
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
            3
        );

        
        return $this->render('admin/list-client.html.twig', [
        'controller_name' => 'AdminController',
        'pagetitle' => 'Liste des Clients',
        'path' => 'listclient_admin',
        'user' => $user,
        'page' => $page,
        'totalclients' => $totalclients,
        'totalreclamationsclient' => $totalreclamationsclient
        
    ]);
    
    }
     /**
     * @Route("/reclamationclient/", name="list_reclamation_client")
     */
    public function listreclamationclient(PaginatorInterface $paginator, Request $request){

        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findreclmationsclient();

        $page = $paginator->paginate (
            $reclamation,
            $request->query->getInt('page', 1),
            5
        );
    return $this->render('admin/list-reclamation-client.html.twig', [
    'controller_name' => 'AdminController',
    'pagetitle' => 'Liste des reclamations client',
    'reclamation'=>$reclamation,
    'page'=>$page
    ]);
}
     /**
     * @Route("/reclamationclient/{id}", name="list_reclamation_client_remove")
     */

public function deletreclamationclient($id) {

    $em = $this->getDoctrine()->getManager(); 
    $reclamation = $em->getRepository(Reclamation::class)->findOneBy(['id' => $id]);
   dump($reclamation);
     $em->remove($reclamation);
      $em->flush();
      $this->addFlash('success', 'Vous avez supprimer la reclamation!');
     return $this->redirectToRoute('list_reclamation_client');
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
          $this->addFlash('success', 'Vous avez supprimer le compte avec succés !');

         return $this->redirectToRoute('listclient_admin');
    }


    /**
     * @Route("/listclient/{id}/activate", name="listclient_admin_activer")
     */


    public function activateclient($id) {

        $em = $this->getDoctrine()->getManager(); 
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
       dump($user);
       if($user->getIsActive() === false){
           $user->setIsActive(true);
           $user->setStatut('Activé');
         $em->persist($user);
          $em->flush();
          $this->addFlash('success', 'Vous avez activé le compte avec succées !');
           } else {
            $this->addFlash('alert', 'Ce compte est déjà activé!');
    }
    return $this->redirectToRoute('listclient_admin');
    }

     /**
     * @Route("/listclient/{id}/desactivate", name="listclient_admin_desactiver")
     */


    public function desactivateclient($id) {

        $em = $this->getDoctrine()->getManager(); 
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);
       dump($user);
       if($user->getIsActive()=== true){
           $user->setIsActive(false);
           $user->setStatut('Desactivé');
         $em->persist($user);
          $em->flush();
          $this->addFlash('success', 'Vous avez desactiver le compte avec succées !');
           } else {
            $this->addFlash('alert', 'ce compte est déjà desactivé!');
    }
    return $this->redirectToRoute('listclient_admin');
    }

    /**
     * @Route("/parametres", name="parametres_admin")
     */

    public function parametres(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
         $form = $this->createForm(ChangePasswordType::class);
 
         $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()){
     
             $oldpassword = $request->request->get('change_password')['oldpassword'];
             $newpassword = $request->request->get('change_password')['comnfirmpassword']['first'];
         
         // Si l'ancien mot de passe est bon
         if($passwordEncoder->isPasswordValid($user, $oldpassword)){
           $newEncodedPassword = $passwordEncoder->encodePassword($user, $newpassword);
            $user->setPassword($newEncodedPassword);
           
 
             $em->flush();
             $this->addFlash('notice', 'Votre mot de passe à bien été change !');
 
             return $this->redirectToRoute('parametres_admin');
         }
         else {
             //$this->addFlash('danger', 'Ancien mot de passe incorrect !');
 
            $form->get('oldpassword')->addError(new FormError('Ancien mot de passe incorrect'));
         }
     }
  
 

return $this->render('admin/parametres.html.twig', [
    'controller_name'=>'AdminController',
    'pagetitle'=>'parametres',
    'path'=>'parametres_admin',
    'form'=>$form->createView()
]);
    }
}
