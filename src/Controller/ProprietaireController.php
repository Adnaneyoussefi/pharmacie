<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\UserPropType;
use App\Entity\Proprietaire;
use App\Form\PropChangeInfoPersoType;
use App\Form\PropChangePasswordType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function compte(/*AuthenticationUtils $authenticationUtils*/Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        //$repos = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $authenticationUtils->getLastUsername()]);
       
        $em = $this->getDoctrine()->getManager();
        $prop = $this->getUser();
       
        $formInfoPerso = $this->createForm(PropChangeInfoPersoType::class);
        $formInfoPerso->handleRequest($request);
        $formPassword = $this->createForm(PropChangePasswordType::class);
        $formPassword->handleRequest($request);
        if($formPassword->isSubmitted() && $formPassword->isValid()){
            $oldpassword = $request->request->get('prop_change_password')['oldpassword'];
            $newpassword = $request->request->get('prop_change_password')['confirmpassword']['first'];
            // Si l'ancien mot de passe est bon
         if($passwordEncoder->isPasswordValid($prop, $oldpassword)){
            $newEncodedPassword = $passwordEncoder->encodePassword($prop, $newpassword);
             $prop->setPassword($newEncodedPassword);
              $em->flush();
              $this->addFlash('notice', 'Votre mot de passe à bien été change !');
              return $this->redirectToRoute('compte_proprietaire');
          }
          else {
             $formPassword->get('oldpassword')->addError(new FormError('Ancien mot de passe incorrect'));
          }
        }
        if($formInfoPerso->isSubmitted() && $formInfoPerso->isValid()){
            $newnom = $request->request->get('prop_change_info_perso')['nom'];
            $newprenom = $request->request->get('prop_change_info_perso')['prenom'];
            $prop->setNom($newnom);
            $prop->setPrenom($newprenom);
            $em->flush();
            $this->addFlash('notice', 'Vos infos sont bien modifiés!');
            return $this->redirectToRoute('compte_proprietaire');        
        }
        
        return $this->render('proprietaire/compte.html.twig',[
            'pagetitle'=>'Compte',
            'path'=>'compte_proprietaire',
            'formPassword'=>$formPassword->createView(),
            'formInfoPerso'=>$formInfoPerso->createView()
            //'prenom'=>$repos->getPrenom()
        ]);
    }
}
