<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Party;
use Pimcore\Model\DataObject\Redirect;

class BirthdayListController extends FrontendController
{
    public function action(Request $request): Response
    {
        $redirects = new Redirect\Listing();
        $redirect = null;

        if($request->attributes->get('contentDocument')->getKey()) {
            $partyName = $request->attributes->get('contentDocument')->getKey();
        } else {
            $partyName = '';
        }

        foreach ($redirects as $oneRedirect) {
            if (lcfirst($oneRedirect->getTitle()) === 'birthday-list') {
                $redirect = $oneRedirect;
            }
        }

        $days = 366;
        $parties = new Party\Listing();
        $employees = new Employee\Listing();
        $partyId = null;

        foreach($parties as $oneParty) {
            if($oneParty->getParty() === $partyName) {
                $partyId = $oneParty->getId();
            }
        }

        $employees->setCondition('party LIKE ?', ['%' . $partyId . '%']);
        $sortedEmployees = $this->sortEmployeesByBirthday($employees);
        $nextBirthdays = $this->getNextBirthdays($sortedEmployees);

        return $this->render('default/birthday-list.html.twig',
            [
                'employees' => $sortedEmployees,
                'nextBirthdays' => $nextBirthdays,
                'days' => $days,
                'url' => $redirect->getTo(),
            ]
        );
    }

    function getNextBirthdays(array $employees): array
    {
        $nextBirthdays = [];
        $now = Carbon::now()->startOfDay();
        $birthdayThisYear = false;
        $days = 366;

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear($now->year)->startOfDay();

            if ($days >= (int) $now->diffInDays($birthday) && $now->dayOfYear <= $birthday->dayOfYear) {
                $birthdayThisYear = true;
                $nextBirthdays[] = $employee;
                $days = (int) $now->diffInDays($birthday);
            }
        }
        if($birthdayThisYear === false) {
            foreach($employees as $employee) {
                $birthday = Carbon::parse($employee->getBirthday())->setYear($now->year + 1)->startOfDay();

                if ($days >= (int) $now->diffInDays($birthday)) {
                    $nextBirthdays[] = $employee;
                    $days = (int) $now->diffInDays($birthday);
                }
            }
        }
        return $nextBirthdays;
    }

    function sortEmployeesByBirthday(Employee\Listing $employees): array
    {
        $employees = $employees->getObjects();
        $now = Carbon::now();

        usort($employees, function ($a, $b) use ($now) {
            $birthdayA = Carbon::parse($a->getBirthday())->setYear($now->year);
            $birthdayB = Carbon::parse($b->getBirthday())->setYear($now->year);

            if ($birthdayA->lt($now)) {
                $birthdayA->addYear();
            }
            if ($birthdayB->lt($now)) {
                $birthdayB->addYear();
            }

            return $birthdayA->dayOfYear <=> $birthdayB->dayOfYear;
        });

        return $employees;
    }
}
