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
    name: 'app:user:remove-from-group',
    description: 'Remove a user from a group',
)]
class UserRemoveFromGroupCommand extends Command
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
            ->addArgument('groupIdentifier', InputArgument::REQUIRED, 'Group identifier')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $identifier = intval($input->getArgument('identifier'));
        $groupIdentifier = intval($input->getArgument('groupIdentifier'));

        try {
            $response = $this->serverClient->userRemoveFromGroup($identifier, $groupIdentifier);
            if (202 !== $response->getStatusCode()) {
                $responseContent = json_decode($response->getContent(false), true);
                $io->error($responseContent['error'] ??
                    sprintf('Failed to remove user ID %d from group ID %d', $identifier, $groupIdentifier));

                return Command::FAILURE;
            }
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf('User ID %d removed from group ID %d', $identifier, $groupIdentifier));

        return Command::SUCCESS;
    }
}
