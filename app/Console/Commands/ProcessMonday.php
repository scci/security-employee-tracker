<?php

namespace SET\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use SET\Duty;
use SET\Handlers\Duty\DutyList;
use SET\Mail\EmailAdminSummary;
use SET\Setting;

class ProcessMonday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:monday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all commands for monday & send single email to Reporter';

    protected $classesToProcess = [
        'notes'     => SendReminders::class,
        'visits'    => ExpiringVisits::class,
        'records'   => RenewTraining::class,
        'destroyed' => DeleteUsers::class,
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mailArray = [];

        foreach ($this->classesToProcess as $key => $class) {
            $mailArray[$key] = (new $class())->handle()->getList();
        }

        $mailArray['dutyLists'] = $this->getDutyList();

        //Send FSO a summary email with all the lists we retrieved.
        $this->sendReporterEmail($mailArray);
    }

    private function sendReporterEmail($array)
    {
        $reportAddress = Setting::where('name', 'report_address')->first();

        Mail::to($reportAddress->secondary)->send(new EmailAdminSummary($array));
    }

    /**
     * @return mixed
     */
    private function getDutyList()
    {
        $duties = Duty::all();

        return $duties->map(function ($item) {
            $userDateArray = (new DutyList($item))->emailOutput();
            $userDateArray->put('duty', $item);

            return $userDateArray;
        });
    }
}
