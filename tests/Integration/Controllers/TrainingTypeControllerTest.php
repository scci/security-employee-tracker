<?php

namespace Tests\Integration\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Training;
use SET\TrainingType;
use SET\User;
use Tests\TestCase;

class TrainingTypeControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->signIn();
        $this->withoutEvents();
    }

    /**
     * @test
     */
    public function it_displays_the_index_page()
    {
        $createdTrainingTypes = factory(TrainingType::class, 5)->create([]);

        // Logged in as admin - Can access the page
        $response = $this->get('trainingtype');

        $response->assertStatus(200);
        $response->assertViewHas('trainingtypes');

        // Logged in as a regular user - Cannot access the page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/trainingtype');

        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with role view - Can access the page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/trainingtype');

        $response->assertStatus(200); // Ok status code
        $response->assertViewHas('trainingtypes');

        // Verify page components (views\trainingtype\index.blade.php)
        $response->assertSee('<span class="card-title">Training Types</span>');
        $response->assertSee('<th>Name</th>'); // Table column
        $response->assertSee('<th>Status</th>'); // Table column
        $response->assertDontSee('<th>Modify</th>'); // Table column
        foreach ($createdTrainingTypes as $key => $createdTrainingType) {
            $response->assertSee($createdTrainingType->name); // Table items of types
        }
        $response->assertDontSee('class="material-icons">mode_edit</i>'); // Table item edit
        $response->assertDontSee('class="material-icons">delete</i>'); // Table item delete
        $response->assertDontSee('data-tooltip="Create Training Type">'); // NEW icon button

        // Logged in as a user with role edit - Can access the page
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->get('/trainingtype');

        $response->assertStatus(200); // Ok status code
        $response->assertViewHas('trainingtypes');

        // Verify page components (views\trainingtype\index.blade.php)
        $response->assertSee('data-tooltip="Create Training Type">'); // Title
        $response->assertSee('<th>Description</th'); // Table column
        $response->assertSee('<th>Modify</th>'); // Table column
        $response->assertSee('class="material-icons">mode_edit</i>'); // Table item edit
        $response->assertSee('class="material-icons">delete</i>'); // Table item delete
        $response->assertSee('data-tooltip="Create Training Type">'); // NEW icon button
    }

    /**
     * @test
     */
    public function it_displays_the_create_page()
    {
        // Logged in as admin - Can access the training create page
        $response = $this->get('trainingtype/create');

        $response->assertStatus(200);

        // Logged in as a regular user - Cannot access the page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get('/trainingtype/create');

        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with role view - Cannot access the page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get('/trainingtype/create');

        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with role edit - Can access the page
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->get('/trainingtype/create');

        $response->assertStatus(200); // OK status code

        // Verify page components (views\trainingtype\create.blade.php, views\trainingtype\_trainingtype_form.blade.php)
        $response->assertSee('Create Training Type'); // Title
        $response->assertSee('Name:'); // Field
        $response->assertSee('Status:'); // Field
        $response->assertSee('Description:'); // Field
        $response->assertSee('type="submit" value="Create"'); // Button
        $response->assertSee('<strong>Create/Update button</strong>'); // Help Text (views\trainingtype\_form.blade.php)
    }

    /**
     * @test
     */
    public function it_stores_the_trainingtype_by_testing_each_user_role()
    {
        // Logged in as admin - Can store the training
        $data = ['name'        => 'Sample Training Type',
                 'status'      => 1,
                 'sidebar'     => 0,
                 'description' => null, ];
        $response = $this->post('trainingtype', $data);

        $response->assertStatus(302); // Redirection status code
        $response->assertRedirect('trainingtype'); // Only check that you're redirecting to a specific URI
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.

        // Logged in as a regular user - Does not store the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->post('trainingtype', $data);

        $response->assertStatus(403); // Forbidden status code
        //$response->assertSeeText('AccessDeniedHttpException');
        $this->assertFalse($response->isRedirection()); // Redirected

        // Logged in as a user with role view - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->post('trainingtype', $data);

        $response->assertStatus(403); // Forbidden status code
        $response->assertSeeText('AccessDeniedHttpException');
        $this->assertFalse($response->isRedirection()); // Redirected

        // Logged in as a user with edit view - Does store the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->post('trainingtype', $data);

        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $response->assertStatus(302); // Redirection status code
        $response->assertRedirect('trainingtype'); // Only check that you're redirecting to a specific URI
    }

    /**
     * @test
     */
    public function it_does_not_store_trainingtype_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = []; // Required data not provided
        $response = $this->post('trainingtype', $data);

        // Handle error redirection
        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $response->assertStatus(302); // Redirection status code

        // Test the StoreTrainingTypeRequest handling
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors('name', 'The name field is required.');
        $response->assertSessionHasErrors(['status']);
        $response->assertSessionHasErrors('status', 'The name field is required.');

        // Test when only name value is entered.
        $data = ['name' => 'Sample Name'];
        $response = $this->post('trainingtype', $data);
        $response->assertStatus(302); // Redirection status code

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['status']);
        $response->assertSessionHasErrors('status', 'The status field is required.');

        // Test when name & status value is entered. (all required data is entered)
        $data = ['name' => 'Sample Name', 'status' => 1];
        $response = $this->post('trainingtype', $data);
        $response->assertStatus(302); // Redirection status code
        $response->assertRedirect('trainingtype'); // Only check that you're redirecting to a specific URI
    }

    /**
     * @test
     */
    public function it_shows_the_trainingtype()
    {
        // Create a trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create([]);
        $createdTrainingTypeId = $createdTrainingType->id;

        // Create trainings
        $createdTrainings = factory(Training::class, 6)->create([]);
        foreach ($createdTrainings as $createdTraining) {
            // Associating trainingtype to a Training
            $createdTraining->trainingType()->associate($createdTrainingType);
            $createdTraining->save();
        }
        $this->assertEquals($createdTrainingType->trainings()->count(), $createdTrainings->count());

        // Logged in as admin - Can see the trainingtype details
        // Logged in as a regular user - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->get("trainingtype/$createdTrainingTypeId");

        $response->assertStatus(200);
        $response->assertSee('/trainingtype/'.$createdTrainingTypeId);
        $response->assertViewHas('trainingtype');
        $response->assertViewHas('trainings');

        // Verify page components (views\trainingtype\show.blade.php)
        $response->assertSee($createdTrainingType->name); // Card Title
        $response->assertSee('<i class="material-icons">mode_edit</i>'); // Edit icon
        $response->assertSee('Status'); // Field
        $response->assertSee('Description:'); // Field
        $response->assertSee('<th>Associated Trainings</th>'); // Table column
        foreach ($createdTrainings as $key => $createdTraining) {
            $response->assertSee($createdTraining->name); // Table items of types
        }

        // Logged in as a view user - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get("trainingtype/$createdTrainingTypeId");

        $response->assertStatus(200); // OK status code
        $response->assertSee('/trainingtype/'.$createdTrainingTypeId);

        // Verify page components (views\trainingtype\show.blade.php)
        $response->assertDontSee('<i class="material-icons">mode_edit</i>'); // Edit icon

        // Logged in as a view user - Does not store the training
        $newuser = factory(User::class)->create(['role' => '']);
        $this->actingAs($newuser);
        $response = $this->get("trainingtype/$createdTrainingTypeId");
        $response->assertStatus(403); // Forbidden status code
        $response->assertSee('Whoops, looks like something went wrong.');
        $this->assertFalse($response->isRedirection()); // Redirected
    }

    /**
     * @test
     */
    public function can_edit_trainingtype()
    {
        // Create a trainingtype object
        $createdTrainingType = factory(TrainingType::class)->create();
        $createdTrainingTypeId = $createdTrainingType->id;

        // Logged in as admin - Can edit the trainingtype details
        $response = $this->get("trainingtype/$createdTrainingTypeId/edit");

        $response->assertStatus(200); // OK request response
        $response->assertSee('Edit Training Type');
        $response->assertViewHas('trainingtype');

        // Logged in as a regular user - Cannot edit the trainingtype details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->get("trainingtype/$createdTrainingTypeId/edit");

        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with role view - Cannot edit the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->get("trainingtype/$createdTrainingTypeId/edit");

        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with edit view - Cannot edit the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->get("trainingtype/$createdTrainingTypeId/edit");

        $response->assertStatus(200); // OK status code
        $response->assertSee('Edit Training Type');
        $response->assertViewHas('trainingtype');

        // Verify page components (views\trainingtype\edit.blade.php, views\trainingtype\_trainingtype_form.blade.php)
        $response->assertSee('Edit Training Type'); // card-title
        $response->assertSee('Name:'); // Field
        $response->assertSee('Status:'); // Field
        $response->assertSee('Description:'); // Field
        $response->assertSee('type="submit" value="Update"'); // Button
        $response->assertSee('Create/Update button'); // Help Text (views\trainingtype\_form.blade.php)
    }

    /**
     * @test
     */
    public function it_updates_the_trainingtype()
    {
        // Create a trainingtype object
        $trainingTypeToCreate = factory(TrainingType::class)->create();
        $createdTrainingTypeId = $trainingTypeToCreate->id;

        // Logged in as admin - Can update the training
        $data = ['name'        => 'Sample Training Type',
                 'status'      => 1,
                 'sidebar'     => 0,
                 'description' => 'Sample Trainging Type Descripiton', ];

        $response = $this->patch("trainingtype/$createdTrainingTypeId", $data);
        $response->assertRedirect("/trainingtype/$createdTrainingTypeId");

        $createdTrainingType = TrainingType::find($trainingTypeToCreate->id);
        $this->assertNotEquals($createdTrainingType->name, $trainingTypeToCreate->name);
        $this->assertEquals($createdTrainingType->name, $data['name']);
        $this->assertNotEquals($createdTrainingType->description, $trainingTypeToCreate->description);
        $this->assertEquals($createdTrainingType->description, $data['description']);
        $this->assertEquals($createdTrainingType->status, $data['status']);
        $this->assertEquals($createdTrainingType->sidebar, $data['sidebar']);
    }

    /**
     * @test
     */
    public function it_updates_the_trainingtype_if_edit_role()
    {
        // Create a trainingtype object
        $trainingTypeToCreate = factory(TrainingType::class)->create();
        $createdTrainingTypeId = $trainingTypeToCreate->id;

        // Logged in as admin - Can update the training
        $data = ['name'        => 'Sample Training Type',
                 'status'      => 1,
                 'sidebar'     => 0,
                 'description' => 'Sample Trainging Type Descripiton', ];
        // Logged in as a regular user - Cannot update the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->patch("trainingtype/$createdTrainingTypeId", $data);
        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with role view - Cannot update the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->patch("trainingtype/$createdTrainingTypeId", $data);
        $response->assertStatus(403); // Forbidden status code

        // Logged in as a user with edit view - Can update the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->patch("trainingtype/$createdTrainingTypeId", $data);

        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $response->assertStatus(302); // Redirection status code
        $response->assertRedirect('trainingtype/'.$createdTrainingTypeId); // Only check that you're redirecting to a specific URI
    }

    /**
     * @test
     */
    public function it_deletes_the_trainingtype()
    {
        // Create a trainingtype object
        $trainingTypeToCreate = factory(TrainingType::class)->create();
        $createdTrainingTypeId = $trainingTypeToCreate->id;

        // Ensure the created trainingtype is in the database
        $createdTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNotNull($createdTrainingType);
        $this->assertEquals($createdTrainingType->id, $createdTrainingTypeId);

        // Delete the created trainingtype as admin
        $response = $this->delete("trainingtype/$createdTrainingTypeId");

        $this->assertEquals($response->content(), '');
        $response->assertStatus(200);  // OK status code
        // Assert that a null object is returned.
        $deletedTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNull($deletedTrainingType);

        // Delete again the created trainingtype.
        $response = $this->delete("trainingtype/$createdTrainingTypeId");
        $response->assertStatus(404);  // Not Found status code
    }

    /**
     * @test
     */
    public function it_deletes_the_associated_training_trainingtypes()
    {
        // Create a trainingtype object
        $trainingTypeCreated = factory(TrainingType::class)->create();
        $createdTrainingTypeId = $trainingTypeCreated->id;

        // Create a training object
        $trainingCreated = factory(Training::class)->create();
        $createdTrainingId = $trainingCreated->id;

        // Associating trainingtype to a Training
        $trainingCreated->trainingType()->associate($trainingTypeCreated);
        $trainingCreated->save();

        // Ensure trainingtype is associated with training
        $this->assertEquals(TrainingType::first()->id, Training::first()->training_type_id);
        $this->assertEquals(Training::has('trainingType')->count(), 1);
        $this->assertEquals(TrainingType::first()->id, Training::has('trainingType')->first()->id);
        $this->assertEquals(TrainingType::first()->id, Training::first()->trainingtype->id);
        $this->assertEquals(TrainingType::first()->name, Training::first()->trainingtype->name);

        // Ensure training is associated with trainingtype
        $this->assertEquals(TrainingType::has('trainings')->count(), 1);
        $this->assertEquals(TrainingType::first()->trainings()->first()->id,
            Training::first()->id);
        $this->assertEquals(TrainingType::first()->trainings()->first()->id,
            Training::first()->id);
        $this->assertEquals(TrainingType::first()->trainings()->first()->training_type_id,
            TrainingType::first()->id);

        // Delete the created trainingtype as admin
        $response = $this->delete("trainingtype/$createdTrainingTypeId");

        $response->assertStatus(200);  // OK status code
        // Assert that a null object is returned.
        $deletedTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNull($deletedTrainingType);

        // Ensure trainingtype is NOT associated with training
        $this->assertNull(Training::first()->training_type_id);
        $this->assertEquals(Training::has('trainingType')->count(), 0);
        $this->assertNull(Training::first()->trainingtype);
    }

    /**
     * @test
     */
    public function it_deletes_the_trainingtype_if_edit_role()
    {
        // Create a new trainingtype object
        $trainingTypeToCreate = factory(TrainingType::class)->create();
        $createdTrainingTypeId = $trainingTypeToCreate->id;
        // Ensure the created trainingtype is in the database
        $createdTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNotNull($createdTrainingType);
        $this->assertEquals($createdTrainingType->id, $createdTrainingTypeId);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        // Cannot access the delete trainingtype as regular user
        $response = $this->delete("trainingtype/$createdTrainingTypeId");
        $response->assertStatus(403); // Forbidden status code

        // Logged in as a view user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        // Cannot access the delete trainingtype as view user
        $response = $this->delete("trainingtype/$createdTrainingTypeId");
        $response->assertStatus(403); // Forbidden status code

        // Logged in as an edit user
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        // Delete the created trainingtype. Assert that a null object is returned.
        $response = $this->delete("trainingtype/$createdTrainingTypeId");

        $response->assertStatus(200); // OK status code
        $deletedTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNull($deletedTrainingType);
    }
}
