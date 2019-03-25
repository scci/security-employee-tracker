<?php

namespace SET\Handlers\Excel;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use SET\Handlers\DateFormat;
use SET\User;

/**
 * Class JpasImportHandler.
 *
 * Going to handle importing our JPAS excel file.
 */
class JpasImportHandler implements ToCollection, WithHeadingRow
{
    use DateFormat;

    private $unique;
    private $changes;

    /**
     * Set our variables as new collections.
     */
    public function __construct()
    {
        $this->unique = new Collection();
        $this->changes = new Collection();
    }

    /**
     * Initial call from UserController.
     * Based on what we get, we will either call an initial import or the resolve import.
     *
     * @param $import
     *
     * @return array
     */
    public function collection(Collection $excel)
    {
        $data = Input::all();

        //We always pass a token and the file. If there is anything more, then we are resolving.
        if ($data['resolveImport']) {
            $this->resolveImport($excel, $data);
        } else {
            $this->initialImport($excel);
        }

        //Return this data. It is just used for the initial import, but still nice to see.
        return ['unique' => $this->unique, 'changes' => $this->changes];
    }

    /**
     * Cycle through our excel spreadsheet.
     * If we can't figure where data goes, we will push it to a collection so the user can map it.
     * Otherwise, we will update the record.
     *
     * @param $excel
     */
    private function initialImport($excel)
    {
        foreach ($excel as $row) {
            $row['eligibility_date'] = $this->transformDateVal($row['eligibility_date']);
            $row['prev_inves'] = $this->transformDateVal($row['prev_inves']);

            $row['name'] = preg_replace('/(,\s|\s)/', '_', $row['name']);
            if (!is_null($row['name']) && $row['name'] != '') { //we get a bunch of null records that we can ignore.
                    $user = User::where('jpas_name', $row['name'])->first(); //see if the record maps to a user
                if (is_null($user)) {
                    $this->unique->push($row); //if no results, we need to have the admin map this record to a user.
                } else {
                    $this->getUserChanges($this->mapJpasToUser($user, $row));
                }
            }
        }
    }

    /**
     * User has mapped some accounts. We need to update those records.
     *
     * @param $excel
     * @param $data
     */
    private function resolveImport($excel, $data)
    {
        foreach ($data as $jpasName => $userGroup) {
            if ($jpasName == 'approve') {
                $this->updateUserData($userGroup);
            } elseif ($userGroup != '' && is_numeric($userGroup)) {
                $this->importNewData($excel, $userGroup, $jpasName);
            }
        }
    }

    /**
     * Place changes so the user can approve/reject them.
     *
     * @param $user
     */
    private function getUserChanges($user)
    {
        if ($user->isDirty()) {
            foreach ($user->getDirty() as $attribute => $newValue) {
                $this->changes->push([
                    'user'     => $user,
                    'field'    => $attribute,
                    'original' => $user->getOriginal($attribute),
                    'new'      => $newValue,
                ]);
            }
        }
    }

    /**
     * Update user. If there are changes, let's log them to an array.
     *
     * @param $user
     */
    private function updateAndLogUser($user)
    {
        //We will let the user know what changes were made.
        if ($user->isDirty()) {
            $log = "The following changes have been made for $user->userFullName: \n";
            foreach ($user->getDirty() as $attribute => $value) {
                $original = $user->getOriginal($attribute);
                $log .= "Changed $attribute from '$original' to '$value'.\n";
            }
            $user->save();
            $this->changes->push($log);
        }
    }

    /**
     * Map the data from our JPAS Excel to our User model.
     *
     * @param $user
     * @param $data
     */
    private function mapJpasToUser($user, $data)
    {
        $user->jpas_name = $data['name'];
        $user->clearance = $data['eligibility'];
        $user->elig_date = $this->dateFormat($data['eligibility_date']);
        $user->inv = $data['inves'];
        $user->inv_close = $this->dateFormat($data['prev_inves']);

        return $user;
    }

    /**
     * @param $excel
     * @param $userGroup
     * @param $jpasName
     *
     * @return mixed
     */
    private function importNewData($excel, $userGroup, $jpasName)
    {
        $user = User::find($userGroup);
        foreach ($excel as $row) {
            $row['eligibility_date'] = $this->transformDateVal($row['eligibility_date']);
            $row['prev_inves'] = $this->transformDateVal($row['prev_inves']);
            $row['name'] = preg_replace('/(,\s|\s)/', '_', $row['name']);
            if ($row['name'] == $jpasName) {
                $this->updateAndLogUser($this->mapJpasToUser($user, $row));
            }
        }
    }

    /**
     * @param $userGroup
     */
    private function updateUserData($userGroup)
    {
        foreach ($userGroup as $userId => $changesGroup) {
            $user = User::find($userId);
            foreach ($changesGroup as $field => $newValue) {
                if ($newValue != '0') {
                    $user[$field] = $newValue;
                }
            }
            $this->updateAndLogUser($user);
        }
    }

    /**
     * Transform excel date values into a datetime string.
     */
    public function transformDateVal($value, $format = 'Y-m-d H:i:s')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
}
