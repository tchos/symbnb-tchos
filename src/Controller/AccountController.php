<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        //dump($error);
        // ici on capte le dernier user qui s'est connecté
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig',[
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     * 
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
     public function logout(){

     }

     /**
     * Permet d'afficher le formulaire d'inscription
     * 
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // gestion du cas des mots de passe du user qui s'inscrit
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            // pour la fenêtre d'alerte success confirmant la réussite de l'inscription
            $this->addFlash(
                'success',
                'Votre compte a bien été créé, vous pouvez maintenant vous connectez !'
            );

            // Redirection vers la page de connexion
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire de modification de profile
     * 
     * @Route("/account/profile", name="account_profile")
     * 
     * @return Response
     */
    public function profile(Request $request)
    {
        // Récupération du user connecté
        $user = $this->getUser();

        // Création du formulaire de modification
        $form = $this->createForm(AccountType::class, $user);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Les données du profil ont été enregistrées avec succès !');
        }
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire de modification du mot de passe
     * 
     * @Route("/account/update-password", name="account_password")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();

        // On recupère le user connecté
        $user = $this->getUser();

        // Création du formulaire de modification
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // On vérifie que le oldPassword du formulaire est le même que celui du user
            // alternative à la fonction password_verify = isValidPassword du UserPasswordEncoder
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash()))
            {
                // Gestion de l'erreur
                // $form->get('oldPassword') me donne accès au champ oldPassword du formulaire
                $form->get('oldPassword')->addError(new FormError("Le mot que vous avez tapé n'est pas
                    votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié !');

                // redirection vers la page d'accueil
                return $this->redirectToRoute('homepage');
            }
        }
        
        return $this->render('account/password.html.twig',[
            'form' =>$form->createView()
        ]);
    }

    /**
     * Permet d'afficher le compte de l'utilisateur connecté
     * 
     * @Route("/account", name="account_index")
     * 
     * @return Response
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
