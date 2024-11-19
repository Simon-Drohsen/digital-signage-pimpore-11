<?php

namespace App\Controller;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\BirthdayReminder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\EmailHelper;
use Pimcore\Controller\FrontendController;
use Pimcore\Mail as PimcoreMail;
use Pimcore\Tool;

class MailController extends FrontendController
{
    const FIRST_REMINDER = 14;
    const SECOND_REMINDER = 7;

    public function birthdayReminderAction(): PimcoreMail|Response
    {
        $employees = new Employee\Listing();
        $employee = null;
        $mails = new BirthdayReminder\Listing();
        $days = 0;
        $tz = new CarbonTimeZone('Europe/Zurich');
        $today = new Carbon();
        $today->setTimezone($tz);
        $today = $today->dayOfYear() - 1;
        foreach($employees as $oneEmployee) {
            $birthday = $oneEmployee->getBirthday();
            $birthday = $birthday->dayOfYear();
            if ($today === $birthday - self::FIRST_REMINDER || $today === $birthday - self::SECOND_REMINDER) {
                $employee = $oneEmployee;
                $mailInfos = null;
                foreach ($mails as $oneMail) {
                    if ($oneMail->getParty() === $oneEmployee->getParty()) {
                        $mailInfos = $oneMail;
                        break;
                    }
                }

                $days = $birthday - $today;

                $mail = new PimcoreMail();
                $mail->setDocument('/mails/birthday-reminder');
                $mail->to($mailInfos->getReceiver());
                $mail->subject('Birthday Reminder');
                $mail->setIgnoreDebugMode(true);
                $mail->setParams([
                    'mail' => $mailInfos,
                    'employee' => $employee,
                    'days' => $days
                ]);
                break;
            }
        }
        if (!$mail) {
            return new Response('No birthday reminder mails to send');
        }

        if ($this->container !== null) {
            return $this->render('mails/template.html.twig', [
                'mail' => $mail,
                'employee' => $employee,
                'days' => $days
            ]);
        }

        return $mail;
    }
}
