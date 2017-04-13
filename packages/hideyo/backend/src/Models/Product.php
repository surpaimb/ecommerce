<?php 

namespace Hideyo\Backend\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Carbon\Carbon;
use Elasticquent\ElasticquentTrait;

class Product extends Model
{
    use ElasticquentTrait, Sluggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */    
    protected $table = 'product';

    // Add the 'avatar' attachment to the fillable array so that it's mass-assignable on this model.
    protected $fillable = ['active', 'discount_promotion', 'discount_type', 'discount_value', 'discount_start_date', 'discount_end_date', 'title', 'brand_id', 'product_category_id', 'reference_code', 'ean_code', 'mpn_code', 'short_description', 'description', 'ingredients', 'price', 'commercial_price', 'tax_rate_id', 'amount', 'meta_title', 'meta_description', 'meta_keywords', 'shop_id', 'modified_by_user_id', 'weight', 'leading_atrribute_group_id'];

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function __construct(array $attributes = array())
    {
        $this->table = config()->get('hideyo.db_prefix').$this->table;
        parent::__construct($attributes);
    }

    function getIndexName()
    {
        return 'product';
    }

    protected function getExistingSlugs($slug)
    {
        $config = $this->getSluggableConfig();
        $save_to = $config['save_to'];
        $include_trashed = $config['include_trashed'];

        $instance = new static;

        $query = $instance->where($save_to, 'LIKE', $slug . '%');

        // @overriden - changed this to scope unique slugs per user
        $query = $query->where('shop_id', $this->shop_id);

        // include trashed models if required
        if ($include_trashed && $this->usesSoftDeleting()) {
            $query = $query->withTrashed();
        }

        // get a list of all matching slugs
        $list = $query->pluck($save_to, $this->getKeyName())->toArray();

        // Laravel 5.0/5.1 check
        return $list instanceof Collection ? $list->all() : $list;
    }
    

    public function beforeValidate()
    {
        $this->sluggify();
    }
    
    public function setDiscountValueAttribute($value)
    {
        if ($value) {
            $this->attributes['discount_value'] = $value;
        } else {
            $this->attributes['discount_value'] = null;
        }
    }


    public function setBrandIdAttribute($value)
    {
        if ($value) {
            $this->attributes['brand_id'] = $value;
        } else {
            $this->attributes['brand_id'] = null;
        }
    }


    public function setDiscountStartDateAttribute($value)
    {
        if ($value) {
            $date = explode('/', $value);
            $value = Carbon::createFromDate($date[2], $date[1], $date[0])->toDateTimeString();
            $this->attributes['discount_start_date'] = $value;
        } else {
            $this->attributes['discount_start_date'] = null;
        }
    }

    public function getDiscountStartDateAttribute($value)
    {
        if ($value) {
            $date = explode('-', $value);
            return $date[2].'/'.$date[1].'/'.$date[0];
        } else {
            return null;
        }
    }

    public function setDiscountEndDateAttribute($value)
    {
        if ($value) {
            $date = explode('/', $value);
            $value = Carbon::createFromDate($date[2], $date[1], $date[0])->toDateTimeString();
            $this->attributes['discount_end_date'] = $value;
        } else {
            $this->attributes['discount_end_date'] = null;
        }
    }

    public function getDiscountEndDateAttribute($value)
    {
        if ($value) {
            $date = explode('-', $value);
            return $date[2].'/'.$date[1].'/'.$date[0];
        } else {
            return null;
        }
    }

