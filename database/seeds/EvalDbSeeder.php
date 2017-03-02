<?php

use Illuminate\Database\Seeder;

class EvalDbSeeder extends Seeder
{
    /**
     * The purpose of this seeder is to populate the application database with
     * representative values for both prospective adopter product evaluation
     * and for test data.
     *
     * @return void
     */
    public function run()
    {
        // Create user data
        DB::table('users')->where('id', '>', '2')->delete();
        // three roles (edit, view, ''); three states (active,separated,destroyed)
        factory(SET\User::class, 3)->create(['role' => 'edit',
            'created_at'                            => Carbon\Carbon::today()->addWeeks(-2), ]); // Admin users
        $supervisors = factory(SET\User::class, 3)->create(['role' => 'view',
            'created_at'                                           => Carbon\Carbon::today()->addDays(-2), ]); // audit users
        $supervisorID = $supervisors->first()->id;
        $supervisors = factory(SET\User::class, rand(10, 10))->create(['role' => '', 'status' => 'active',
            'created_at'                                                     => Carbon\Carbon::today()->addWeeks(-9),
            'supervisor_id'                                                  => $supervisorID,
            'elig_date'                                                      => Carbon\Carbon::today()->addMonths(-6)->format('Y-m-d'),
            'clearance'                                                      => 'interim secret',
            'inv'                                                            => 'N/A',
            'inv_close'                                                      => null,
        ]); // Regular active users
        $supervisorIds = $supervisors->pluck('id')->toArray();
        $supervisorID2 = array_pop($supervisorIds);
        for ($i = 0; $i < rand(35, 35); $i++) {
            $elig_date = Carbon\Carbon::today()->addYears(-5)->addDays(rand(0, 365));
            factory(SET\User::class)->create(['role' => '', 'status' => 'active',
                'created_at'                         => Carbon\Carbon::today()->addWeeks(-9),
                'supervisor_id'                      => rand($supervisorID, $supervisorID2),
                'elig_date'                          => $elig_date->format('Y-m-d'),
                'clearance'                          => 'secret',
                'inv'                                => 'ABCXYZ',
                'inv_close'                          => $elig_date->addWeeks(-2)->format('Y-m-d'),
            ]); // Regular active users
        }
        factory(SET\User::class, 3)->create(['role' => '', 'status' => 'destroyed',
          'destroyed_date'                        => Carbon\Carbon::tomorrow()->addDays(rand(0, 4)),
          'created_at'                            => Carbon\Carbon::today()->addWeeks(-9), ]); // Regular destroyed users
      factory(SET\User::class, 3)->create(['role' => '', 'status' => 'separated',
          'destroyed_date'                        => Carbon\Carbon::today()->addMonths(rand(3, 9)),
          'created_at'                            => Carbon\Carbon::today()->addWeeks(-9), ]); // Regular separated users
        DB::table('activity_log')->truncate();

        $createdUsers = SET\User::all();
        dump('Created Users: '.$createdUsers->count());

        // Associate groups to a user
        DB::table('groups')->truncate();
        DB::table('group_training')->truncate();
        DB::table('group_user')->truncate();
        // factory(SET\Group::class, 5)->create();
        factory(SET\Group::class)->create(['name' => 'Alpha Team']);
        factory(SET\Group::class)->create(['name' => 'Bravo Building']);
        factory(SET\Group::class)->create(['name' => 'Charlie Delta Grp']);
        factory(SET\Group::class)->create(['name' => 'Echo Foxtrot Lab']);
        factory(SET\Group::class)->create(['name' => 'Xray Yankee Zulu']);
        factory(SET\Group::class, 5)->create([]);
        $createdGroups = SET\Group::all();
        foreach ($createdUsers as $createdUser) {
            if ($createdUser->destroyed_date) {
                continue;
            } // Do not assign user
            if ($createdUser->id < 3) {
                continue;
            } // Exclude admin
            foreach ($createdGroups as $createdGroup) {
                if (rand(1, 6) != 1) {
                    continue;
                } // So not all users get group
                $createdGroup->users()->attach($createdUser);
            }
        }
        dump('Created Groups: '.$createdGroups->count());

        // Create Trainings
        DB::table('trainings')->truncate();
        factory(SET\Training::class, rand(15, 20))->create();
        $createdTrainings = SET\Training::all();
        dump('Created Trainings: '.$createdTrainings->count());

        // Associate Training to a Group
        DB::table('training_user')->truncate();
        foreach ($createdGroups as $createdGroup) {
            foreach ($createdTrainings as $createdTraining) {
                if (rand(1, 3) != 1) {
                    continue;
                } // So not all groups get training
                $createdGroup->trainings()->attach($createdTraining->id);
                $groupUsers = $createdGroup->users()->get();
                foreach ($groupUsers as $groupUser) {
                    if ($groupUser->destroyed_date) {
                        continue;
                    } // Do not attach trainings
                    $trainingDate = Carbon\Carbon::today()->AddWeeks(rand(-26, 52));
                    if (rand(1, 10) == 1) { //Due
                          $due_date = Carbon\Carbon::today()->addWeeks(rand(1, 12))->format('Y-m-d');
                        $completed_date = null;
                    } elseif (rand(1, 25) == 1) { // Late
                        $due_date = Carbon\Carbon::today()->addWeeks(rand(-8, 0))->format('Y-m-d');
                        $completed_date = null;
                    } else { // Completed
                        $pastWeeks = rand(-48, 0);
                        $due_date = Carbon\Carbon::today()->addWeeks($pastWeeks)->addDays(rand(-21, 21))->format('Y-m-d');
                        $completed_date = Carbon\Carbon::today()->addWeeks($pastWeeks)->format('Y-m-d');
                    }
                    $data = [
                        'author_id'       => 2,
                        'user_id'         => $groupUser->id,
                        'due_date'        => $due_date,
                        'completed_date'  => $completed_date,
                        'training_id'     => $createdTraining->id,
                    ];
                    $trainingUser = SET\TrainingUser::create($data);
                }
            }
        }
        $createdTrainingUsers = SET\TrainingUser::all();
        dump('Created TrainingUsers: '.$createdTrainingUsers->count());

        // Associate trainings to a trainingType
        DB::table('training_types')->truncate();
        // factory(SET\TrainingType::class, 3)->create();
        factory(SET\TrainingType::class)->create(['name' => 'Human Resources']);
        factory(SET\TrainingType::class)->create(['name' => 'Informaton Systems']);
        factory(SET\TrainingType::class)->create(['name' => 'Security']);
        $createdTrainingTypes = SET\TrainingType::all();
        $ttCount = 1;
        foreach ($createdTrainings as $createdTraining) {
            if ($createdUser->id < 3) {
                continue;
            } // Exclude admin
            if (rand(1, 4) == 1) {
                continue;
            } // So not all trainings get types
            $createdTrainingType = $createdTrainingTypes->where('id', '=', $ttCount)->first();
            $createdTraining->trainingType()->associate($createdTrainingType);
            $createdTraining->save();
            if (++$ttCount > $createdTrainingTypes->count()) {
                $ttCount = 1;
            }
        }
        dump('Created TrainingTypes: '.$createdTrainingTypes->count());

        // Associate duties to a User/Group
        DB::table('duties')->truncate();
        DB::table('duty_user')->truncate();
        DB::table('duty_group')->truncate();
        DB::table('duty_swaps')->truncate();
        $duty = factory(SET\Duty::class)->create([
            'name'  => 'Officer of the Day', 'cycle'  => 'daily', ]);
        $duty = factory(SET\Duty::class)->create([
            'name'  => 'End of Day Check', 'cycle'  => 'weekly', ]);
        $duty = factory(SET\Duty::class)->create([
            'name'  => 'Fire systems monthly check', 'cycle'  => 'monthly', ]);
        $createdDuties = SET\Duty::all();
        // Assign individual users to duties
        foreach ($createdDuties as $duty) {
            $userIDs = [];
            foreach ($createdUsers as $createdUser) {
                if ($createdUser->id < 3) {
                    continue;
                } // Exclude admin
                    if ($createdUser->destroyed_date) {
                        continue;
                    } // do not assign
                    if (rand(1, 4) != 1) {
                        continue;
                    } // So not all get one
                    array_push($userIDs, $createdUser->id);
            }
            $duty->users()->attach($userIDs);
        }
        // Assign group(s) to duties
        for ($i = 0; $i < 3; $i++) {
            $duty = factory(SET\Duty::class)->create([
                'cycle'  => 'weekly', 'has_groups' => '1', ]);
            $groupIDs = [];
            foreach ($createdGroups as $createdGroup) {
                if (rand(1, 2) != 1) {
                    continue;
                } // So not all get one
                array_push($groupIDs, $createdGroup->id);
            }
            $duty->groups()->attach($groupIDs);
        }
        $createdDuties = SET\Duty::all();
        dump('Created Duties: '.$createdDuties->count());

        // Associate visits to a User
        DB::table('visits')->truncate();
        foreach ($createdUsers as $createdUser) {
            for ($i = 0; $i < 6; $i++) {
                if ($createdUser->id < 3) {
                    continue;
                } // Exclude admin
                if (rand(1, 2) != 1) {
                    continue;
                } // So not all get one
                factory(SET\Visit::class, 'seeder')->create([
                    'author_id'       => 2,
                    'user_id'         => $createdUser->id,
                    'visit_date'      => Carbon\Carbon::today()->addWeeks(rand(-1, 7))->format('Y-m-d'),
                    'expiration_date' => Carbon\Carbon::today()->addMonths(rand(2, 12))->format('Y-m-d'),
                ]);
            }
        }
        $createdVisits = SET\Visit::all();
        dump('Created Visits: '.$createdVisits->count());

        // Associate travels to a User
        DB::table('travels')->truncate();
        foreach ($createdUsers as $createdUser) {
            if ($createdUser->destroyed_date) {
                continue;
            } // do not assign
            if ($createdUser->id < 3) {
                continue;
            }
            for ($i = 0; $i < 6; $i++) {
                if (rand(1, 20) != 1) {
                    continue;
                } // So not all get one
                $travelDate = Carbon\Carbon::today()->addDays(rand(-7, 45));
                factory(SET\Travel::class, 'seeder')->create([
                    'author_id'   => 2,
                    'user_id'     => $createdUser->id,
                    'leave_date'  => $travelDate->format('Y-m-d'),
                    'return_date' => $travelDate->addDays(14)->format('Y-m-d'),
                    'brief_date'  => $travelDate->addDays(-3)->format('Y-m-d'),
                ]);
            }
        }
        $createdTravels = SET\Travel::all();
        dump('Created Travels: '.$createdTravels->count());

        // Associate notes to a User
        DB::table('notes')->truncate();
        foreach ($createdUsers as $createdUser) {
            if ($createdUser->id < 3) {
                continue;
            }
            if (rand(1, 6) == 1) {
                continue;
            } // So not all get one
            factory(SET\Note::class, 'seeder')->create([
                'author_id'   => 2,
                'user_id'     => $createdUser->id,
            ]);
        }
        $createdNotes = SET\Note::all();
        dump('Created Notes: '.$createdNotes->count());

        // Create News
        DB::table('news')->truncate();
        for ($i = 0; $i < rand(10, 20); $i++) {
            $newsDate = Carbon\Carbon::today()->addWeeks(rand(-26, 26));
            factory(SET\News::class, 'seeder')->create([
              'publish_date' => $newsDate->format('Y-m-d'),
              'expire_date'  => $newsDate->addMonths(6)->format('Y-m-d'),
              'author_id'    => 2,
            ]);
        }
        $createdNews = SET\News::all();
        dump('Created News: '.$createdNews->count());
    }
}
