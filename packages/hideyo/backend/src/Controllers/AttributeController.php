<?php namespace Hideyo\Backend\Controllers;

/**
 * AttributeController
 *
 * This is the controller of the attributes of a attribute group
 * @author Matthijs Neijenhuijs <matthijs@dutchbridge.nl>
 * @version 1.0
 */

use App\Http\Controllers\Controller;
use Hideyo\Backend\Repositories\AttributeRepositoryInterface;
use Hideyo\Backend\Repositories\AttributeGroupRepositoryInterface;
use Illuminate\Http\Request;
use Notification;

class AttributeController extends Controller
{
    public function __construct(AttributeRepositoryInterface $attribute, AttributeGroupRepositoryInterface $attributeGroup)
    {
        $this->attributeGroup = $attributeGroup;
        $this->attribute = $attribute;
    }

    public function index(Request $request, $attributeGroupId)
    {
        if ($request->wantsJson()) {

            $query = $this->attribute->getModel()
            ->select([\DB::raw('@rownum  := @rownum  + 1 AS rownum'),'id','value'])
            ->where('attribute_group_id', '=', $attributeGroupId);
            
            $datatables = \Datatables::of($query)->addColumn('action', function ($query) use ($attributeGroupId) {
                $delete = \Form::deleteajax(url()->route('hideyo.attribute.destroy', array('attributeGroupId' => $attributeGroupId, 'id' => $query->id)), 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'));
                $link = ' <a href="'.url()->route('hideyo.attribute.edit', array('attributeGroupId' => $attributeGroupId, 'id' => $query->id)).'" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a>'.$delete;
            
                return $link;
            });

            return $datatables->make(true);

        } else {
            return view('hideyo_backend::attribute.index')->with('attributeGroup', $this->attributeGroup->find($attributeGroupId));
        }
    }

    public function create($attributeGroupId)
    {
        return view('hideyo_backend::attribute.create')->with(array('attributeGroup' =>  $this->attributeGroup->find($attributeGroupId)));
    }

    public function store(Request $request, $attributeGroupId)
    {
        $result  = $this->attribute->create($request->all(), $attributeGroupId);

        if (isset($result->id)) {
            Notification::success('The extra field was inserted.');
            return redirect()->route('hideyo.attribute.index', $attributeGroupId);
        } else {
            foreach ($result->errors()->all() as $error) {
                Notification::error($error);
            }
        }

        return redirect()->back()->withInput();
    }

    public function edit($attributeGroupId, $id)
    {
        return view('hideyo_backend::attribute.edit')->with(array('attribute' => $this->attribute->find($id)));
    }

    public function update(Request $request, $attributeGroupId, $id)
    {
        $result  = $this->attribute->updateById($request->all(), $attributeGroupId, $id);

        if (isset($result->id)) {
            Notification::success('The extra field was updated.');
            return redirect()->route('hideyo.attribute.index', $attributeGroupId);
        } else {
            foreach ($result->errors()->all() as $error) {
                Notification::error($error);
            }
        }

        return redirect()->back()->withInput();
    }

    public function destroy($attributeGroupId, $id)
    {
        $result  = $this->attribute->destroy($id);

        if ($result) {
            Notification::success('Extra field was deleted.');
            return Redirect::route('hideyo.attribute.index', $attributeGroupId);
        }
    }
}