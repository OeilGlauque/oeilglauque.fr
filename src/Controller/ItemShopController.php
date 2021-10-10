<?php

namespace App\Controller;

use DateTime;
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
            $this->addFlash('error', "Les horaires ne correspondent pas");
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
}
?>