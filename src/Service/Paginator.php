<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Paginator
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    // Constructeur: il va nous permettre de récupérer le Repository de l'entité
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request,
        $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher un template twig particulier
     *
     * @return Template
     */
    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route,
        ]);
    }

    /**
     * Fonction qui retournera le nombre total de pages
     *
     * @return Integer
     */
    public function getPages()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !
                 Utilisez la méthode setEntityClass() de votre objet Paginator !");
        }
        // 1) On recupère le repository de l'entité et on calcumle le nombre d'enregistrements sur l'entité
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) Calcul du nombre de pages
        // la fonction "ceil()" arrondi un nombre décimal à l'entier supérieur. Ex: 3,4 => 4
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    /**
     * Méthode qui va récupérer les enregistrements sur une entité
     *
     * @return Response
     */
    public function getData()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer !");
        }
        // 1) Calcul de l'offset (le début à partir du quel on va récupérer les données)
        $offset = ($this->currentPage * $this->limit) - $this->limit;

        // 2) On recupère le repository de l'entité et on lui demande de nous fournir les données
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // 3) On renvoie les données
        return $data;
    }

    // Getters et Setters
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }

    public function getPage()
    {
        return $this->currentPage;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }
}

?>