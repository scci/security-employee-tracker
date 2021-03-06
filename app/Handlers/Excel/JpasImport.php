<?php

namespace SET\Handlers\Excel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Files\ExcelFile;

class JpasImport extends ExcelFile
{
    public function getFile()
    {
        //If we pass a filepath, use it and get out
        if (Input::has('uploadedFile')) {
            return Input::get('uploadedFile');
        }
        //Process the excel file provided.
        $file = Input::file('file');
        $fileName = $file->getClientOriginalName();
        Storage::disk('local')->put($fileName, File::get($file));

        $storagePath = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

        return $storagePath.$fileName;
    }

    public function getFilters()
    {
        return [
            'chunk',
        ];
    }
}
