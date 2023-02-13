<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

use App\Form\DishFormType;
use App\Entity\Dish;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\DishRepository;
use App\Repository\SeatsRepository;

class CRUDController extends AbstractController
{
    #[Route('/add_something', name: 'app_add_something')]
    public function addSomething(
        SeatsRepository $seatsRepository,
        MenuRepository $menuRepository,
        DishRepository $dishRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response {

        $check_type = $menuRepository->findOneBy([
            'type' => 'halal'
        ]);

        if($check_type == NULL)
        {
            $menu = new Menu();
            $menu->setType('Halal');

            $entityManager = $doctrine->getManager();
            $entityManager->persist($menu);
            $entityManager->flush();
        }

        $dishHalal = $dishRepository->findBy(
            ['type' => 'halal'],
            ['type' => 'ASC']
        );

        $db = $doctrine->getManager()->getConnection();
        $response = [];
        $mail = 'sokhona.salaha@gmail.com';
        $surname = 'Sokhona';

        $check_reservation = $db
        ->prepare(
            "SELECT surname, name, ReservationDate, hour
            FROM reservation
            WHERE mail = ?
            AND
            surname = ?"
        )
        ->executeQuery([$mail, $surname])
        ->fetchAllAssociative();

        if($check_reservation) {

            $surname = [];
            $name = [];
            $date = [];
            $hour = [];

            foreach($check_reservation as $reservation) {
                $surname[] = $reservation['surname'];
                $name[] = $reservation['name'];
                $date[] = $reservation['ReservationDate'];
                $hour[] = $reservation['hour'];
            }

            $response['surname'] = $surname;
            $response['name'] = $name;
            $response['date'] = $date;
            $response['hour'] = $hour;
        }


        return new Response(var_dump($response));
    }

    #[Route('/add_dish', name: 'app_add_dish')]
    public function addDish(
        MenuRepository $menuRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        
        $menus = $menuRepository->findAll();
        $dishEntity = new Dish();
        $dishForm = $this->createForm(DishFormType::class, $dishEntity);

        $dishForm->handleRequest($request);
        if ($dishForm->isSubmitted()) {
            $name = $_POST['dish_form']['name'];
            $description = $_POST['dish_form']['description'];
            $price = $_POST['dish_form']['price'];
            $type = $menuRepository->find($_POST['dish_type']);

            $dishEntity->setName($name);
            $dishEntity->setDescription($description);
            $dishEntity->setPrice($price);
            $dishEntity->setType($type);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($dishEntity);
            $entityManager->flush();

            return new Response('Plat enregistré !');
        }

        return $this->render('form/dish.html.twig', [
            'dishForm' => $dishForm->createView(),
            'menus' => $menus,
        ]);
    }
}
