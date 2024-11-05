<?php

namespace App\Controller;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Mail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{
    const FIRST_REMINDER = 14;
    const SECOND_REMINDER = 7;

    public function getSelectedMail($mails, $party) {
        $selectedMail = new Mail();
        foreach($mails as $mail) {
            if($mail->getParty() === $party) {
                $selectedMail = $mail;
            }
        }
        return $selectedMail;
    }

    public function sendBirthdayReminder(TransportInterface $transport): Mail | null
    {
        $mails = new Mail\Listing;
        $tz = new CarbonTimeZone('Europe/Zurich');
        $employees = new Employee\Listing;
        foreach($employees as $employee) {
            $birthday = $employee->getBirthday();
            $today = new Carbon();
            $today->setTimezone($tz);
            $today = $today->dayOfYear() - 1;
            $birthday = $birthday->dayOfYear();
            if($today === $birthday - self::FIRST_REMINDER || $today === $birthday - self::SECOND_REMINDER) {
                $employeeParty = $employee->getParty();
                $mail = $this->getSelectedMail($mails->getObjects(), $employeeParty);
                $receiver = $mail->getReceiver();
                $receiver = explode('.', $receiver);
                $receiver = $receiver[0];
                $birthdayWithoutYear = $employee->getBirthday()->format('d.m');
                $email = (new TemplatedEmail())
                    ->from($mail->getSender())
                    ->to($mail->getReceiver())
                    ->subject($mail->getSubject())
                    ->htmlTemplate('mail/template.html.twig')
                    ->context([
                        'receiver' => ucfirst($receiver),
                        'days' => $birthday - $today,
                        'birthday' => $birthdayWithoutYear,
                        'employee' => $employee
                    ]);
                $transport->send($email);
                return $mail;
            }
        }
        return null;
    }
}
