<?php

namespace SET\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use SET\Duty;
use SET\Handlers\Duty\DutyList;
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

    /**
     * Variables we get from other commands so we can have a unified email to the "reporter"
     */
    protected $trainingUsers;
    protected $visits;
    protected $records;
    protected $dutyLists;
    protected $monday;
    protected $destroyed;


    /**
     * ProcessMonday constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->trainingUsers = $this->visits = $this->records = $this->dutyLists = $this->destroyed = new Collection();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //process training and visit reminders, then get those lists
        $reminder = new SendReminders();
        $reminder->handle();
        $this->trainingUsers = $reminder->gettrainingUsers();
        $this->visits = $reminder->getVisits();

        //auto renew training and get that list
        $records = new RenewTraining();
        $records->handle();
        $this->records = $records->getTrainingAdminRecord();

        //update end of day list for both building and lab. Retrieve list
        $duties = Duty::all();
        $this->dutyLists = $duties->map(function($item) {
            $userDateArray = (new DutyList($item))->emailOutput();
            $userDateArray->put('duty', $item);
            return $userDateArray;
        });

        //process old deadman users.
        $deadman = new DeleteSeparatedAndDestroyedUsers();
        $deadman->handle();
        $this->destroyed = $deadman->getDestroyed();

        //Send FSO a summary email with all the lists we retrieved.
        $this->sendReporterEmail();
    }

    private function sendReporterEmail()
    {

        $reportAddress = Setting::where('name', 'report_address')->first();

        Mail::send('emails.admin_reminder', [
            'notes'     => $this->trainingUsers,
            'visits'    => $this->visits,
            'records'   => $this->records,
            'monday'    => Carbon::now()->startOfWeek(),
            'dutyLists' => $this->dutyLists,
            'destroyed' => $this->destroyed,
        ], function($m) use ($reportAddress) {
            $m->to($reportAddress->secondary, $reportAddress->primary)
                ->subject('Weekly Report');
        });
    }
}
