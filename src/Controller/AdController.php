<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        // Récupération du repository qui gère l'entité "Ad"
        // $repo = $this->getDoctrine()->getRepository(Ad::class);

        // Recupération de toutes les données qui sont dans la table "Ad"
        $ads = $repo->findAll();

        /** 
         * la route "/ads" renvoie la page "index.html.twig" 
         * se trouvant dans le dossier "ad"
         * */
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("/ads/new", name="ads_create")
     * 
     * @return Response
     */
    //public function create(Request $request, ObjectManager $manager)
    public function create(Request $request)
    {
        $ad = new Ad();

        // Création d'un constructeur de formulaire en symfony
        $form = $this->createForm(AdType::class, $ad);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        /**
         * Ayant extrait les infos saisies dans le formulaire, 
         * on vérifie que le formulaire a été soumis et qu'il est valide
         * 
        */

        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image)
            {
                $image->setRelation($ad);
                $manager->persist($image);
            }

            // L'auteur de l'annonce sera le user connecté
            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            // Redirection vers la page qui va montrer l'annonce créée
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
        
        /** 
         * la route "/ads/new" renvoie la page "new.html.twig" 
         * se trouvant dans le dossier "ad"
         * */
        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une annonce
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @return Response
     */
    public function edit(Ad $ad, Request $request)
    {
        // Création d'un constructeur de formulaire en symfony
        $form = $this->createForm(AdType::class, $ad);

        // handlerequest() permet de parcourir la requête et d'extraire les informations du formulaire
        $form->handleRequest($request);

        /**
         * Ayant extrait les infos saisies dans le formulaire, 
         * on vérifie que le formulaire a été soumis et qu'il est valide
         * 
        */
        if($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();

            foreach($ad->getImages() as $image)
            {
                $image->setRelation($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            // Message d'alerte qui confirmera que l'annonce a été créée avec succès
            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
            );

            // Redirection vers la page qui va montrer l'annonce créée
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * Pour afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
     public function show(Ad $ad)
     {
         /** 
         * la route "/ads/{slug}" renvoie la page "show.html.twig" 
         * se trouvant dans le dossier "ad" 
         * qui sera la page de l'annonce rattachée au "slug"
         * 
         */
         return $this->render('ad/show.html.twig', [
             'ad' => $ad
         ]);
     }

     /**
        *public function show($slug, AdRepository $repo)
        *{
        *   //findBySlug = renvoie un tableau de slug
        *  // findOneBySlug = renvoie un seul slug
        *    $ad = $repo->findOneBySlug($slug);
        *
        *   // Réponse
        *  return $this->render('ad/show.html.twig', [
        *        'ad' => $ad
        *    ]);
        *} 
     */
}