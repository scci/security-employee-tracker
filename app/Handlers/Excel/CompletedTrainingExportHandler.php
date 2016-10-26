<?php
namespace SET\Handlers\Excel;

use \Maatwebsite\Excel\Files\ExportHandler;
use SET\User;
use SET\Training;

class CompletedTrainingExportHandler implements ExportHandler
{

    public function handle($export)
    {
        return $export->sheet('User-Training', function ($sheet) {

            $trainings = Training::all();
            $users = User::skipSystem()->with([
                'assignedTrainings' => function ($q) {
                    $q->whereNotNull('completed_date')->orderBy('completed_date', 'desc');
                }
            ])->active()->orderBy('last_name')->get();

            $sheet->loadView('report.completed_training', array('users' => $users, 'trainings' => $trainings));

        })->download('xlsx');
    }
}
