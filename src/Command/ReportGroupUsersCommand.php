<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ServerClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AsCommand(
    name: 'app:report:group-users',
    description: 'Get report with the list of users of the groups',
)]
class ReportGroupUsersCommand extends Command
{
    public function __construct(
        #[Required] public ServerClientInterface $serverClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('groupId', InputArgument::OPTIONAL, 'Group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $groupId = $input->getArgument('groupId');
        if (null !== $groupId) {
            $groupId = intval($groupId);
        }

        try {
            $reportResponse = $this->serverClient->reportGroupUsers($groupId);
            if (404 === $reportResponse->getStatusCode()) {
                $io->error(sprintf('Group %d is not found', $groupId));

                return Command::FAILURE;
            }
            $report = json_decode($reportResponse->getContent(), true);
        } catch (
            TransportExceptionInterface|
            ClientExceptionInterface|
            RedirectionExceptionInterface|
            ServerExceptionInterface $exception
        ) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $table = new Table($output);
        $table
            ->setHeaders(['User identifier', 'User name', 'User email'])
            ->setRows($this->prepareTableRows($report, $groupId));
        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @param array<array-key, mixed> $report
     *
     * @return array<array-key, mixed>
     */
    private function prepareTableRows(array $report, ?int $reportGroupId): array
    {
        $allGroups = [];
        $usersByGroup = [];
        foreach ($report as $user) {
            foreach ($user['userGroups'] as $group) {
                $groupName = $group['name'];
                $groupId = (string) $group['id'];
                if (null !== $reportGroupId && $reportGroupId !== (int) $groupId) {
                    continue;
                }
                if (!isset($allGroups[$groupName])) {
                    $allGroups[$groupName] = $groupId;
                }
                $usersByGroup[$groupId][] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                ];
            }
        }
        $tableRows = [];
        foreach ($allGroups as $groupName => $groupId) {
            $groupUsers = [];
            foreach ($usersByGroup as $usersGroupId => $users) {
                if ((int) $usersGroupId !== (int) $groupId) {
                    continue;
                }
                foreach ($users as $user) {
                    $groupUsers[] = [$user['id'], $user['name'], $user['email']];
                }
            }
            $tableRows[] = [
                new TableCell(
                    sprintf('%s (ID: %d)', $groupName, $groupId),
                    [
                        'colspan' => 3,
                        'style' => new TableCellStyle([
                            'align' => 'center',
                            'cellFormat' => '<info>%s</info>',
                        ]),
                    ],
                ),
            ];
            $tableRows = array_merge($tableRows, [new TableSeparator()], $groupUsers, [new TableSeparator()]);
        }
        array_pop($tableRows); // remove last separator

        return $tableRows;
    }
}
