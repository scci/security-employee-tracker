<?php

namespace SET\Handlers\Excel;

//use Maatwebsite\Excel\Files\ExportHandler;
//use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use SET\Training;
use SET\User;

class CompletedTrainingExportHandler implements FromView, WithEvents, ShouldAutoSize, WithTitle
{
    public function view(): View
    {
        $trainings = Training::all();
        $users = User::skipSystem()->with([
            'assignedTrainings' => function ($q) {
                $q->whereNotNull('completed_date')->orderBy('completed_date', 'desc');
            },
        ])->active()->orderBy('last_name')->get();

        return view('report.completed_training', ['users' => $users, 'trainings' => $trainings]);
    }

    public function title(): string
    {
        return 'User-Training';
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:ZZ1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
