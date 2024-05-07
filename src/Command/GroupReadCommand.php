<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ServerClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'app:group:read',
    description: 'Read group by identifier',
)]
class GroupReadCommand extends Command
{
    public function __construct(
        #[Required] public ServerClientInterface $serverClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'Group identifier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $identifier = intval($input->getArgument('identifier'));

        try {
            $response = $this->serverClient->readGroup($identifier);
            $responseContent = json_decode($response->getContent(false), true);
            if (200 !== $response->getStatusCode()) {
                $io->error($responseContent['error'] ?? sprintf('Failed to read group "%d"', $identifier));

                return Command::FAILURE;
            }
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Property', 'Value'])
            ->setRows([
                ['Identifier', $responseContent['id'] ?? ''],
                ['Name', $responseContent['name'] ?? ''],
            ]);
        $table->render();

        return Command::SUCCESS;
    }
}
