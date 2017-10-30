<?php

namespace Tests\Integration\Controllers;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use SET\User;
use SET\Visit;

class VisitControllerTest extends TestCase
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
        // Logged in as admin - Can access the visit create page
        $userId = $this->user->id;

        $response = $this->get("/user/$userId/visit/create");
        $response->assertStatus(200);
        $response->assertSee('Add a Visit');
        $response->assertSee('Visit date');
        $response->assertSee('Expiration date');
        $response->assertSee('Point of Contact');
        $response->assertSee('PoC Phone Number');

        // Create a regular user - Still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        // Admin can access another user's visit create page
        $response = $this->get("/user/$userId/visit/create");
        $response->assertStatus(200);
        $response->assertSee('Add a Visit');
        $response->assertSee('Visit date');
        $response->assertSee('Expiration date');
        $response->assertSee('Point of Contact');
        $response->assertSee('PoC Phone Number');

         // Logged in as a regular user - Cannot access the visit create page
        $this->actingAs($newuser);
        $response = $this->get("/user/$userId/visit/create");
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the visit create page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/visit/create");

        $response->assertStatus(403);
    }

   /**
    * @test
    */
   public function it_stores_the_visit_by_testing_each_user_role()
   {
       // Logged in as admin - Can store the visit
       $userId = $this->user->id;
       $data = ['smo_code'          => 'A SMO Code',
                 'visit_date'       => '2016-12-10',
                 'expiration_date'  => '2016-12-20',
                 'poc'              => 'A point of contact',
                 'phone'            => '12345',
                 'comment'          => 'Comment on visit', ];

       $response = $this->post("/user/$userId/visit/", $data);
       $response->assertRedirect('user/'.$userId);

       // Logged in as a regular user - Does not store the visit
       $newuser = factory(User::class)->create();
       $this->actingAs($newuser);
       $userId = $newuser->id;
       $response = $this->post("/user/$userId/visit/", $data);
       $response->assertStatus(403);

       // Logged in as a user with role view - Does not store the visit
       $newuser = factory(User::class)->create(['role' => 'view']);
       $this->actingAs($newuser);
       $userId = $newuser->id;
       $response = $this->post("/user/$userId/visit/", $data);
       $response->assertStatus(403);
   }

    /**
     * @test
     */
    public function it_does_not_store_visit_with_invalid_data()
    {
        // Logged in as admin - No data provided
        $userId = $this->user->id;
        $data = [];

        $response = $this->post("/user/$userId/visit/", $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['smo_code', 'expiration_date']);
        $response->assertSessionHasErrors('smo_code', 'The smo_code field is required.');
        $response->assertSessionHasErrors('expiration_date', 'The expiration date field is required.');

        $data = ['smo_code'         => '',
                 'visit_date'       => '',
                 'expiration_date'  => '',
                 'poc'              => '',
                 'phone'            => '',
                 'comment'          => '', ];

        $response = $this->post("/user/$userId/visit/", $data);
        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['smo_code', 'expiration_date']);
        $response->assertSessionHasErrors('smo_code', 'The smo_code field is required.');
        $response->assertSessionHasErrors('expiration_date', 'The expiration date field is required.');
    }

    /**
     * @test
     */
    public function can_edit_the_visit()
    {
        // Create a visit object
        $userId = $this->user->id;
        $visitToCreate = factory(Visit::class)->create();
        $createdVisitId = $visitToCreate->id;

        // Logged in as admin - Can edit the visit
        $response = $this->get("/user/$userId/visit/$createdVisitId/edit");
        $response->assertStatus(200);
        $response->assertSee("Update a Visit");
        $response->assertViewHas('user');
        $response->assertViewHas('visit');

        // Logged in as a regular user - Cannot access the visit edit page
        $newuser = factory(User::class)->create();
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/visit/$createdVisitId/edit");
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot access the visit edit page
        $newuser = factory(User::class)->create(['role' => 'view']);
        $this->actingAs($newuser);
        $userId = $newuser->id;
        $response = $this->get("/user/$userId/visit/$createdVisitId/edit");
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_updates_the_visit()
    {
        // Create a visit object
        $userId = $this->user->id;
        $visitToCreate = factory(Visit::class)->create();
        $createdVisitId = $visitToCreate->id;

        // Logged in as admin - Can update the visit
        $data = ['smo_code'         => 'A SMO Code',
                 'visit_date'       => '2016-12-11',
                 'expiration_date'  => '2016-12-28', ];
                 //'poc'              => "A point of contact",
                 //'phone'            => "12345",
                 //'comment'          => 'Comment on visit'];

        $response = $this->patch("/user/$userId/visit/$createdVisitId", $data);

        $response->assertRedirect('user/'.$userId);

        $createdVisit = Visit::find($createdVisitId);
        $this->assertNotEquals($createdVisit->smo_code, $visitToCreate->smo_code);
        $this->assertEquals($createdVisit->smo_code, $data['smo_code']);
        $this->assertEquals($createdVisit->visit_date, $data['visit_date']);
        $this->assertEquals($createdVisit->expiration_date, $data['expiration_date']);

        // Create a regular user - Still logged in as admin
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;

        $response = $this->patch("/user/$userId/visit/$createdVisitId", $data);
        $response->assertRedirect('user/'.$userId);

        // Logged in as new user. User cannot edit own visit
        $this->actingAs($newuser);
        $response = $this->patch("/user/$userId/visit/$createdVisitId", $data);
        $response->assertStatus(403);

        // Logged in as a user with role view - Cannot edit visit
        $newuser = factory(User::class)->create(['role' => 'view']);
        $userId = $newuser->id;
        $this->actingAs($newuser);
        $response = $this->patch("/user/$userId/visit/$createdVisitId", $data);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function it_deletes_the_visit()
    {
        // Create a visit object
        $userId = $this->user->id;
        $visitToCreate = factory(Visit::class)->create();
        $createdVisitId = $visitToCreate->id;

        // Ensure the created visit is in the database
        $createdVisit = Visit::find($createdVisitId);
        $this->assertNotNull($createdVisit);
        $this->assertEquals($createdVisit->id, $createdVisitId);

        // Delete the created visit. Assert that a null object is returned.
        $response = $this->delete("/user/$userId/visit/$createdVisitId");
        $deletedVisit = Visit::find($createdVisitId);
        $this->assertNull($deletedVisit);

        // Logged in as a regular user
        $newuser = factory(User::class)->create();
        $userId = $newuser->id;
        $this->actingAs($newuser);

        // Cannot access the delete visit page since the visit is already deleted
        $response = $this->delete("/user/$userId/visit/$createdVisitId");
        $response->assertStatus(404);

        // Create a new visit and try to delete. Get forbidden status code
        $visitToCreate = factory(Visit::class)->create();
        $createdVisitId = $visitToCreate->id;
        $response = $this->delete("/user/$userId/visit/$createdVisitId");
        $response->assertStatus(403);
    }
}
