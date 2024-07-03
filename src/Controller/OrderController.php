<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Service\Cart;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use Symfony\Component\Mime\Email;
use App\Repository\CityRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailer){

    }

    #[Route('/order', name: 'app_order')]
    public function index(Request $request,
    SessionInterface $session,
    ProductRepository $productRepository,
    EntityManagerInterface $em,
    Cart $cart,
    CityRepository $cityRepository): Response
    {
        $data = $cart->getCart($session);


        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

        if($order->isPayOndelivery()){
            if(!empty($data['total'])){

            
                $order->setTotalPrice($data['total']);
                $order->setCreatedAt(new DateTimeImmutable());
                $em->persist($order);
                $em->flush();

                foreach($data['cart'] as $value){

                    $orderProduct = new OrderProducts();
                    $orderProduct->setOrder($order);
                    $orderProduct->setProduct($value['product']);
                    $orderProduct->setQte($value['quantity']);
                    $order->addOrderProduct($orderProduct);
                    $em->persist($orderProduct);

                    

                    $em->flush();
                }
            }
            

            
            $session->set('cart', []);

            

            $html = $this->render('mail/orderConfirm.html.twig', [
                'order' => $order
            ]);

            $email = (new Email())
            ->from('myShop@gmail.com')
            ->to($order->getEmail())
            ->subject('Confirmation de commande')
            ->html($html->getContent())
            ;

            $this->mailer->send($email);
            return $this->redirectToRoute('order_ok_message');
            
        }
            
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView() ,
            'total' => $data['total']
        ]);
    }

    #[Route('/editor/order', name: 'app_orders_show')]
    public function getAllOrder(OrderRepository $orderRepository, Request $request, PaginatorInterface $paginator):Response
    {
        $data = $orderRepository->findBy([], ['id' => 'DESC']);

        $order = $paginator->paginate(
            // la donnée a paginer
            $data,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('order/order.html.twig', [
            'orders' => $order
        ]);
    }


    #[Route('/editor/order/{id}/is-completed/update', name: 'app_orders_is_completed_update')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $em):Response {
        $order = $orderRepository->find($id);
        $order->setCompleted(true);
        $em->flush();
        $this->addFlash('success', 'modification effectuée');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/editor/order/{id}/remove', name: 'app_orders_remove')]
    public function removeOrder(Order $order, EntityManagerInterface $em ):Response{
        $em->remove($order);
        $em->flush();
        $this->addFlash('danger', 'Votre commande a été supprimée');
        return $this->redirectToRoute('app_orders_show');
    }


    #[Route('/order-ok-message', name: 'order_ok_message')]
    public function orderMessage (): Response {
        return $this->render('order/order_message.twig');
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city):Response {
        $cityShippingPrice = $city->getShippingCost();

        return new Response(json_encode(['status' => 200, "message" => 'on', 'content' => $cityShippingPrice]));
    } 

}
