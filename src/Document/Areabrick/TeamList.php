<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Employee;
use Pimcore\Model\DataObject\Party;
use Carbon\Carbon;

class TeamList extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Team List';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);


        $days = 366;
        $partyName = $info->getRequest()->get('party');
        $parties = new Party\Listing();
        $party = null;
        $employees = new Employee\Listing();

        if($partyName !== null && $partyName !== '') {
            foreach($parties as $oneParty) {
                if($oneParty->getParty() === $partyName) {
                    $party = $oneParty->getId();
                }
            }
            $employees->setCondition('party = ?', $party);
        }

        $sortedEmployees = $this->sortEmployeesByBirthday($employees);
        $nextBirthdays = $this->getNextBirthdays($sortedEmployees);

        $info->setParam('employees', $sortedEmployees);
        $info->setParam('nextBirthdays', $nextBirthdays);
        $info->setParam('party', $partyName);
        $info->setParam('days', $days);

        return null;
    }

    function getNextBirthdays(array $employees): array
    {
        $nextBirthdays = [];
        $now = Carbon::now()->startOfDay();
        $birthdayThisYear = false;
        $days = 366;

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear($now->year)->startOfDay();

            if ($days >= $now->diffInDays($birthday) && $now->dayOfYear < $birthday->dayOfYear) {
                $birthdayThisYear = true;
                $nextBirthdays[] = $employee;
                $days = $now->diffInDays($birthday);
            }
        }
        if($birthdayThisYear === false) {
            foreach($employees as $employee) {
                $birthday = Carbon::parse($employee->getBirthday())->setYear($now->year + 1)->startOfDay();

                if ($days >= $now->diffInDays($birthday)) {
                    $nextBirthdays[] = $employee;
                    $days = $now->diffInDays($birthday);
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

