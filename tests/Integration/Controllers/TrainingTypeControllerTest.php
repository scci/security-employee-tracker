<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\Http\Controllers\TrainingTypeController;
use SET\Training;
use SET\TrainingType;
use SET\User;

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
        $createdTrainingTypes = factory(SET\TrainingType::class,5)->create([]);

        // Logged in as admin - Can access the page
        $this->action('GET', 'TrainingTypeController@index');

        $this->seePageIs('trainingtype');
        $this->assertViewHas('trainingtypes');

        // Logged in as a regular user - Cannot access the page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/trainingtype');

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with role view - Can access the page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/trainingtype');

        $this->seeStatusCode(200); // Ok status code
        $this->seePageIs('trainingtype');
        $this->assertViewHas('trainingtypes');
        // Verify page components (views\trainingtype\index.blade.php)
        $this->see('<span class="card-title">Training Types</span>');
        $this->see('<th>Name</th>'); // Table column
        $this->see('<th>Status</th>'); // Table column
        $this->dontSee('<th>Modify</th>'); // Table column
        foreach ($createdTrainingTypes as $key => $createdTrainingType) {
            $this->see($createdTrainingType->name); // Table items of types
        }
        $this->dontSee('class="material-icons">mode_edit</i>'); // Table item edit
        $this->dontSee('class="material-icons">delete</i>'); // Table item delete
        $this->dontSee('data-tooltip="Create Training Type">'); // NEW icon button

        // Logged in as a user with role edit - Can access the page
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $this->call('GET', '/trainingtype');

        $this->seeStatusCode(200); // Ok status code
        $this->seePageIs('trainingtype');
        $this->assertViewHas('trainingtypes');
        // Verify page components (views\trainingtype\index.blade.php)
        $this->see('data-tooltip="Create Training Type">'); // Title
        $this->see('<th>Description</th'); // Table column
        $this->see('<th>Modify</th>'); // Table column
        $this->see('class="material-icons">mode_edit</i>'); // Table item edit
        $this->see('class="material-icons">delete</i>'); // Table item delete
        $this->see('data-tooltip="Create Training Type">'); // NEW icon button
    }

    /**
     * @test
     */
    public function it_displays_the_create_page()
    {
        // Logged in as admin - Can access the training create page
        $this->call('GET', 'trainingtype/create');

        $this->seePageIs('trainingtype/create');

        // Logged in as a regular user - Cannot access the page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', '/trainingtype/create');

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with role view - Cannot access the page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', '/trainingtype/create');

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with role edit - Can access the page
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $this->call('GET', '/trainingtype/create');

        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('trainingtype/create');

        // Verify page components (views\trainingtype\create.blade.php, views\trainingtype\_trainingtype_form.blade.php)
        $this->see('Create Training Type'); // Title
        $this->see('Name:'); // Field
        $this->see('Status:'); // Field
        $this->see('Description:'); // Field

        $this->see('type="submit" value="Create"'); // Button
        $this->see('<strong>Create/Update button</strong>'); // Help Text (views\trainingtype\_form.blade.php)
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
                 'description' => null];
        $response = $this->call('POST', 'trainingtype', $data);

        $this->seeStatusCode(302); // Redirection status code
        $this->assertRedirectedTo('trainingtype'); // Only check that you're redirecting to a specific URI
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.

        // Logged in as a regular user - Does not store the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $response = $this->call('POST', 'trainingtype', $data);

        $this->seeStatusCode(403); // Forbidden status code
        $this->assertEquals($response->content(),'Forbidden');
        $this->assertFalse($response->isRedirection()); // Redirected

        // Logged in as a user with role view - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $response = $this->call('POST', 'trainingtype', $data);

        $this->seeStatusCode(403); // Forbidden status code
        $this->assertEquals($response->content(),'Forbidden');
        $this->assertFalse($response->isRedirection()); // Redirected

        // Logged in as a user with edit view - Does store the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->call('POST', 'trainingtype', $data);

        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $this->seeStatusCode(302); // Redirection status code
        $this->assertRedirectedTo('trainingtype'); // Only check that you're redirecting to a specific URI
    }

    /**
     * @test
     */
    public function it_does_not_store_trainingtype_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $data = []; // Required data not provided
        $response = $this->call('POST', 'trainingtype', $data);

        // Handle error redirection
        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $this->seeStatusCode(302); // Redirection status code

        // Test the StoreTrainingTypeRequest handling
        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['name']);
        $this->assertSessionHasErrors('name', 'The name field is required.');
        $this->assertSessionHasErrors(['status']);
        $this->assertSessionHasErrors('status', 'The name field is required.');

        // Test when only name value is entered.
        $data = ['name' => 'Sample Name'];
        $this->call('POST', 'trainingtype', $data);
        $this->seeStatusCode(302); // Redirection status code

        $this->assertSessionHasErrors();
        $this->assertSessionHasErrors(['status']);
        $this->assertSessionHasErrors('status', 'The status field is required.');

        // Test when name & status value is entered. (all required data is entered)
        $data = ['name' => 'Sample Name', 'status' => 1];
        $response = $this->call('POST', 'trainingtype', $data);
        $this->seeStatusCode(302); // Redirection status code
        $this->assertRedirectedTo('trainingtype'); // Only check that you're redirecting to a specific URI
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
        $createdTrainings = factory(SET\Training::class,6)->create([]);
        foreach ($createdTrainings as $createdTraining) {
            // Associating trainingtype to a Training
            $createdTraining->trainingType()->associate($createdTrainingType);
            $createdTraining->save();
        }
        $this->assertEquals($createdTrainingType->trainings()->count(),$createdTrainings->count());

        // Logged in as admin - Can see the trainingtype details
        // Logged in as a regular user - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $this->call('GET', "trainingtype/$createdTrainingTypeId");

        $this->seePageIs('/trainingtype/'.$createdTrainingTypeId);
        $this->assertViewHas('trainingtype');
        $this->assertViewHas('trainings');

        // Verify page components (views\trainingtype\show.blade.php)
        $this->see($createdTrainingType->name); // Card Title
        $this->see('<i class="material-icons">mode_edit</i>'); // Edit icon
        $this->see('Status'); // Field
        $this->see('Description:'); // Field
        $this->see('<th>Associated Trainings</th>'); // Table column
        foreach ($createdTrainings as $key => $createdTraining) {
            $this->see($createdTraining->name); // Table items of types
        }

        // Logged in as a view user - Does not store the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', "trainingtype/$createdTrainingTypeId");

        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('/trainingtype/'.$createdTrainingTypeId);
        // Verify page components (views\trainingtype\show.blade.php)
        $this->dontSee('<i class="material-icons">mode_edit</i>'); // Edit icon

        // Logged in as a view user - Does not store the training
        $newuser = factory(User::class)->create(['role' => '']);
        $this->actingAs($newuser);
        $response = $this->call('GET', "trainingtype/$createdTrainingTypeId");
        $this->seeStatusCode(403); // Forbidden status code
        $this->see('Whoops, looks like something went wrong.');
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
        $this->call('GET', "trainingtype/$createdTrainingTypeId/edit");

        $this->seeStatusCode(200); // OK request response
        $this->seePageIs('/trainingtype/'.$createdTrainingTypeId.'/edit');
        $this->assertViewHas('trainingtype');

        // Logged in as a regular user - Cannot edit the trainingtype details
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('GET', "trainingtype/$createdTrainingTypeId/edit");

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with role view - Cannot edit the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('GET', "trainingtype/$createdTrainingTypeId/edit");

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with edit view - Cannot edit the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $this->call('GET', "trainingtype/$createdTrainingTypeId/edit");

        $this->seeStatusCode(200); // OK status code
        $this->seePageIs('/trainingtype/'.$createdTrainingTypeId.'/edit');
        $this->assertViewHas('trainingtype');

        // Verify page components (views\trainingtype\edit.blade.php, views\trainingtype\_trainingtype_form.blade.php)
        $this->see('Edit Training Type'); // card-title
        $this->see('Name:'); // Field
        $this->see('Status:'); // Field
        $this->see('Description:'); // Field
        $this->see('type="submit" value="Update"'); // Button
        $this->see('Create/Update button'); // Help Text (views\trainingtype\_form.blade.php)
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
                 'description' => 'Sample Trainging Type Descripiton'];

        $this->call('PATCH', "trainingtype/$createdTrainingTypeId", $data);
        $this->assertRedirectedTo("/trainingtype/$createdTrainingTypeId");

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
                 'description' => 'Sample Trainging Type Descripiton'];
        // Logged in as a regular user - Cannot update the training
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $this->call('PATCH', "trainingtype/$createdTrainingTypeId", $data);
        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with role view - Cannot update the training
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $this->call('PATCH', "trainingtype/$createdTrainingTypeId", $data);
        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a user with edit view - Can update the training
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        $response = $this->call('PATCH', "trainingtype/$createdTrainingTypeId", $data);

        $this->assertTrue($response->isRedirection()); // Make sure you've been redirected.
        $this->assertFalse($response->isOk()); // Just check that you don't get a 200 OK response.
        $this->seeStatusCode(302); // Redirection status code
        $this->assertRedirectedTo('trainingtype/'.$createdTrainingTypeId); // Only check that you're redirecting to a specific URI
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
        $response = $this->call('DELETE', "trainingtype/$createdTrainingTypeId");

        $this->assertEquals($response->content(),'');
        $this->seeStatusCode(200);  // OK status code
        // Assert that a null object is returned.
        $deletedTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNull($deletedTrainingType);

        // Delete again the created trainingtype.
        $this->call('DELETE', "trainingtype/$createdTrainingTypeId");
        $this->seeStatusCode(404);  // Not Found status code
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
        $this->assertEquals(TrainingType::first()->id,Training::first()->training_type_id);
        $this->assertEquals(Training::has('trainingType')->count(), 1);
        $this->assertEquals(TrainingType::first()->id,Training::has('trainingType')->first()->id);
        $this->assertEquals(TrainingType::first()->id,Training::first()->trainingtype->id);
        $this->assertEquals(TrainingType::first()->name,Training::first()->trainingtype->name);

        // Ensure training is associated with trainingtype
        $this->assertEquals(TrainingType::has('trainings')->count(), 1);
        $this->assertEquals(TrainingType::first()->trainings()->first()->id,
            Training::first()->id);
        $this->assertEquals(TrainingType::first()->trainings()->first()->id,
            Training::first()->id);
        $this->assertEquals(TrainingType::first()->trainings()->first()->training_type_id,
            TrainingType::first()->id);

        // Delete the created trainingtype as admin
        $this->call('DELETE', "trainingtype/$createdTrainingTypeId");

        $this->seeStatusCode(200);  // OK status code
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
        $this->call('DELETE', "trainingtype/$createdTrainingTypeId");

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as a view user
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        // Cannot access the delete trainingtype as view user
        $this->call('DELETE', "trainingtype/$createdTrainingTypeId");

        $this->seeStatusCode(403); // Forbidden status code

        // Logged in as an edit user
        $newuser = factory(User::class)->create(['role' => 'edit']);
        $this->actingAs($newuser);
        // Delete the created trainingtype. Assert that a null object is returned.
        $response = $this->call('DELETE', "trainingtype/$createdTrainingTypeId");

        $this->seeStatusCode(200); // OK status code
        $deletedTrainingType = TrainingType::find($createdTrainingTypeId);
        $this->assertNull($deletedTrainingType);
    }
}
