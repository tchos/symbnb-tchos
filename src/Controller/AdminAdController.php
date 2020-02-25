<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\Paginator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Cette page affichera la liste des annonces
     * 
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, Paginator $paginator)
    {
        // Service pour l'entité "Booking"
        $paginator->setEntityClass(Ad::class)
                  ->setPage($page);

        // liste des annonces: $ads = $paginator->getData();

        // nombre de pages: $pages = $paginator->getPages();

        return $this->render('admin/ad/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Permet à l'admin de modifier une annonce
     * 
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success',"L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !");
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet à l'admin de supprimer une annonce
     * 
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     *
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        // Une annonce ne peut être supprimée que s'il n'y a aucune réservation dessus
        if(count($ad->getBookings()) > 0)
        {
            // fenetre d'alert d'interdiction de suppression sur une annonce réservée
            $this->addFlash('warning', 
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> 
                car elle possède déjà une réservation.");
        } else
        {
            $manager->remove($ad);
            $manager->flush();

            // fenetre d'alert de suppression avec succès
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée.");
        }

        // Redirection vers la page des annonces du panel d'administration
        return $this->redirectToRoute('admin_ads_index');
    }
}
