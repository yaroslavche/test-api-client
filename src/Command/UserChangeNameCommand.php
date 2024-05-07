<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ServerClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'app:user:change-name',
    description: 'Change user name',
)]
class UserChangeNameCommand extends Command
{
    public function __construct(
        #[Required] public ServerClientInterface $serverClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'Identifier')
            ->addArgument('name', InputArgument::REQUIRED, 'Name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $identifier = intval($input->getArgument('identifier'));
        $name = $input->getArgument('name');

        try {
            $response = $this->serverClient->changeUserName($identifier, $name);
            if (202 !== $response->getStatusCode()) {
                $responseContent = json_decode($response->getContent(false), true);
                $io->error(
                    $responseContent['error'] ?? sprintf('Failed to change user ID %d name "%s"', $identifier, $name),
                );

                return Command::FAILURE;
            }
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf('User %d name changed to "%s"', $identifier, $name));

        return Command::SUCCESS;
    }
}
