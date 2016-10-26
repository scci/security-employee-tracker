<?php
namespace SET\Handlers\Excel;

use \Maatwebsite\Excel\Files\NewExcelFile;

class CompletedTrainingExport extends NewExcelFile
{
    public function getFilename()
    {
        return 'Completed Training';
    }
}
