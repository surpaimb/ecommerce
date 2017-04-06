<?php 

namespace Hideyo\Backend\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraField extends Model
{
    
    protected $table = 'extra_field';

    // Add the 'avatar' attachment to the fillable array so that it's mass-assignable on this model.
    protected $fillable = ['type', 'default_value', 'title', 'all_products', 'filterable', 'product_category_id', 'shop_id', 'modified_by_user_id'];

    public function __construct(array $attributes = array())
    {
        $this->table = config()->get('hideyo.db_prefix').$this->table;
        
        parent::__construct($attributes);
    }

    public function categories()
    {
        return $this->belongsToMany('Hideyo\Backend\Models\ProductCategory', 'extra_field_related_product_category');
    }

    public function productCategory()
    {
        return $this->belongsTo('Hideyo\Backend\Models\ProductCategory');
    }

    public function values()
    {
        return $this->hasMany('Hideyo\Backend\Models\ExtraFieldDefaultValue');
    }

    public function setProductCategoryIdAttribute($value)
    {
        if ($value) {
            $this->attributes['product_category_id'] = $value;
        } else {
            $this->attributes['product_category_id'] = null;
        }
    }
}
