<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if($date === null)
            return '';
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
        // $frenchDate est sous la forme 21/11/1989
        if($frenchDate === null)
        {
            // Exception =  on n'a pas rempli le champ de la date
            throw new TransformationFailedException("Vous devez fournir une date !");
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if($date === false)
        {
            // Exception = la date rempli n'est pas au bon format
            throw new TransformationFailedException("Le format de la date entrée n'est pas le bon !");
        }

        return $date;
    }
}