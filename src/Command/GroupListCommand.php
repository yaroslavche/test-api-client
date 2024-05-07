<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ServerClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'app:group:list',
    description: 'Groups list',
)]
class GroupListCommand extends Command
{
    public function __construct(
        #[Required] public ServerClientInterface $serverClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $response = $this->serverClient->listGroups();
            $responseContent = json_decode($response->getContent(false), true);
            if (200 !== $response->getStatusCode()) {
                $io->error($responseContent['error'] ?? 'Failed to list groups');

                return Command::FAILURE;
            }
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $table = new Table($output);
        $tableRows = [];
        foreach ($responseContent as $group) {
            $tableRows[] = [$group['id'], $group['name']];
        }
        $table
            ->setHeaders(['Group identifier', 'Group name'])
            ->setRows($tableRows);
        $table->render();

        return Command::SUCCESS;
    }
}
