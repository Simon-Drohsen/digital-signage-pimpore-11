<?php

namespace App\Controller;

use Carbon\Carbon;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Party;
class BirthdayCongratulationController extends FrontendController
{
    public function action(Request $request): Response
    {
        $days = 366;
        $nextBirthday = null;
        $employees = new Employee\Listing();
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
}
