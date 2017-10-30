<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Note;
use SET\User;
use Tests\TestCase;

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
        $response = $this->get("/user/$userId/note/create");
        $response->assertStatus(200);
        $response->assertSee('Add a Note');
        $response->assertSee('Private');
        $response->assertSee('File has PII/Encrypt File');

        // Logged in as a regular user - Cannot access the note create page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/note/create");

        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the note create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/note/create");

        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_stores_the_note_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the note
        $userId = $this->user->id;
        $data = ['title'   => 'Test Note',
                 'comment' => 'Description For Note',
                 'private' => '1',
                 'alert'   => 0, ];

        $response = $this->post("/user/$userId/note/", $data);
        $response->assertRedirect('user/'.$userId);

        // Logged in as a regular user - Does not store the note
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->post("/user/$userId/note/", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Does not store the note
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->post("/user/$userId/note/", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_does_not_store_note_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $userId = $this->user->id;
        $data = [];

        $response = $this->post("/user/$userId/note/", $data);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['title']);
        $response->assertSessionHasErrors('title', 'The title field is required.');

        // Logged in as admin - Only publish_date is entered.
        $data = ['comment' => 'A Note Description'];
        $response = $this->post("/user/$userId/note/", $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors('title', 'The title field is required.');
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
        $response = $this->get("/user/$userId/note/$createdNoteId/edit");
        $response->assertStatus(200);
        $response->assertSee('Update a Note');
        $response->assertSee('Private');
        $response->assertSee('File has PII/Encrypt File');
        $response->assertViewHas('user');
        $response->assertViewHas('note');

        // Logged in as a regular user - Cannot edit the note
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/note/$createdNoteId/edit");
        $response->assertStatus(403);
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
        $data = ['title'   => 'Test Note',
                 'comment' => 'Description For Note',
                 'private' => '1', ]; //,
        //'alert'   => 0, ];

        $response = $this->patch("/user/$userId/note/$createdNoteId", $data);

        $response->assertRedirect('user/'.$userId);

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
        $response = $this->patch("/user/$userId/note/$createdNoteId", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot update the note
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->patch("/user/$userId/note/$createdNoteId", $data);
        $response->assertStatus(403);
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
        $response = $this->delete("/user/$userId/note/$createdNoteId");
        $deletedNote = Note::find($createdNoteId);
        $this->assertNull($deletedNote);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);

        // Cannot access the delete note page since the note with
        // the provided Id has already been deleted
        $response = $this->delete("/user/$userId/note/$createdNoteId");
        $response->assertStatus(404);

        // Create a new note and try to delete. Get forbidden status code
        $noteToCreate = factory(Note::class)->create();
        $createdNoteId = $noteToCreate->id;
        $response = $this->delete("/user/$userId/note/$createdNoteId");
        $response->assertStatus(403);
    }
}
