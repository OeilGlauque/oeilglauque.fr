<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\ItemShop;
use App\Entity\ItemShopOrder;
use App\Entity\ItemShopSlot;
use App\Entity\ItemShopType;
use App\Entity\Edition;
use App\Entity\Feature;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Dotenv\Dotenv;

use Psr\Log\LoggerInterface;

class ItemShopController extends FOGController
{
    /**
     * @Route("/order", name="orderIndex")
     */
    public function orderIndex(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $edition = $this->getCurrentEdition();
        $types = $this->getDoctrine()->getRepository(ItemShopType::class)->findAll();
        $slots = $this->getDoctrine()->getRepository(ItemShopSlot::class)->findAll();
        $items = $this->getDoctrine()->getRepository(ItemShop::class)->findAll();

        return $this->render('oeilglauque/orderIndex.html.twig', array(
            'edition' => $edition,
            'types' => $types,
            'slots' => $slots,
            'items' => $items
        ));
    }

    private function parseDate(string $date) {
        return DateTime::createFromFormat("Y-m-d*H:i", $date);
    }

    /**
     * @Route("/order/addSlot/{edition}", name="addSlot")
     */
    public function addSlot(Request $request, $edition, LoggerInterface $logger) {
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
        
        $editionval = $this->getDoctrine()->getRepository(Edition::class)->find($edition);
        if (!$editionval) {
            throw $this->createNotFoundException(
                'Aucune édition n\'a pour id '.$edition
            );
        }
        $slot->setEdition($editionval);

        $typeval = $this->getDoctrine()->getRepository(ItemShopType::class)->find($request->query->get('type'));
        if (!$typeval) {
            throw $this->createNotFoundException(
                'Le type fourni n\'existe pas'
            );
        }
        $slot->setType($typeval);

        $this->getDoctrine()->getManager()->persist($slot);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "Le créneau a bien été ajouté. ");

