<?php declare(strict_types=1);

namespace Swag\Order\Storefront\Controller;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Swag\Order\Service\OrderServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class OrderController extends StorefrontController
{
    public function __construct(private readonly OrderServiceInterface $orderService)
    {
    }

    #[Route(
        path: '/order-list',
        name: 'frontend.order.list',
        methods: ['GET']
    )]
    public function order(Request $request, SalesChannelContext $context): Response
    {
        return $this->json(
            $this->orderService->getOrders(
                $request->getPayload(),
                $context->getContext()
            )->getElements()
        );
    }
}
