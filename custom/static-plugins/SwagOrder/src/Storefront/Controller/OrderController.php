<?php declare(strict_types=1);

namespace Swag\Order\Storefront\Controller;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class OrderController extends StorefrontController
{
    public function __construct(private readonly EntityRepository $orderRepository)
    {
    }

    #[Route(
        path: '/order-list',
        name: 'frontend.order.list',
        methods: ['GET']
    )]
    public function order(Request $request, SalesChannelContext $context): Response
    {
        $criteria = $this->createCriteria($request, $context);
        $orders = $this->orderRepository->search($criteria, $context->getContext())->getElements();

        return $this->json($orders);
    }

    private function createCriteria(Request $request, SalesChannelContext $context): Criteria
    {
        $criteria = new Criteria();

        if ($request->get('number-of-days')) {
            $dateTimeFormat = (new \DateTimeImmutable(sprintf('-%s days', $request->get('number-of-days'))))
                ->format(Defaults::STORAGE_DATE_TIME_FORMAT);

            $criteria->addFilter(new RangeFilter(
                'orderDateTime',
                [
                    RangeFilter::GTE => $dateTimeFormat,
                ]
            ));
        }

        if ($request->get('limit')) {
            $criteria->setLimit((int) $request->get('limit'));
        }

        $criteria->addFields(['orderNumber']);

        return $criteria;
    }
}
