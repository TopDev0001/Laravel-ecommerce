<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\DeliveryFee;
use App\Models\Province;
use Auth;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use App\Classes\Common;

class DeliveryFeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

  	public function index()
    {        
        return view('admin.delivery_fee.index');
    }

    //*** JSON Request
    public function datatables()
    {
         $datas = DeliveryFee::orderBy('vendor_id')->get();
         //--- Integrating This Collection Into Datatables
         return Datatables::of($datas)
                ->addColumn('shop_name', function(DeliveryFee $data) {
                    $user = User::findOrFail($data->vendor_id);
                    return $user->shop_name;
                })
                ->addColumn('vendor_name', function(DeliveryFee $data) {
                    $user = User::findOrFail($data->vendor_id);
                    return $user->owner_name;
                })
				->addColumn('provinces', function(DeliveryFee $data) {
                    $province_ids = $data->province_ids;
                    $provinces = array();
                    if($province_ids) {
                        $province_ids = json_decode($province_ids);
                        $province = Province::whereIn('id', $province_ids)->get();
                        if($province){
                            foreach($province as $item) {
                                $provinces[] = $item->province_name;
                            }
                        }
                    }
                    return implode(',', $provinces);
                })
                ->editColumn('delivery_fee', function(DeliveryFee $data) {
                    return Common::formatPrice($data->delivery_fee);
                })
				->addColumn('action', function(DeliveryFee $data) {
					return '<div class="action-list"><a href="' . route('admin-vr-delivery_fee-edit',$data->id) . '"> <i class="fas fa-edit"></i>Edit</a><a href="javascript:;" data-href="' . route('admin-vr-delivery_fee-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
					
				}) 
				->rawColumns(['provinces','action'])
				->toJson(); //--- Returning Json Data To Client Side
    }

    public function edit($id) {
        $delivery_fee = DeliveryFee::findOrFail($id);
        $delivery_fees = DeliveryFee::where('id','<>',$id)->where('vendor_id','=',$delivery_fee->vendor_id)->get();
        $not_ids = array();
        foreach($delivery_fees as $item) {
            if($item->province_ids) {
                $not_ids = array_merge($not_ids, json_decode($item->province_ids));
            }
        }        
        if(sizeof($not_ids)>0) {
            $provinces = Province::whereNotIn('id', $not_ids)->get();    
        } else {
            $provinces = Province::all();
        }                  
        $sign = Currency::where('is_default','=',1)->first();
        $vendors = User::where('is_vendor','=',2)->orWhere('is_vendor','=',1)->orderBy('id','desc')->get();
        return view('admin.delivery_fee.edit', compact('sign', 'provinces', 'delivery_fee', 'vendors'));
    }

    public function delete($id) {
        $data = DeliveryFee::findOrFail($id);
        $data->delete();
        $msg = 'Data Deleted Successfully.';
        return response()->json($msg); 
    }


    public function create()
    {
        $sign = Currency::where('is_default','=',1)->first();
        $provinces = Province::all();
        $vendors = User::where('is_vendor','=',2)->orWhere('is_vendor','=',1)->orderBy('id','desc')->get();
        return view('admin.delivery_fee.create', compact('sign', 'provinces', 'vendors'));
    }


    public function update(Request $request){
        $id = $request->id;        

        if($id == 0){
            $delivery_fee = new DeliveryFee();            
        } else {
            $delivery_fee = DeliveryFee::findOrFail($id);
        }
        $delivery_fee->vendor_id = $request->vendor_id;
        $delivery_fee->class_name = $request->class_name;
        $delivery_fee->delivery_fee = $request->delivery_fee;
        $province_ids = $request->province_ids;
        if($province_ids) {
            $province_ids = explode(',', $province_ids);                
        } else {
            $province_ids = [];
        }
        foreach($province_ids as &$item) {
            $item = intval($item);
        }
        $province_ids = json_encode($province_ids);
        $delivery_fee->province_ids = $province_ids;
        if($request->description)
            $delivery_fee->description = $request->description;
        if($id == 0){
            $delivery_fee->save();
        } else {
            $delivery_fee->update();
        }
        return response()->json('Delivery Fee saved Successfully.');           
    }
}
