<?php namespace SET\Http\Controllers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Krucas\Notification\Facades\Notification;
use SET\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use SET\Training;
use SET\User;

class AttachmentController extends Controller
{

    /**
     * Currently only called from the Training page via the sidebar upload.
     * @param $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        $data = $request->all();

        $encrypt = false;

        if (array_key_exists('encrypt', $data)) {
            $encrypt = $data['encrypt'];
        }

        if ($data['type'] == 'training') {
            $model = Training::findOrFail($data['id']);
        } else if ($data['type'] == 'user') {
            $model = User::findOrFail($data['id']);
            $encrypt = true;
        } else {
            Notification::container()->success('Upload Failed');
            return back();
        }

        Attachment::upload($model, $request->file('files'), $encrypt);
        Notification::container()->success('Upload Complete');

        return back();

    }

    /**
     * Let the browser download the file.
     * @param $fileId
     * @return Response
     */
    public function show($fileId)
    {
        $entry = Attachment::findOrFail($fileId);
        $type = explode("\\", $entry->imageable_type);
        $modelName = strtolower($type[1]);

        $file = $modelName . "_" . $entry->imageable_id . '/' . $entry->filename;

        if ($entry->encrypted) {
            try {
                $fileContent = decrypt(Storage::get($file));
            } catch (DecryptException $e) {
                dd($e);
            }
        } else {
            $fileContent = Storage::get($file);
        }

        return response()->make($fileContent, 200, array(
            'Content-Type' => $entry->mime,
            'Content-Disposition' => 'attachment; filename="' . $entry->filename . '"'
        ));

    }

    /**
     * Remove the specified resource from storage.
     * @param  $fileId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($fileId)
    {
        $file = Attachment::findOrFail($fileId);
        $file->delete();

        $type = explode("\\", $file->imageable_type);
        $modelName = strtolower($type[1]);

        $fileLocation = "app/" . $modelName . "_" . $file->imageable_id . '/' . $file->filename;
        Storage::delete($fileLocation);

        return back();
    }

}
