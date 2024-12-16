<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CurrencyRate;

#[AsCommand(
    name: 'app:update-rates',
    description: 'Update cryptocurrency exchange rates from Binance API',
)]
class UpdateRatesCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Pairs for updating
        $currencyPairs = [
            'BTC/USD' => 'BTCUSDT',
            'BTC/EUR' => 'BTCEUR',
            'BTC/GBP' => 'BTCGBP',
        ];

        foreach ($currencyPairs as $pair => $symbol) {
            try {
                //query to API Binance
                $response = $this->httpClient->request(
                    'GET',
                    "https://api.binance.com/api/v3/ticker/price?symbol=$symbol"
                );

                $data = $response->toArray();

                $rate = (float) $data['price'];
                $timestamp = new \DateTime();

                // create
                $currencyRate = new CurrencyRate();
                $currencyRate->setCurrencyPair($pair);
                $currencyRate->setRate($rate);
                $currencyRate->setTimestamp($timestamp);

                $this->entityManager->persist($currencyRate);
                $output->writeln("Updated $pair: $rate");
            } catch (\Exception $e) {
                $output->writeln("Error updating $pair: " . $e->getMessage());
            }
        }

        $this->entityManager->flush();
        $output->writeln('Currency rates updated successfully.');

        return Command::SUCCESS;
    }
}

