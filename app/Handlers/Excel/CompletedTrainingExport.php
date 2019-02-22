<?php

namespace SET\Handlers\Excel;

use Maatwebsite\Excel\Excel;

class CompletedTrainingExport extends Excel
{
    public function getFilename()
    {
        return 'Completed Training';
    }
}
