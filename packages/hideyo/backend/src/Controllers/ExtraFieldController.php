<?php namespace Hideyo\Backend\Controllers;

/**
 * ProductWeightTypeController
 *
 * This is the controller of the product weight types of the shop
 * @author Matthijs Neijenhuijs <matthijs@dutchbridge.nl>
 * @version 1.0
 */

use App\Http\Controllers\Controller;

use Hideyo\Backend\Repositories\ExtraFieldRepositoryInterface;
use Hideyo\Backend\Repositories\ProductCategoryRepositoryInterface;

use \Request;
use \Notification;
use \Redirect;

class ExtraFieldController extends Controller
{
    public function __construct(
        ExtraFieldRepositoryInterface $extraField,
        ProductCategoryRepositoryInterface $productCategory
    ) {
        $this->extraField = $extraField;
        $this->productCategory = $productCategory;
    }

    public function index()
    {
        if (Request::wantsJson()) {

            $query = $this->extraField->getModel()
            ->select([\DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id', 'all_products','title'])
            ->where('shop_id', '=', \Auth::guard('hideyobackend')->user()->selected_shop_id);
            
            $datatables = \Datatables::of($query)

            ->addColumn('category', function ($query) {
                if ($query->categories) {
                    $output = array();
                    foreach ($query->categories as $categorie) {
                        $output[] = $categorie->title;
                    }

                    return implode(' | ', $output);
                }
            })
            ->addColumn('action', function ($query) {
                $delete = \Form::deleteajax('/admin/extra-field/'. $query->id, 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'));
                $link = '<a href="/admin/extra-field/'.$query->id.'/values" class="btn btn-default btn-sm btn-info"><i class="entypo-pencil"></i>'.$query->values->count().' values</a> <a href="/admin/extra-field/'.$query->id.'/edit" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a> 
                '.$delete;
            
                return $link;
            });

            return $datatables->make(true);


        } else {
            return \View::make('hideyo_backend::extra-field.index')->with('extraField', $this->extraField->selectAll());
        }
    }

    public function create()
    {
        return \View::make('hideyo_backend::extra-field.create')->with(array('productCategories' => $this->productCategory->selectAll()->pluck('title', 'id')));
    }

    public function store()
    {
        $result  = $this->extraField->create(Request::all());

        if (isset($result->id)) {
            \Notification::success('The extra field was inserted.');
            return \Redirect::route('admin.extra-field.index');
        } else {
            foreach ($result->errors()->all() as $error) {
                \Notification::error($error);
            }
        }

        return \Redirect::back()->withInput();
    }

    public function edit($id)
    {
        return \View::make('hideyo_backend::extra-field.edit')->with(array('extraField' => $this->extraField->find($id), 'productCategories' => $this->productCategory->selectAll()->pluck('title', 'id')));
    }

    public function update($id)
    {
        $result  = $this->extraField->updateById(Request::all(), $id);

        if (isset($result->id)) {
            \Notification::success('The extra field was updated.');
            return \Redirect::route('admin.extra-field.index');
        } else {
            foreach ($result->errors()->all() as $error) {
                \Notification::error($error);
            }
        }

        return \Redirect::back()->withInput();
    }

    public function destroy($id)
    {
        $result  = $this->extraField->destroy($id);

        if ($result) {
            Notification::success('Extra field was deleted.');
            return Redirect::route('admin.extra-field.index');
        }
    }
}