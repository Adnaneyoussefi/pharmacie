<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Produit;
use App\Data\SearchData;
use App\Entity\Commande;
use App\Form\SearchForm;
use App\Form\ProduitType;
use App\Form\UserPropType;
use App\Entity\Reclamation;
use App\Entity\Proprietaire;
use App\Form\ReclamationType;
use App\Entity\DetailsCommande;
use App\Form\PharmaChangeInfoType;
use App\Form\PropChangePasswordType;
use App\Form\PropChangeInfoPersoType;
use Symfony\Component\Form\FormError;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\PhpUnit\TextUI\Command;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/proprietaire")
 */

class ProprietaireController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = new User();
        $user->setRegistredAt(new \DateTime('now'));
        $user->setIsActive(false);
        $user->setStatut('Desactivé');
        $form = $this->createForm(UserPropType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $role[] = 'ROLE_PROP';
            $user->setRoles($role);
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('proprietaire/inscription.html.twig',[
            'pagetitle'=>'Inscription',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="home_proprietaire")
     */
    public function home(UserInterface $user)
    {
        $w=[];
        $produit = $this->getDoctrine()->getRepository(Produit::class)->GetNnProduit($user);
        $vente= $this->getDoctrine()->getRepository(DetailsCommande::class)->GetNnVente($user);
        //commande par date
      for($j=1; $j<=12; $j++){
        $us= $this->getDoctrine()->getRepository(DetailsCommande::class)->createQueryBuilder('D')
        ->select('count(D.id)')
        ->join('D.produit','p')
        ->join('D.commande','C')
        ->where('p.proprietaire = :prop')
        ->andwhere('MONTH(C.date) = :date')
        ->setParameter('prop', $user->getProprietaire())
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
        dump($p);
        return $this->render('proprietaire/acceuil.html.twig',[
            'pagetitle'=>'Home',
            'path'=>'home_proprietaire',
            'produit'=>$produit,
            'vente'=>$vente,
            'p'=>$p

        ]);
    }
    
     /**
     * @Route("/contact", name="contactprop")
     */

     public function contact(Request $request){
        
        $reclamation = new Reclamation();
        $user= $this->getUser();
        $formcontact = $this->createForm(ReclamationType::class, $reclamation);
        $formcontact->handleRequest($request);

        if($formcontact->isSubmitted() && $formcontact->isValid()){
        $reclamation->setEmmeteur('prop');
        $reclamation->setUser($user);
        $em = $this->getDoctrine()->getManager();

        $em->persist($reclamation);
        $em->flush();
        $this->addFlash('success', 'Votre réclamation a été envoyé !');

        return $this->redirectToRoute('contactprop');
        }
        return $this->render('proprietaire/contact.html.twig',[
            'pagetitle'=>'contact',
            'formcontact'=>$formcontact->createView()

        ]);
     }

     /**
     * @Route("/compte", name="compte_proprietaire")
     */
    public function compte(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {       
        $em = $this->getDoctrine()->getManager();
        $prop = $this->getUser();
       
        $formInfoPerso = $this->createForm(PropChangeInfoPersoType::class);
        $formInfoPerso->handleRequest($request);
        $formPassword = $this->createForm(PropChangePasswordType::class);
        $formPassword->handleRequest($request);
        $formInfoPharma = $this->createForm(PharmaChangeInfoType::class);
        $formInfoPharma->handleRequest($request);
        $active_tab1 = "active show";
        $active_tab2 = "";
        $active_tab3 = "";
        if($formPassword->isSubmitted() && $formPassword->isValid()){
            $oldpassword = $request->request->get('prop_change_password')['oldpassword'];
            $newpassword = $request->request->get('prop_change_password')['confirmpassword']['first'];

            $active_tab = $_POST['active_tab'];
            $active_tab1 = ($active_tab ==="tab_1") ? "active show" : "";
            $active_tab2 = ($active_tab ==="tab_2") ? "active show" : "";
            $active_tab3 = ($active_tab ==="tab_3") ? "active show" : "";
            // Si l'ancien mot de passe est bon
            if($passwordEncoder->isPasswordValid($prop, $oldpassword)){

                $newEncodedPassword = $passwordEncoder->encodePassword($prop, $newpassword);
                $prop->setPassword($newEncodedPassword);
                $em->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été changé !');
            }
            else {
                $formPassword->get('oldpassword')->addError(new FormError('Ancien mot de passe incorrect'));
            }
        }
        elseif ($formPassword->isSubmitted()) {
            $active_tab = $_POST['active_tab'];
            $active_tab1 = ($active_tab ==="tab_1") ? "active show" : "";
            $active_tab2 = ($active_tab ==="tab_2") ? "active show" : "";
            $active_tab3 = ($active_tab ==="tab_3") ? "active show" : "";
        }

        if($formInfoPerso->isSubmitted() && $formInfoPerso->isValid()){
            $newnom = $request->request->get('prop_change_info_perso')['nom'];
            $newprenom = $request->request->get('prop_change_info_perso')['prenom'];
            $prop->setNom($newnom);
            $prop->setPrenom($newprenom);
            $em->flush();
            $this->addFlash('success', 'Vos infos sont bien modifiés!');
            return $this->redirectToRoute('compte_proprietaire');        
        }
        if($formInfoPharma->isSubmitted() && $formInfoPharma->isValid()){

            $active_tab = $_POST['active_tab'];
            $active_tab1 = ($active_tab !=="tab_3") ? "active show" : "";
            $active_tab2 = ($active_tab !=="tab_3") ? "active show" : "";
            $active_tab3 = ($active_tab ==="tab_3") ? "active show" : "";

            $newnompharma = $request->request->get('pharma_change_info')['nom_pharmacie'];
            $newadressepharma = $request->request->get('pharma_change_info')['adresse_pharmacie'];
            $newvillepharma = $request->request->get('pharma_change_info')['ville'];
            $newtelpharma = $request->request->get('pharma_change_info')['tel'];
            $proprietaire = $prop->getProprietaire();
            $proprietaire->setNomPharmacie($newnompharma);
            $proprietaire->setAdresse($newadressepharma);
            $proprietaire->setVille($newvillepharma);
            $proprietaire->setTel($newtelpharma);

            $em->flush();
            $this->addFlash('success', 'Vos infos de pharmacie sont bien modifiés !');
        }
        return $this->render('proprietaire/compte.html.twig',[
            'pagetitle'=>'Compte',
            'path'=>'compte_proprietaire',
            'formPassword'=>$formPassword->createView(),
            'formInfoPerso'=>$formInfoPerso->createView(),
            'formInfoPharma'=>$formInfoPharma->createView(),
            'active_tab1' => $active_tab1,
            'active_tab2' => $active_tab2,
            'active_tab3' => $active_tab3,
            //'prenom'=>$repos->getPrenom()
        ]);
    }

    /**
     * @Route("/vente", name="vente_proprietaire")
     */
    public function vente(PaginatorInterface $paginator, UserInterface $user, Request $request)
    {
        $data = new SearchData();
        $form2 = $this->createForm(SearchForm::class, $data);
        $form2->handleRequest($request);
        
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findVentes($data, $user);
        
        $prixTotal = $this->getDoctrine()->getRepository(DetailsCommande::class)->findPrixTotal($user);

        $page = $paginator->paginate(
            $commandes,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('proprietaire/vente.html.twig',[
            'page'=> $page,
            'pagetitle'=>'Vente',
            'path'=>'home_proprietaire',
            'form2' => $form2->createView(),
            'prixTotal' => $prixTotal,
            'commandes' => $commandes 
        ]);
    }
      /**
     * @Route("/vente/{id}/annuler", name="vente_proprietaire_annuler")
     */
    public function annulervente($id){
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(DetailsCommande::class)->findOneBy(['id' => $id]);
        if($commande){
        $commande->setLivraison(null);
        $em->persist($commande);
        $em->flush();
        $this->addFlash('success', 'La commande n\'est pas envoyée !');
        return $this->redirectToRoute('vente_proprietaire');

    }}

    /**
     * @Route("/commande", name="commande_proprietaire")
     */
    public function commande(PaginatorInterface $paginator, UserInterface $user, Request $request)
    {
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findCommande($user);

        $page = $paginator->paginate(
            $commandes,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('proprietaire/commande.html.twig',[
            'page'=> $page,
            'pagetitle'=>'Commande',
            'path'=>'home_proprietaire',  
            'commandes' => $commandes          
        ]);
    }

    /**
     * @Route("/commande/{id}", name="commande_proprietaire_livré")
     */
    public function commandelivré($id){
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(DetailsCommande::class)->findOneBy(['id' => $id]);
        if($commande){
        $commande->setLivraison('oui');
        $em->persist($commande);
        $em->flush();
        $this->addFlash('success', 'La commande a été Transféré au statut Livré !');
        return $this->redirectToRoute('commande_proprietaire');

    }}

    /**
     * @Route("/commande/{id}/encours", name="commande_proprietaire_encours")
     */
    public function envoyercommande($id){
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(DetailsCommande::class)->findOneBy(['id' => $id]);
        if($commande){
        $commande->setLivraison('encours');
        $em->persist($commande);
        $em->flush();
        $this->addFlash('success', 'La commande a été Transféré au statut En Cours !');
        return $this->redirectToRoute('commande_proprietaire');

    }}

    /**
     * @Route("/commande/{id}/annuler", name="commande_proprietaire_annuler")
     */
    public function annulercommande($id){
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository(DetailsCommande::class)->findOneBy(['id' => $id]);
        if($commande){
        $commande->setLivraison(null);
        $em->persist($commande);
        $em->flush();
        $this->addFlash('success', 'La commande n\'est pas envoyée !');
        return $this->redirectToRoute('commande_proprietaire');

    }}

    /**
     * @Route("/commande/{id}/remove", name="commande_proprietaire_supp")
     */
    public function deletecommande($id) {

        $em = $this->getDoctrine()->getManager(); 
        $commande = $em->getRepository(DetailsCommande::class)->findOneBy(['id' => $id]);
        
        if($commande->getCommande()->getProduits()->count() == 1)
        {
        $em->remove($commande);
        $em->remove($commande->getCommande());
        }
        else{
            $em->remove($commande);
        }
        $em->flush();
        $this->addFlash('success', 'La commande a été annulée!');

        return $this->redirectToRoute('commande_proprietaire');
    }

}
