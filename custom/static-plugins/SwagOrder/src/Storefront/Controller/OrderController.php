<?php declare(strict_types=1);

namespace Swag\Order\Storefront\Controller;

use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Swag\Order\Service\OrderServiceInterface;
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
    public function orderList(RequestDataBag $requestDataBag, SalesChannelContext $context): Response
    {
        $orders = $this->orderService->getOrders(
            $requestDataBag->all(),
            $context->getContext()
        );

        return $this->json(
            $this->orderService->formatOrdersAsArray($orders)
        );
    }
}
