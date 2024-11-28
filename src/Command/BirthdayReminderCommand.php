<?php

declare(strict_types=1);

namespace App\Command;

use App\Controller\MailController;
use Exception;
use Pimcore\Model\DataObject\Employee;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:birthday:reminder', description: 'Send birthday reminder mails')]
class BirthdayReminderCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mailController = new MailController();
        $employees = new Employee\Listing();

        foreach ($employees->getObjects() as $employee) {
            $mail = $mailController->birthdayReminderAction($employee);

            $io = new SymfonyStyle($input, $output);

            if ($mail === null) {
                $io->success('birthday not in 7 or 14 days');
            } elseif ($mail->send()) {
                $io->success('Birthday reminder mail sent successfully for ' . $employee->getFirstname());
            } else {
                $io->error('Failed to send birthday reminder mails');
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
