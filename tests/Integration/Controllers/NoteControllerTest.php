<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Note;
use SET\User;

class NoteControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->signIn();
    }

    /**
     * @test
     */
    public function it_shows_the_create_page()
    {
        // Logged in as admin - Can access the note create page
        $userId = $this->user->id;
        $this->call('GET', "/user/$userId/note/create");

        $this->seePageIs("/user/$userId/note/create");

        // Logged in as a regular user - Cannot access the note create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('GET', "/user/$userId/note/create");

        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot access the note create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('GET', "/user/$userId/note/create");

        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_stores_the_note_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the note
        $userId = $this->user->id;
        $data = ['title'    => 'Test Note',
                 'comment'  => 'Description For Note',
                 'private'  => '1',
                 'alert'    => 0, ];

        $this->call('POST', "/user/$userId/note/", $data);
        $this->assertRedirectedToRoute('user.show', $userId);

        // Logged in as a regular user - Does not store the note
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('POST', "/user/$userId/note/", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Does not store the note
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('POST', "/user/$userId/note/", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_note_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $userId = $this->user->id;
        $data = [];

        $this->call('POST', "/user/$userId/note/", $data);

        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['title']);
        $this->assertSessionHasErrors('title', 'The title field is required.');

        // Logged in as admin - Only publish_date is entered.
        $data = ['comment' => 'A Note Description'];
        $this->call('POST', "/user/$userId/note/", $data);
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors('title', 'The title field is required.');
    }

    /**
     * @test
     */
    public function can_edit_the_note()
    {
        // Create a note object
        $userId = $this->user->id;
        $noteToCreate = factory(Note::class)->create();
        $createdNoteId = $noteToCreate->id;

        // Logged in as admin - Can edit the note
        $this->call('GET', "/user/$userId/note/$createdNoteId/edit");
        $this->seePageIs("/user/$userId/note/$createdNoteId/edit");
        $this->assertViewHas('user');
        $this->assertViewHas('note');

        // Logged in as a regular user - Cannot edit the note
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('GET', "/user/$userId/note/$createdNoteId/edit");
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_updates_the_notes()
    {
        // Create a note object
        $userId = $this->user->id;
        $noteToCreate = factory(Note::class)->create();
        $createdNoteId = $noteToCreate->id;

        // Logged in as admin - Can update the note
        $data = ['title'    => 'Test Note',
                 'comment'  => 'Description For Note',
                 'private'  => '1', ]; //,
                 //'alert'   => 0, ];

        $this->call('PATCH', "/user/$userId/note/$createdNoteId", $data);

        $this->assertRedirectedToRoute('user.show', $userId);

        $createdNote = Note::find($createdNoteId);
        $this->assertNotEquals($createdNote->title, $noteToCreate->title);
        $this->assertEquals($createdNote->title, $data['title']);
        $this->assertEquals($createdNote->comment, $data['comment']);
        $this->assertEquals($createdNote->private, $data['private']);
        $this->assertEquals($createdNote->alert, $noteToCreate->alert);

        // Logged in as a regular user - Cannot update the note
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('PATCH', "/user/$userId/note/$createdNoteId", $data);
        $this->seeStatusCode(403);

        // Logged in as a user with role view - Cannot update the note
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $this->call('PATCH', "/user/$userId/note/$createdNoteId", $data);
        $this->seeStatusCode(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_note()
    {
        // Create a note object
        $userId = $this->user->id;
        $noteToCreate = factory(Note::class)->create();
        $createdNoteId = $noteToCreate->id;

        // Ensure the created note is in the database
        $createdNote = Note::find($createdNoteId);
        $this->assertNotNull($createdNote);
        $this->assertEquals($createdNote->id, $createdNoteId);

        // Delete the created note. Assert that a null object is returned.
        $this->call('DELETE', "/user/$userId/note/$createdNoteId");
        $deletedNote = Note::find($createdNoteId);
        $this->assertNull($deletedNote);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete note page since the note with
        // the provided Id has already been deleted
        $this->call('DELETE', "/user/$userId/note/$createdNoteId");
        $this->seeStatusCode(404);

        // Create a new note and try to delete. Get forbidden status code
        $noteToCreate = factory(Note::class)->create();
        $createdNoteId = $noteToCreate->id;
        $this->call('DELETE', "/user/$userId/note/$createdNoteId");
        $this->seeStatusCode(403);
    }
}
