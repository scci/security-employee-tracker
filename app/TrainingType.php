<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;

class TrainingType extends Model
{
  /**
   * @var string
   */
  protected $table = 'training_types';
  /**
   * @var bool
   */
  public $timestamps = true;
  /**
   * @var array
   */
  protected $fillable = ['name', 'description', 'sidebar', 'status'];
  /**
   * @var array
   */
  protected $dates = ['created_at', 'updated_at'];

  /**
 * Get the trainings that have the training type.
 */
  public function trainings()
  {
      return $this->hasMany('SET\Training', 'training_type_id'); // One To Many
  }

}
