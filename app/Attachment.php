<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    protected $table = 'attachments';
    public $timestamps = true;
    protected $fillable = ['filename', 'mime', 'imageable_type', 'imageable_id', 'encrypted', 'admin_only'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public static function upload($model, $files, $encrypted = false, $admin_only = false)
    {
        $modelName = strtolower(class_basename($model)).'_';

        foreach ($files as $file) {
            $data = [];

            Storage::makeDirectory($modelName.$model->id);

            Storage::disk('local')
                ->put($modelName.$model->id.'/'.$file->getClientOriginalName(), self::encryptFileContents($file, $encrypted));

            $data['filename'] = $file->getClientOriginalName();
            $data['mime'] = $file->getClientMimeType();
            $data['encrypted'] = $encrypted;
            $data['admin_only'] = $admin_only;
            $model->attachments()->create($data);
        }
    }

    private static function encryptFileContents($file, $encrypt)
    {
        if ($encrypt) {
            return encrypt(File::get($file));
        }

        return File::get($file);
    }
}
