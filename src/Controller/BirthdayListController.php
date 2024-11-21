<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Party;

class BirthdayListController extends FrontendController
{
    public function action(Request $request): Response
    {
        $days = 366;
        $parties = new Party\Listing();
        $employees = new Employee\Listing();
        $partyId = null;

        if ($this->editmode) {
            return $this->render('default/birthday-list.html.twig',
                [
                    'employees' => $employees,
                    'nextBirthdays' => $this->getNextBirthdays($employees->getObjects()),
                    'days' => $days,
                ]
            );
        }

        $party = explode('/', $request->attributes->getString('_site_path'))[1];

        foreach($parties as $oneParty) {
            if($oneParty->getParty() === $party) {
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

        usort($employees, function ($a, $b) {
            $dayOfYearA = Carbon::parse($a->getBirthday())->dayOfYear;
            $dayOfYearB = Carbon::parse($b->getBirthday())->dayOfYear;

            return $dayOfYearA <=> $dayOfYearB;
        });

        return $employees;
    }
}
