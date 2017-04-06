<?php 

namespace Hideyo\Backend\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraFieldDefaultValue extends Model
{

    protected $table = 'extra_field_default_value';

    // Add the 'avatar' attachment to the fillable array so that it's mass-assignable on this model.
    protected $fillable = ['value', 'extra_field_id', 'modified_by_user_id'];

    public function __construct(array $attributes = array())
    {
        $this->table = config()->get('hideyo.db_prefix').$this->table;
    	
        parent::__construct($attributes);
    }
}