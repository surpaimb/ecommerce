<?php namespace Hideyo\Backend\Controllers;

use App\Http\Controllers\Controller;

/**
 * ClientController
 *
 * This is the controller for the shop clients
 * @author Matthijs Neijenhuijs <matthijs@dutchbridge.nl>
 * @version 1.0
 */


use Hideyo\Backend\Repositories\CouponRepositoryInterface;
use Hideyo\Backend\Repositories\ProductCategoryRepositoryInterface;
use Hideyo\Backend\Repositories\ProductRepositoryInterface;
use Hideyo\Backend\Repositories\SendingMethodRepositoryInterface;
use Hideyo\Backend\Repositories\PaymentMethodRepositoryInterface;

use Illuminate\Http\Request;
use Notification;

class CouponController extends Controller
{
    public function __construct(Request $request, SendingMethodRepositoryInterface $sendingMethod, PaymentMethodRepositoryInterface $paymentMethod, CouponRepositoryInterface $coupon, ProductCategoryRepositoryInterface $productCategory, ProductRepositoryInterface $product)
    {
        $this->request = $request;
        $this->coupon = $coupon;
        $this->product = $product;
        $this->productCategory = $productCategory;
        $this->sendingMethod = $sendingMethod;
        $this->paymentMethod = $paymentMethod;
    }

    public function index()
    {
        if ($this->request->wantsJson()) {
            $query = $this->coupon->getModel()->select(
                [
                \DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                $this->coupon->getGroupModel()->getTable().'.title as coupontitle']
            )->where($this->coupon->getModel()->getTable().'.shop_id', '=', \Auth::guard('hideyobackend')->user()->selected_shop_id)


            ->with(array('couponGroup'))        ->leftJoin($this->coupon->getGroupModel()->getTable(), $this->coupon->getGroupModel()->getTable().'.id', '=', $this->coupon->getModel()->getTable().'.coupon_group_id');
            
            
            $datatables = \Datatables::of($query)

            ->filterColumn('title', function ($query, $keyword) {
                $query->whereRaw("coupon.title like ?", ["%{$keyword}%"]);
            })

            ->filterColumn('grouptitle', function ($query, $keyword) {
                $query->whereRaw("coupon_group.title like ?", ["%{$keyword}%"]);
            })
            ->addColumn('grouptitle', function ($query) {
                return $query->coupontitle;
            })

            ->addColumn('action', function ($query) {
                $delete = \Form::deleteajax('/admin/coupon/'. $query->id, 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'));
                $link = '<a href="/admin/coupon/'.$query->id.'/edit" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a>  '.$delete;
            
                return $link;
            });

            return $datatables->make(true);
        } else {
            return view('hideyo_backend::coupon.index')->with('coupon', $this->coupon->selectAll());
        }
    }

    public function create()
    {
        return view('hideyo_backend::coupon.create')->with(array(
            'products'          => $this->product->selectAll()->lists('title', 'id'),
            'productCategories' => $this->productCategory->selectAll()->lists('title', 'id'),
            'groups'            => $this->coupon->selectAllGroups()->lists('title', 'id')->toArray(),
            'sendingMethods'    => $this->sendingMethod->selectAll()->lists('title', 'id'),
            'paymentMethods'    => $this->paymentMethod->selectAll()->lists('title', 'id')
        ));
    }

    public function store()
    {
        $result  = $this->coupon->create($this->request->all());
 
        if (isset($result->id)) {
            \Notification::success('The coupon was inserted.');
            return redirect()->route('hideyo.coupon.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            \Notification::error($error);
        }
        return redirect()->back()->withInput();
    }

    public function edit($id)
    {
        return view('hideyo_backend::coupon.edit')->with(
            array(
            'coupon' => $this->coupon->find($id),
            'products' => $this->product->selectAll()->lists('title', 'id'),
            'groups' => $this->coupon->selectAllGroups()->lists('title', 'id')->toArray(),
            'productCategories' => $this->productCategory->selectAll()->lists('title', 'id'),
            'sendingMethods' => $this->sendingMethod->selectAll()->lists('title', 'id'),
            'paymentMethods' => $this->paymentMethod->selectAll()->lists('title', 'id'),
            )
        );
    }

    public function update($id)
    {
        $result  = $this->coupon->updateById($this->request->all(), $id);

        if (isset($result->id)) {
            \Notification::success('The coupon method was updated.');
            return redirect()->route('hideyo.coupon.index');
        }
        
        foreach ($result->errors()->all() as $error) {
            \Notification::error($error);
        }
        return redirect()->back()->withInput();
    }

    public function destroy($id)
    {
        $result  = $this->coupon->destroy($id);

        if ($result) {
            Notification::success('The coupon was deleted.');
            return redirect()->route('hideyo.coupon.index');
        }
    }
}