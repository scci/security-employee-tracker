<?php namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Krucas\Notification\Facades\Notification;
use SET\Attachment;
use SET\Http\Requests\UpdateNoteRequest;
use SET\Http\Requests\StoreNoteRequest;
use SET\Note;
use Illuminate\Support\Facades\Event;
use SET\Setting;
use SET\User;
use Illuminate\Support\Facades\Storage;
use SET\Events\TrainingAssigned;

class NoteController extends Controller
{

    public function create(User $user)
    {
        return view('note.create', compact('user'));
    }

    public function store(StoreNoteRequest $request, User $user)
    {
        $data = $request->all();
        $data['author_id'] = Auth::user()->id;

        $note = $user->notes()->create($data);

        if (Request::hasFile('files')) {
            Attachment::upload($note, Request::file('files'), $data['encrypt']);
        }

        Notification::container()->success('Note successfully created');

        return redirect()->action('UserController@show', $user->id);
    }

    public function edit(User $user, $noteID)
    {
        $note = Note::findOrFail($noteID);
        return view('note.edit', compact('user', 'note'));
    }


    public function update(UpdateNoteRequest $request, $userID, $noteId)
    {
        $note = Note::findOrFail($noteId);
        $note['author_id'] = Auth::user()->id;
        $data = $request->all();
        $note->update($data);

        if (Request::hasFile('files')) {
            Attachment::upload($note, Request::file('files'), $data['encrypt']);
        }

        Notification::container()->success('Note successfully updated');

        return redirect()->action('UserController@show', $userID);
    }


    public function destroy($userID, $noteId)
    {
        
        Note::find($noteId)->delete();
        Storage::deleteDirectory('note_'.$noteId);

        return back();
    }


    /**
     * Sent IT and email when we debrief a user.
     *
     * @param User $user
     * @param $data
     */
    private function debrief(User $user, $data)
    {
        $user->update(['status' => 'deadman']);
        $date = $data['due_date'];

        //get our debrief list (those who needs to be notified of a debrief) and convert to an array.
        $debriefList = Setting::where('name', 'debrief')->first()->primary;
        $debriefList = explode(',', $debriefList);

        foreach ($debriefList as $email) {
            Mail::send('emails.terminated_user', ['user' => $user, 'date' => $date], function ($m) use ($user, $email) {
                $m->to($email)->subject("Terminate " . $user->userFullName . "'s accounts");
            });
        }

        Notification::container()->info("IT has been notified to terminate this account on $date");
    }
}
