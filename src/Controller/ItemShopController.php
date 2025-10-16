<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\ItemShop;
use App\Entity\ItemShopOrder;
use App\Entity\ItemShopSlot;
use App\Entity\ItemShopType;
use App\Entity\Edition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ItemShopController extends FOGController
{
    #[Route("/order", name: "orderIndex")]
    public function orderIndex(EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $edition = $this->FogParams->getCurrentEdition();
        $types = $manager->getRepository(ItemShopType::class)->findAll();
        $slots = $manager->getRepository(ItemShopSlot::class)->findAll();
        $items = $manager->getRepository(ItemShop::class)->findAll();

        //dd($edition);

        return $this->render('oeilglauque/orderIndex.html.twig', [
            'edition' => $edition,
            'types' => $types,
            'slots' => $slots,
            'items' => $items,
            'newHeader' => true
        ]);
    }

    private function parseDate(string $date) {
        return DateTime::createFromFormat("Y-m-d*H:i", $date);
    }

    #[Route("/order/addSlot/{id}", name: "addSlot")]
    public function addSlot(Request $request, Edition $edition, EntityManagerInterface $manager) {
        $slot = new ItemShopSlot();
        $slot->setDeliveryTime($this->parseDate($request->query->get('deliveryTime')));
        $slot->setOrderTime($this->parseDate($request->query->get('orderTime')));
        if ($request->query->get('preOrderTime') != null) {
            $slot->setPreOrderTime($this->parseDate($request->query->get('preOrderTime')));
        }
        if ($request->query->get('maxOrder') != null) {
            $slot->setMaxOrder($request->query->get('maxOrder'));
        }

        if ($slot->getDeliveryTime() < $slot->getOrderTime() || $slot->getOrderTime() < $slot->getPreOrderTime()) {
            $this->addFlash('danger', "Les horaires ne correspondent pas");
            return $this->redirectToRoute('orderIndex');
        }
        
        if (!$edition) {
            throw $this->createNotFoundException(
                'Aucune édition n\'a pour id '.$edition
            );
        }
        $slot->setEdition($edition);

        $typeval = $manager->getRepository(ItemShopType::class)->find($request->query->get('type'));
        if (!$typeval) {
            throw $this->createNotFoundException(
                'Le type fourni n\'existe pas'
            );
        }
        $slot->setType($typeval);

        $manager->persist($slot);
        $manager->flush();
        $this->addFlash('success', "Le créneau a bien été ajouté. ");

        return $this->redirectToRoute('orderIndex');
    }

    #[Route("/order/addItem/{id}", name: "addItem")]
    public function addItem(Request $request, Edition $edition, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $item = new ItemShop();
        $item->setName($request->query->get('name'));
        $item->setDescription($request->query->get('description'));
        $item->setPrice($request->query->get('price'));
        $item->setHelperPrice($request->query->get('helperprice'));

        if (!$edition) {
            throw $this->createNotFoundException(
                'Aucune édition n\'a pour id '.$edition
            );
        }
        $item->setEdition($edition);

        $typeval = $manager->getRepository(ItemShopType::class)->find($request->query->get('type'));
        if (!$typeval) {
            throw $this->createNotFoundException(
                'Le type fourni n\'existe pas'
            );
        }
        $item->setType($typeval);
        $manager->persist($item);
        $manager->flush();
        $this->addFlash('success', "Le produit " . $item->getName() . " a bien été ajouté. ");

        return $this->redirectToRoute('orderIndex');
    }

    #[Route("/order/addType", name: "addType")]
    public function addType(Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($request->query->get('name') != "") {
            $type = new ItemShopType();
            $type->setType($request->query->get('name'));
            $manager->persist($type);
            $manager->flush();
            $this->addFlash('success', "Le type " . $type->getType() . " a bien été ajouté. ");
        }

        return $this->redirectToRoute('orderIndex');
    }

    #[Route("/order/deleteSlot/{id}", name: "deleteSlot")]
    public function deleteSlot(ItemShopSlot $slot, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($slot) {
            $manager->remove($slot);
            $manager->flush();
            $this->addFlash('success', "Le créneau a bien été supprimé.");
        }

        return $this->redirectToRoute('orderIndex');
    }

    #[Route("/order/deleteItem/{id}", name: "deleteItem")]
    public function deleteItem(ItemShop $item, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($item) {
            $manager->remove($item);
            $manager->flush();
            $this->addFlash('success', "Le produit a bien été supprimé.");
        }

        return $this->redirectToRoute('orderIndex');
    }

    #[Route("/order/{id}", name: "orderList")]
    public function orderList(ItemShopSlot $slot, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$slot) {
            $this->addFlash('danger', "Le slot n'existe pas.");
            return $this->redirectToRoute('orderIndex');
        }

        $edition = $this->FogParams->getCurrentEdition();
        $currslot = $slot;
        $types = $manager->getRepository(ItemShopType::class)->findAll();
        $slots = $manager->getRepository(ItemShopSlot::class)->findAll();
        $items = $manager->getRepository(ItemShop::class)->findBy(["type" => $slot->getType()]);
        $orders = $manager->getRepository(ItemShopOrder::class)->findBy(["slot" => $slot]);
        $groupedOrders1 = [];
        $groupedOrders2 = [];
        if ($currslot->getPreOrderTime() != null) {
            $groupedOrders1 = $manager
                ->createQuery('SELECT COUNT(o.id) as itemcount, i.name as item FROM App\Entity\ItemShopOrder o JOIN App\Entity\ItemShop i WHERE o.slot = :slot AND (o.item) = i.id AND o.time < :preorder GROUP BY i.id')
                ->setParameters([
                    'slot' => $currslot,
                    'preorder' => $currslot->getPreOrderTime()
                ])
                ->getResult();
            $groupedOrders2 = $manager
                ->createQuery('SELECT COUNT(o.id) as itemcount, i.name as item FROM App\Entity\ItemShopOrder o JOIN App\Entity\ItemShop i WHERE o.slot = :slot AND (o.item) = i.id AND o.time > :preorder GROUP BY i.id')
                ->setParameters([
                    'slot' => $currslot,
                    'preorder' => $currslot->getPreOrderTime()
                ])
                ->getResult();
        } else {
            $groupedOrders1 = $manager
                ->createQuery('SELECT COUNT(o.id) as itemcount, i.name as item FROM App\Entity\ItemShopOrder o JOIN App\Entity\ItemShop i WHERE o.slot = :slot AND (o.item) = i.id GROUP BY i.id')
                ->setParameters([
                    'slot' => $currslot
                ])
                ->getResult();
        }

        return $this->render('oeilglauque/orderList.html.twig', [
            'edition' => $edition,
            'slots' => $slots,
            'orders' => $orders,
            'groupedOrders1' => $groupedOrders1,
            'groupedOrders2' => $groupedOrders2,
            'types' => $types,
            'items' => $items,
            'currentSlot' => $currslot
        ]);
    }

    #[Route("/order/addOrder/{id}", name: "addOrder")]
    public function addOrder(Request $request, ItemShopSlot $slot, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order = new ItemShopOrder();

        if (!$slot) {
            $this->addFlash('danger', "Le slot n'existe pas.");
            return $this->redirectToRoute('orderIndex');
        }
        $order->setSlot($slot);

        $itemval = $manager->getRepository(ItemShop::class)->find($request->query->get('item'));

        if (!$itemval) {
            $this->addFlash('danger', "Le slot n'existe pas.");
            return $this->redirectToRoute('orderList', ["id" => $slot->getId()]);
        }

        $date = new DateTime("now", new DateTimeZone('Europe/Paris'));
        $dateMax = $slot->getOrderTime();

        if ($date->format('Dd') > $dateMax->format('Dd') or $date->format('H:i:s') > $dateMax->format('H:i:s')){
            $this->addFlash('popup', "Heure limite de commande dépassé...");
            return $this->redirectToRoute('orderList', ["id" => $slot->getId()]);
        }

        $orders = $manager->getRepository(ItemShopOrder::class)->findBy(["slot" => $slot]);
        if ($slot->getMaxOrder() != null && count($orders) >= $slot->getMaxOrder()) {
            $this->addFlash('popup', "Le nombre maximal de commande pour ce créneau a été atteint.");
            return $this->redirectToRoute('orderList', ["id" => $slot->getId()]);
        }


        $order->setItem($itemval);

        $order->setPseudo($request->query->get('pseudo'));
        $order->setTime($date);
        $manager->persist($order);
        $manager->flush();
        $this->addFlash('success', "La commande de " . $order->getPseudo() . " a bien été ajouté. ");


        return $this->redirectToRoute('orderList', ["id" => $slot->getId()]);
    }

    #[Route("/order/collectOrder/{id}/{state}", name: "collectOrder")]
    public function collectOrder(Request $request, $id, $state, EntityManagerInterface $manager) : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order = $manager->getRepository(ItemShopOrder::class)->find($id);
        $order->setCollected(!$state);
        $manager->flush();

        if ($request->getContent() == "update") {
            return new JsonResponse(json_encode(!$state),json: true);
        } else {
            $this->addFlash('success', "La commande de " . $order->getPseudo() . " a bien été livré. ");
            return $this->redirectToRoute('orderList', ["id" => $order->getSlot()->getId()]);
        }
    }

    #[Route("/order/deleteOrder/{id}", name: "deleteOrder")]
    public function deleteOrder(ItemShopOrder $order, EntityManagerInterface $manager) : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($order) {
            $manager->remove($order);
            $manager->flush();
            $this->addFlash('success', "La commande de " . $order->getPseudo() . " a bien été supprimé.");
        }

        return $this->redirectToRoute('orderList', ["id" => $order->getSlot()->getId()]);
    }
}