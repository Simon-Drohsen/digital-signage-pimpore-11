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

    public function birthdayReminderAction($employee = false): PimcoreMail|Response|null
    {
        if (!$employee) {
            return $this->render('mails/template.html.twig');
        }

        if ((Carbon::now()->year - $employee->getBirthday()->year) % 10 !== 0 && $employee->getBirthday()->year + 18 !== Carbon::now()->year) {
            return null;
        }

        $today = $this->getToday();
        $mails = new BirthdayReminder\Listing();

        $mail = $this->findBirthdayReminderMail($employee, $mails, $today);

        if ($this->container === null && !$mail) {
            return null;
        } elseif (!$mail) {
            return $this->render('mails/template.html.twig');
        }

        if ($this->container) {
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
        $today = Carbon::now($tz)->startOfDay();

        return $today->dayOfYear();
    }

    private function isReminderDue(int $today, int $birthday): bool
    {
        return $today === $birthday - self::FIRST_REMINDER || $today === $birthday - self::SECOND_REMINDER;
    }

    private function findMailInfo(BirthdayReminder\Listing $mails, Employee $employee): ?BirthdayReminder
    {
        foreach ($mails as $oneMail) {
            $mailParty = $oneMail->getParty();
            $employeeParty = $employee->getParty();

            $mailParty = $this->sortArr($mailParty);
            $employeeParty = $this->sortArr($employeeParty);
            if ($mailParty === $employeeParty) {
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
    private function findBirthdayReminderMail(Employee $employee, BirthdayReminder\Listing $mails, int $today): ?array
    {
        $birthday = $employee->getBirthday();
        $birthday = $birthday->setYear(Carbon::now()->year)->dayOfYear;
        if (!$birthday || !$this->isReminderDue($today, $birthday)) {
            return null;
        }

        $mailInfos = $this->findMailInfo($mails, $employee);
        if (!$mailInfos) {
            return null;
        }

        $days = $birthday - $today;
        $mail = $this->createReminderMail($mailInfos, $employee, $days);

        return [
            'mail' => $mail,
            'employee' => $employee,
            'days' => $days,
        ];
    }

    public function sortArr(array $arr): array
    {
        $intArray = array_map('intval', $arr);
        sort($intArray, SORT_NUMERIC);
        return array_map(function($num) {
            return str_pad($num, 2, '0', STR_PAD_LEFT);
        }, $intArray);
    }
}
