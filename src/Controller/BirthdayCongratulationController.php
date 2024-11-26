<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Employee;

class BirthdayCongratulationController extends FrontendController
{
    public function action(Request $request): Response
    {
        $days = 366;
        $nextBirthday = null;
        $employees = $this->sortEmployeesByBirthday(new Employee\Listing());
        $now = Carbon::now()->startOfDay();

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear(Carbon::now()->year)->startOfDay();

            if ($days >= (int) $now->diffInDays($birthday) && $now->dayOfYear() <= $birthday->dayOfYear()) {
                $nextBirthday = $employee;
                break;
            }
        }

        if ($this->editmode) {
            return $this->render('default/birthday-congratulation.html.twig',
                [
                    'nextBirthday' => $nextBirthday,
                ]
            );
        }

        return $this->render('default/birthday-congratulation.html.twig',
            [
                'nextBirthday' => $nextBirthday,
            ]
        );
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
