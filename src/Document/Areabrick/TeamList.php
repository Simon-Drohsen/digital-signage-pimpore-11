<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Employee;
use Carbon\Carbon;

class TeamList extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Team List';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);

        $checkNextBirthdays = [];
        $nextBirthdays = [];
        $days = 366;
        $employees = new Employee\Listing();

        $checkNextBirthdays = $this->checkNextBirthday($employees, $checkNextBirthdays, $days);
        $nextBirthdays = $this->checkNextBirthday(array_reverse($checkNextBirthdays), $nextBirthdays, $days);

        $info->setParam('employees', $employees->getObjects());
        $info->setParam('nextBirthdays', $nextBirthdays);
        $info->setParam('days', $days);

        return null;
    }

    public function checkNextBirthday($employees, $nextBirthdays, $days)
    {
        $now = Carbon::now()->startOfDay();

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear(Carbon::now()->year)->startOfDay();

            if ($days >= $now->diffInDays($birthday) && $now->dayOfYear() < $birthday->dayOfYear()) {
                $nextBirthdays[] = $employee;
                $days = $now->diffInDays($birthday);
            }
        }

        return $nextBirthdays;
    }
}

