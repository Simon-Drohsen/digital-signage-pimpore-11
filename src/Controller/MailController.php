<?php

namespace App\Controller;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Exception;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\BirthdayReminder;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Controller\FrontendController;
use Pimcore\Mail as PimcoreMail;

class MailController extends FrontendController
{
    const int FIRST_REMINDER = 14;
    const int SECOND_REMINDER = 7;

    /**
     * @throws Exception
     */
    public function birthdayReminderAction(): PimcoreMail|Response|null
    {
        $today = $this->getToday();
        $employees = new Employee\Listing();
        $mails = new BirthdayReminder\Listing();

        $mail = $this->findBirthdayReminderMail($employees, $mails, $today);

        if (!$mail) {
            return null;
        }

        if ($this->container !== null) {
            return $this->render('mails/template.html.twig', [
                'mail' => $mail['mail'],
                'employee' => $mail['employee'],
                'days' => $mail['days'],
            ]);
        }

        return $mail['mail'];
    }

    private function getToday(): int
    {
        $tz = new CarbonTimeZone('Europe/Zurich');
        $today = Carbon::now($tz);
        return $today->dayOfYear() - 1;
    }

    private function isReminderDue(int $today, int $birthday): bool
    {
        return $today === $birthday - self::FIRST_REMINDER || $today === $birthday - self::SECOND_REMINDER;
    }

    private function findMailInfo(BirthdayReminder\Listing $mails, Employee $employee): ?BirthdayReminder
    {
        foreach ($mails as $oneMail) {
            if ($oneMail->getParty() === $employee->getParty()) {
                return $oneMail;
            }
        }
        return null;
    }

    /**
     * @throws Exception
     */
    private function createReminderMail(BirthdayReminder $mailInfos, Employee $employee, int $days): PimcoreMail
    {
        $mail = new PimcoreMail();
        $mail->setDocument('/mails/birthday-reminder');
        $mail->to($mailInfos->getReceiver());
        $mail->subject('Birthday Reminder');
        $mail->setIgnoreDebugMode(true);
        $mail->setParams([
            'mail' => $mailInfos,
            'employee' => $employee,
            'days' => $days,
        ]);

        return $mail;
    }

    /**
     * @throws Exception
     */
    private function findBirthdayReminderMail(Employee\Listing $employees, BirthdayReminder\Listing $mails, int $today): ?array
    {
        foreach ($employees as $oneEmployee) {
            $birthday = $oneEmployee->getBirthday()?->dayOfYear();
            if (!$birthday || !$this->isReminderDue($today, $birthday)) {
                continue;
            }

            $mailInfos = $this->findMailInfo($mails, $oneEmployee);
            if (!$mailInfos) {
                continue;
            }

            $days = $birthday - $today;
            $mail = $this->createReminderMail($mailInfos, $oneEmployee, $days);

            return [
                'mail' => $mail,
                'employee' => $oneEmployee,
                'days' => $days,
            ];
        }

        return null;
    }
}
