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

        $nextBirthdays = [];
        $days = 366;
        $employees = new Employee\Listing();
        $round = false;

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear(Carbon::now()->year)->startOfDay();
            $now = Carbon::now()->startOfDay();

            if ($days >= $now->diffInDays($birthday) && $now->diffInDays($birthday) >= 0) {
                $nextBirthdays[] = $employee;
                $days = $now->diffInDays($birthday);
            }
        }

        $info->setParam('employees', $employees->getObjects());
        $info->setParam('nextBirthdays', $nextBirthdays);
        $info->setParam('days', $days);

        return null;
    }
}

