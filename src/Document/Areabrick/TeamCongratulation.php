<?php

namespace App\Document\Areabrick;

use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\DataObject\Employee;
use Carbon\Carbon;

class TeamCongratulation extends AbstractAreabrick {
    public function getName(): string
    {
        return 'Team Congratulation';
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        parent::action($info);
        $nextBirthday = null;
        $days = 366;
        $employees = new Employee\Listing();

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear(Carbon::now()->year)->startOfDay();
            $now = Carbon::now()->startOfDay();

            if ($days >= $now->diffInDays($birthday) && $now->diffInDays($birthday) >= 0) {
                $nextBirthday = $employee;
                $days = $now->diffInDays($birthday);
            }
        }

        $info->setParam('nextBirthday', $nextBirthday);

        return null;
    }
}

