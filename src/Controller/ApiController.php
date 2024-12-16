<?php
namespace App\Controller;

use App\Repository\CurrencyRateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/rates', name: 'get_rates', methods: ['GET'])]
    public function getRates(Request $request, CurrencyRateRepository $repository): JsonResponse
    {
        // get parametrs 
        $currencyPair = $request->query->get('currencyPair'); // Наприклад, BTC/USD
        $start = $request->query->get('start'); // Наприклад, 2024-12-01
        $end = $request->query->get('end'); // Наприклад, 2024-12-10

        // query to db
        $query = $repository->createQueryBuilder('c');

        if ($currencyPair) {
            $query->andWhere('c.currencyPair = :currencyPair')
                  ->setParameter('currencyPair', $currencyPair);
        }

        if ($start) {
            $query->andWhere('c.timestamp >= :start')
                  ->setParameter('start', new \DateTime($start));
        }

        if ($end) {
            $query->andWhere('c.timestamp <= :end')
                  ->setParameter('end', new \DateTime($end));
        }

        $rates = $query->getQuery()->getResult();

        $data = array_map(function ($rate) {
            return [
                'id' => $rate->getId(),
                'currencyPair' => $rate->getCurrencyPair(),
                'rate' => $rate->getRate(),
                'timestamp' => $rate->getTimestamp()->format('Y-m-d H:i:s'),
            ];
        }, $rates);

        return $this->json($data);
    }
}
