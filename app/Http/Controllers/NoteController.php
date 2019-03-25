<?php

namespace SET\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use SET\Attachment;
use SET\Http\Requests\StoreNoteRequest;
use SET\Http\Requests\UpdateNoteRequest;
use SET\Note;
use SET\User;

class NoteController extends Controller
{
    public function create(User $user)
    {
        $this->authorize('edit');

        return view('note.create', compact('user'));
    }

    public function store(StoreNoteRequest $request, User $user)
    {
        $this->authorize('edit');
        $data = $request->all();
        $data['author_id'] = Auth::user()->id;

        $note = $user->notes()->create($data);

        if (Request::hasFile('files')) {
            Attachment::upload($note, Request::file('files'), $data['encrypt']);
        }

        return redirect()->action('UserController@show', $user->id)->with('status', 'Note successfully created');
    }

    public function edit(User $user, $noteID)
    {
        $this->authorize('edit');

        $note = Note::findOrFail($noteID);

        return view('note.edit', compact('user', 'note'));
    }

    public function update(UpdateNoteRequest $request, $userID, $noteId)
    {
        $this->authorize('edit');
        $note = Note::findOrFail($noteId);
        $note['author_id'] = Auth::user()->id;
        $data = $request->all();
        $note->update($data);

        if (Request::hasFile('files')) {
            Attachment::upload($note, Request::file('files'), $data['encrypt']);
        }

        return redirect()->action('UserController@show', $userID)->with('status', 'Note successfully updated');
    }

    public function destroy($userID, Note $note)
    {
        $this->authorize('edit');

        $note->delete();
        Storage::deleteDirectory('note_'.$note->id);

        return back();
    }
}
