<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use App\Repository\CarrierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class OrderController extends AbstractController
{
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(CarrierRepository $carrierRepository): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if (count($addresses) === 0) {
            return $this->redirectToRoute('app_account_address_form');
        }

        $carriers = $carrierRepository->findAll();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'carriers' => $carriers,
            'action' => $this->generateUrl('app_order_summary'),
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_summary', methods: ['POST'])]
    public function add(
        Request $request,
        Cart $cart,
        CarrierRepository $carrierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $addresses = $this->getUser()->getAddresses();
        $carriers = $carrierRepository->findAll();
        $products = $cart->getCart();

        if (empty($products)) {
            $this->addFlash('info', 'Votre panier est vide.');

            return $this->redirectToRoute('app_cart');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'carriers' => $carriers,
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->redirectToRoute('app_order');
        }

        $choices = $form->getData();
        $addressObj = $form->get('addresses')->getData();
        $carrier = $form->get('carriers')->getData();

        $address = $addressObj->getFirstname() . ' ' . $addressObj->getLastname() . '<br/>';
        $address .= $addressObj->getAddress() . '<br/>';
        $address .= $addressObj->getPostalCode() . ' ' . $addressObj->getCity() . '<br/>';
        $address .= $addressObj->getCountry() . '<br/>';
        $address .= $addressObj->getPhoneNumber();

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setCreatedAt(new \DateTime());
        $order->setState(1);
        $order->setCarrierName($carrier->getName());
        $order->setCarrierPrice($carrier->getPrice());
        $order->setDelivery($address);

        foreach ($products as $product) {
            $orderDetail = new OrderDetail();
            $orderDetail->setProductName($product['object']->getName());
            $orderDetail->setProductIllustration($product['object']->getIllustration());
            $orderDetail->setProductPrice($product['object']->getPrice());
            $orderDetail->setProductTva($product['object']->getTva());
            $orderDetail->setProductQuantity($product['qty']);
            $order->addOrderDetail($orderDetail);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->render('order/summary.html.twig', [
            'choices' => $choices,
            'cart' => $products,
            'order' => $order,
            'totalWt' => $cart->getTotalPrice(),
        ]);
    }
}

