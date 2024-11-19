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
        $partyName = $info->getRequest()->get('party');
        $employees = new Employee\Listing();

        $now = Carbon::now()->startOfDay();

        foreach ($employees as $employee) {
            $birthday = Carbon::parse($employee->getBirthday())->setYear(Carbon::now()->year)->startOfDay();

            if ($days >= (int) $now->diffInDays($birthday) && $now->dayOfYear() <= $birthday->dayOfYear()) {
                $nextBirthday = $employee;
                break;
            }
        }

        $info->setParam('nextBirthday', $nextBirthday);
        $info->setParam('party', $partyName);

        return null;
    }
}

