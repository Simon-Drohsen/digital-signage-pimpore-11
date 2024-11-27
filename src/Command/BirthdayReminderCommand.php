<?php

declare(strict_types=1);

namespace App\Command;

use App\Controller\MailController;
use Pimcore\Mail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\isInstanceOf;

#[AsCommand(name: 'app:birthday:reminder', description: 'Send birthday reminder mails')]
class BirthdayReminderCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = Response::HTTP_OK;
        $mailController = new MailController();

        $mail = $mailController->birthdayReminderAction();

        $io = new SymfonyStyle($input, $output);

        if ($mail === null) {
            $io->success('No birthday reminder mails to send');
        } elseif ($mail->send()) {
            $io->success('Birthday reminder mails sent successfully');
        }
        return Command::SUCCESS;
    }
}
