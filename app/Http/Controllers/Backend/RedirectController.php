<?php namespace App\Http\Controllers\Backend;

/**
 * RedirectController
 *
 * This is the controller of the redirects of the shop
 * @author Matthijs Neijenhuijs <matthijs@hideyo.io>
 * @version 0.1
 */

use App\Http\Controllers\Controller;
use Hideyo\Ecommerce\Framework\Repositories\RedirectRepositoryInterface;
use Hideyo\Ecommerce\Framework\Repositories\ShopRepositoryInterface;

use Session;
use Apiclient;
use Input;
use Response;
use View;
use Request;
use Notification;
use Excel;

class RedirectController extends Controller
{
    public function __construct(RedirectRepositoryInterface $redirect, ShopRepositoryInterface $shop)
    {
        $this->redirect = $redirect;
        $this->shop = $shop;
    }

    public function index()
    {
        if (Request::wantsJson()) {

            $query = $this->redirect->selectAll();
            $datatables = \Datatables::of($query)

            ->addColumn('url', function ($query) {
                return '<a href="'.$query->url.'" target="_blank">'.$query->url.'</a>';
            })

            ->addColumn('action', function ($query) {
                $deleteLink = \Form::deleteajax(url()->route('redirect.destroy', $query->id), 'Delete', '', array('class'=>'btn btn-default btn-sm btn-danger'));
                $links = '<a href="'.url()->route('redirect.edit', $query->id).'" class="btn btn-default btn-sm btn-success"><i class="entypo-pencil"></i>Edit</a>  '.$deleteLink;
            
                return $links;
            });

            return $datatables->make(true);

        }
        
        return view('backend.redirect.index')->with('redirect', $this->redirect->selectAll());
    }

    public function create()
    {
        $shops = $this->shop->selectAll()->pluck('title', 'id')->toArray();
        return view('backend.redirect.create')->with(array('shops' => $shops));
    }

    public function store()
    {
        $result  = $this->redirect->create(Request::all());
 

        if (isset($result->id)) {
            Notification::success('The redirect was created.');
            return redirect()->route('redirect.index');
        }

        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function edit($redirectId)
    {
                $shops = $this->shop->selectAll()->pluck('title', 'id');
        return view('backend.redirect.edit')->with(array(
            'redirect' => $this->redirect->find($redirectId),
            'shops' => $shops
        ));
    }

    public function getImport()
    {
        return view('backend.redirect.import')->with(array());
    }

    public function postImport()
    {

        $file = Request::file('file');
        Excel::load($file, function ($reader) {

              $results = $reader->get();

            if ($results->count()) {
                $result = $this->redirect->importCsv($results, auth('hideyobackend')->user()->selected_shop_id);

                Notification::success('The redirects are imported.');
       
                return redirect()->route('redirect.index');
            } else {
                Notification::success('The redirects imported are failed.');
                return redirect()->route('redirect.import');
            }
        });
    }

    public function getExport()
    {
        $result  =  $this->redirect->selectAll()->get();

        Excel::create('redirects', function ($excel) use ($result) {

            $excel->sheet('Redirects', function ($sheet) use ($result) {
                $newArray = array();
                foreach ($result as $row) {
                    $newArray[$row->id] = array(
                        'active' => $row->active,
                        'url' => $row->url,
                        'redirect_url' => $row->redirect_url
                    );
                }

                $sheet->fromArray($newArray);
            });
        })->download('xls');
    }

    public function update($redirectId)
    {
        $result  = $this->redirect->updateById(Request::all(), $redirectId);

        if (isset($result->id)) {
            Notification::success('The redirect was updated.');
            return redirect()->route('redirect.index');
        }

        foreach ($result->errors()->all() as $error) {
            Notification::error($error);
        }
        
        return redirect()->back()->withInput();
    }

    public function destroy($redirectId)
    {
        $result  = $this->redirect->destroy($redirectId);

        if ($result) {
            Notification::success('Redirect item is deleted.');
            return redirect()->route('redirect.index');
        }
    }
}
