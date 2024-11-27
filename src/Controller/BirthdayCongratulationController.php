<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Redirect;

class BirthdayCongratulationController extends FrontendController
{
    public function action(): Response
    {
        $redirects = new Redirect\Listing();
        $redirect = null;

        foreach ($redirects as $oneRedirect) {
            if (lcfirst($oneRedirect->getTitle()) === 'birthday-congratulation') {
                $redirect = $oneRedirect;
            }
        }

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

        return $this->render('default/birthday-congratulation.html.twig',
            [
                'nextBirthday' => $nextBirthday,
                'url' => $redirect->getTo()[0]['link']->getData(),
                'timeout' => $redirect->getTimeout(),
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
