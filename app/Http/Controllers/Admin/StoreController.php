<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\UploadTrait;
use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    use UploadTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \App\Store[]|\Illuminate\Database\Eloquent\Collection
     */

    public function __construct()
    {
       $this->middleware('user.has.store')->only(['create','store']);
    }


    public function index()
    {
        $store = auth()->user()->store;
            //\App\Store::paginate(5);


        return view('admin.stores.index',compact('store'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $users = \App\User::all(['id','name']);
        return view('admin.stores.create',compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $user = auth()->user();
        $data = $request->all();

        //$user = \App\User::find($data['user']);

        if($request->hasFile('logo')){
            $data['logo'] = $this->imageUpload($request->file('logo'));
        }
        $store = $user->store()->create($data);

        flash('Loja cadastrada com sucesso')->success();
        return redirect()->route('admin.stores.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $store = \App\Store::find($id);
        return view('admin.stores.edit',compact('store'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRequest $request, $id)
    {
        $data = $request->all();

        $store = \App\Store::find($id);

        if($request->hasFile('logo')){
            if(Storage::disk('public')->exists($store->logo)){
                Storage::disk('public')->delete($store->logo);
            }

            $data['logo'] = $this->imageUpload($request->file('logo'));
        }

        $store->update($data);

        flash('Loja atualizada com sucesso')->success();
        return redirect()->route('admin.stores.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $store=\App\Store::find($id);
        $store->delete();

        flash('Loja removida com sucesso')->success();
        return redirect()->route('admin.stores.index');
    }
}
