<?php

namespace App\Command;

use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:CountryAndCity',
    description: 'Add a short description for your command',
)]
class WeatherCountryAndCityCommand{
    public function __construct(
        private readonly WeatherUtil $weatherUtil,
    ) {}
    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country Code')] string $country,
        #[Argument('City Name')] string $city,
    ): int {
        $forecast = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);
        if (empty($forecast)) {
            $io->warning(sprintf('No forecast found for %s (%s).', $city, $country));
            return Command::FAILURE;
        }
        $io->writeln(sprintf('Location: %s (%s)', $city, $country));
        $io->table(['Date', 'Temperature', 'Pressure', 'Wind', 'Humidity'], array_map(fn($m) => [
            $m->getDate()->format('Y-m-d'),
            $m->getTemperature(),
            $m->getPressure(),
            $m->getWind(),
            $m->getHumidity(),
        ], $forecast));

        return Command::SUCCESS;
    }
}