        return $this->redirectToRoute('orderIndex');
    }

    /**
     * @Route("/order/addItem/{edition}", name="addItem")
     */
    public function addItem(Request $request, $edition)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $item = new ItemShop();
        $item->setName($request->query->get('name'));
        $item->setDescription($request->query->get('description'));
        $item->setPrice($request->query->get('price'));

        $editionval = $this->getDoctrine()->getRepository(Edition::class)->find($edition);
        if (!$editionval) {
            throw $this->createNotFoundException(
                'Aucune édition n\'a pour id '.$edition
            );
        }
        $item->setEdition($editionval);

        $typeval = $this->getDoctrine()->getRepository(ItemShopType::class)->find($request->query->get('type'));
        if (!$typeval) {
            throw $this->createNotFoundException(
                'Le type fourni n\'existe pas'
            );
        }
        $item->setType($typeval);
        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "Le produit " . $item->getName() . " a bien été ajouté. ");

        return $this->redirectToRoute('orderIndex');
    }

    /**
     * @Route("/order/addType", name="addType")
     */
    public function addType(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($request->query->get('name') != "") {
            $type = new ItemShopType();
            $type->setType($request->query->get('name'));
            $this->getDoctrine()->getManager()->persist($type);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Le type " . $type->getType() . " a bien été ajouté. ");
        }

        return $this->redirectToRoute('orderIndex');
    }

    /**
     * @Route("/order/deleteSlot/{id}", name="deleteSlot")
     */
    public function deleteSlot(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $slot = $this->getDoctrine()->getRepository(ItemShopSlot::class)->find($id);
        if ($slot) {
            $this->getDoctrine()->getManager()->remove($slot);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Le créneau a bien été supprimé.");
        }

        return $this->redirectToRoute('orderIndex');
    }

    /**
     * @Route("/order/deleteItem/{id}", name="deleteItem")
     */
    public function deleteItem(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $item = $this->getDoctrine()->getRepository(ItemShop::class)->find($id);
        if ($item) {
            $this->getDoctrine()->getManager()->remove($item);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Le créneau a bien été supprimé.");
        }

        return $this->redirectToRoute('orderIndex');
    }

    /**
     * @Route("/order/{slot}", name="orderList")
     */
    public function orderList(Request $request, $slot)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $slotval = $this->getDoctrine()->getRepository(ItemShopSlot::class)->find($slot);
        if (!$slotval) {
            $this->addFlash('danger', "Le slot n'existe pas.");
            return $this->redirectToRoute('orderIndex');
        }

        $edition = $this->getCurrentEdition();
        $currslot = $slotval;
        $types = $this->getDoctrine()->getRepository(ItemShopType::class)->findAll();
        $slots = $this->getDoctrine()->getRepository(ItemShopSlot::class)->findAll();
        $items = $this->getDoctrine()->getRepository(ItemShop::class)->findBy(["type" => $slotval->getType()]);
        $orders = $this->getDoctrine()->getRepository(ItemShopOrder::class)->findBy(["slot" => $slotval]);
        $groupedOrders1 = array();
        $groupedOrders2 = array();
        if ($currslot->getPreOrderTime() != null) {
            $groupedOrders1 = $this->getDoctrine()
                ->getManager()
                ->createQuery('SELECT COUNT(o.id) as itemcount, i.name as item FROM App\Entity\ItemShopOrder o JOIN App\Entity\ItemShop i WHERE o.slot = :slot AND (o.item) = i.id AND o.time < :preorder GROUP BY i.id')
                ->setParameters(array(
                    'slot' => $currslot,
                    'preorder' => $currslot->getPreOrderTime()
                ))
                ->getResult();
            $groupedOrders2 = $this->getDoctrine()
                ->getManager()
                ->createQuery('SELECT COUNT(o.id) as itemcount, i.name as item FROM App\Entity\ItemShopOrder o JOIN App\Entity\ItemShop i WHERE o.slot = :slot AND (o.item) = i.id AND o.time > :preorder GROUP BY i.id')
                ->setParameters(array(
                    'slot' => $currslot,
                    'preorder' => $currslot->getPreOrderTime()
                ))
                ->getResult();
        } else {
            $groupedOrders1 = $this->getDoctrine()
                ->getManager()
                ->createQuery('SELECT COUNT(o.id) as itemcount, i.name as item FROM App\Entity\ItemShopOrder o JOIN App\Entity\ItemShop i WHERE o.slot = :slot AND (o.item) = i.id GROUP BY i.id')
                ->setParameters(array(
                    'slot' => $currslot
                ))
                ->getResult();
        }

        return $this->render('oeilglauque/orderList.html.twig', array(
            'edition' => $edition,
            'slots' => $slots,
            'orders' => $orders,
            'groupedOrders1' => $groupedOrders1,
            'groupedOrders2' => $groupedOrders2,
            'types' => $types,
            'items' => $items,
            'currentSlot' => $currslot
        ));
    }

    /**
     * @Route("/order/addOrder/{slot}", name="addOrder")
     */
    public function addOrder(Request $request, $slot)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order = new ItemShopOrder();

        $slotval = $this->getDoctrine()->getRepository(ItemShopSlot::class)->find($slot);
        if (!$slotval) {
            $this->addFlash('danger', "Le slot n'existe pas.");
            return $this->redirectToRoute('orderIndex');
        }
        $order->setSlot($slotval);

        $itemval = $this->getDoctrine()->getRepository(ItemShop::class)->find($request->query->get('item'));
        if (!$itemval) {
            $this->addFlash('danger', "Le slot n'existe pas.");
            return $this->redirectToRoute('orderList', ["slot" => $slot]);
        }
        $order->setItem($itemval);

        $order->setPseudo($request->query->get('pseudo'));
        $order->setTime(new DateTime("now", new DateTimeZone('Europe/Paris')));
        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "La commande de " . $order->getPseudo() . " a bien été ajouté. ");

        $orders = $this->getDoctrine()->getRepository(ItemShopOrder::class)->findBy(["slot" => $slotval]);
        if (count($orders) >= $slotval->getMaxOrder()) {
            $this->addFlash('danger', "Le nombre maximal de commande pour ce créneau a été atteint.");
        }

        return $this->redirectToRoute('orderList', ["slot" => $slot]);
    }

    /**
     * @Route("/order/collectOrder/{id}/{state}", name="collectOrder")
     */
    public function collectOrder(Request $request, $id, $state)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order = $this->getDoctrine()->getRepository(ItemShopOrder::class)->find($id);
        $order->setCollected(!$state);
        $this->getDoctrine()->getManager()->persist($order);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', "La commande de " . $order->getPseudo() . " a bien été livré. ");

        return $this->redirectToRoute('orderList', ["slot" => $order->getSlot()->getId()]);
    }

    /**
     * @Route("/order/deleteOrder/{id}", name="deleteOrder")
     */
    public function deleteOrder(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $order = $this->getDoctrine()->getRepository(ItemShopOrder::class)->find($id);
        if ($order) {
            $this->getDoctrine()->getManager()->remove($order);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "La commande de " . $order->getPseudo() . " a bien été supprimé.");
        }

        return $this->redirectToRoute('orderList', ["slot" => $order->getSlot()->getId()]);
    }
}
?>