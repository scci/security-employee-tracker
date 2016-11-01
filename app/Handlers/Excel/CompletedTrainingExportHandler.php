<?php

namespace SET\Handlers\Excel;

use Maatwebsite\Excel\Files\ExportHandler;
use SET\Training;
use SET\User;

class CompletedTrainingExportHandler implements ExportHandler
{
    public function handle($export)
    {
        return $export->sheet('User-Training', function ($sheet) {
            $trainings = Training::all();
            $users = User::skipSystem()->with([
                'assignedTrainings' => function ($q) {
                    $q->whereNotNull('completed_date')->orderBy('completed_date', 'desc');
                },
            ])->active()->orderBy('last_name')->get();

            $sheet->loadView('report.completed_training', ['users' => $users, 'trainings' => $trainings]);
        })->download('xlsx');
    }
}
