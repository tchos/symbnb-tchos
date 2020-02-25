<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Permet à l'admin de lister les réservations
     * 
     * @Route("/admin/bookings", name="admin_booking_index")
     */
    public function index(BookingRepository $repo)
    {
        // Récupération de la liste des réservations
        $bookings = $repo->findAll();

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings
        ]);
    }

    /**
     * Permet à l'admin de modifier une réservation
     *
     * @Route("admin/bookings/{id}/edit", name="admin_booking_edit")
     * 
     * @param Booking $booking
     * @return Response
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /**  
             * En mettant le montan à 0, le montant sera automatiquement recalculé, voir
             * l'entité Booking avec le cycle de vie "@ORM\PreUpdate" et la fonction "prePersist"
             * */
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();

            $this->addFlash('success', 
                "La réservation n° <strong>{$booking->getId()}</strong> a bien été modifiée.");
            
            // Redirection vers la page listant les réservation si succès de la modification
            return $this->redirectToRoute('admin_booking_index');
        }
        
        return $this->render('admin/booking/edit.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Permete à l'admin de supprimer une réservation
     *
     * @Route("admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @return Response
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash('success', 
            "La réservation de <strong>{$booking->getBooker()->getFullName()}</strong> sur l'annonce
             <strong>{$booking->getAd()->getTitle()}</strong> a bien été supprimée.");

        return $this->redirectToRoute('admin_booking_index');
    }
}
