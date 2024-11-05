<?php

declare(strict_types=1);

namespace App\Command;

use App\Controller\MailController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Transport\TransportInterface;


#[AsCommand(name: 'app:birthday:reminder', description: 'Send birthday reminder mails')]
class BirthdayReminderCommand extends Command
{
    public function __construct(private readonly TransportInterface $transport)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $mailController = new MailController();

        $mail = $mailController->sendBirthdayReminder($this->transport);

        if (!$mail) {
            $io->success('No birthday reminder mails to send');
            return Command::SUCCESS;
        }

        $io->success(sprintf('sending birthday reminder mails to %s', $mail->getReceiver()));

        return Command::SUCCESS;
    }
}