    public function getPriceDetails()
    {

        if ($this->price) {
            if (isset($this->taxRate->rate)) {
                $taxRate = $this->taxRate->rate;
                $price_inc = (($this->taxRate->rate / 100) * $this->price) + $this->price;
                $tax_value = $price_inc - $this->price;
            } else {
                $taxRate = 0;
                $price_inc = 0;
                $tax_value = 0;
            }

            $discount_price_inc = false;
            $discount_price_ex = false;
            $discountTaxRate = 0;
            if ($this->discount_value) {
                if ($this->discount_type == 'amount') {
                    $discount_price_inc = $price_inc - $this->discount_value;

                    if ($this->shop->wholesale) {
                        $discount_price_ex = $this->price - $this->discount_value;
                    } else {
                        $discount_price_ex = $discount_price_inc / 1.21;
                    }
                } elseif ($this->discount_type == 'percent') {
                    if ($this->shop->wholesale) {
                        $discount = ($this->discount_value / 100) * $this->price;
                        $discount_price_ex = $this->price - $discount;
                    } else {
                        $tax = ($this->discount_value / 100) * $price_inc;
                        $discount_price_inc = $price_inc - $tax;
                        $discount_price_ex = $discount_price_inc / 1.21;
                    }
                }
                $discountTaxRate = $discount_price_inc - $discount_price_ex;
                $discount_price_inc = $discount_price_inc;
                $discount_price_ex = $discount_price_ex;
            }

            $commercialPrice = null;
            if ($this->commercial_price) {
                $commercialPrice = number_format($this->commercial_price, 2, '.', '');
            }

            return array(
                'orginal_price_ex_tax'  => $this->price,
                'orginal_price_ex_tax_number_format'  => number_format($this->price, 2, '.', ''),
                'orginal_price_inc_tax' => $price_inc,
                'orginal_price_inc_tax_number_format' => number_format($price_inc, 2, '.', ''),
                'commercial_price_number_format' => $commercialPrice,
                'tax_rate' => $taxRate,
                'tax_value' => $tax_value,
                'currency' => 'EU',
                'discount_price_inc' => $discount_price_inc,
                'discount_price_inc_number_format' => number_format($discount_price_inc, 2, '.', ''),
                'discount_price_ex' => $discount_price_ex,
                'discount_price_ex_number_format' => number_format($discount_price_ex, 2, '.', ''),
                'discount_tax_value' => $discountTaxRate,
                'discount_value' => $this->discount_value,
                'amount' => $this->amount
            );
        } else {
            return null;
        }
    }

    public function shop()
    {
        return $this->belongsTo('Hideyo\Backend\Models\Shop');
    }

    public function attributeGroup()
    {
        return $this->belongsTo('Hideyo\Backend\Models\AttributeGroup', 'leading_atrribute_group_id');
    }
    

    public function extraFields()
    {
        return $this->hasMany('Hideyo\Backend\Models\ProductExtraFieldValue');
    }

    public function taxRate()
    {
        return $this->belongsTo('Hideyo\Backend\Models\TaxRate');
    }

    public function brand()
    {
        return $this->belongsTo('Hideyo\Backend\Models\Brand');
    }


    public function productCategory()
    {
        return $this->belongsTo('Hideyo\Backend\Models\ProductCategory');
    }

    public function subcategories()
    {
        return $this->belongsToMany('Hideyo\Backend\Models\ProductCategory', config()->get('hideyo.db_prefix').'product_sub_product_category');
    }

    public function relatedProducts()
    {
        return $this->belongsToMany('Hideyo\Backend\Models\Product', 'product_related_product', 'product_id', config()->get('hideyo.db_prefix').'related_product_id');
    }

    public function relatedProductsActive()
    {
        return $this->belongsToMany('Hideyo\Backend\Models\Product', 'product_related_product', 'product_id', 'related_product_id')->whereHas('productCategory', function ($query) {
            $query->where('active', '=', '1');
        })->where('product.active', '=', '1');
    }


    public function productImages()
    {
        return $this->hasMany('Hideyo\Backend\Models\ProductImage');
    }

    public function attributes()
    {
        return $this->hasMany('Hideyo\Backend\Models\ProductAttribute');
    }

    public function amountOptions()
    {
        return $this->hasMany('Hideyo\Backend\Models\ProductAmountOption');
    }

    public function amountSeries()
    {
        return $this->hasMany('Hideyo\Backend\Models\ProductAmountSeries');
    }
}
