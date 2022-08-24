<?php

namespace SET\Http\Controllers;

use SET\Http\Controllers\Controller;
use SET\Http\Requests\InventoryRequest;
use SET\Http\Requests\StoreUpdateInventoryRequest;
use SET\Inventory;
use SET\InventoryClassification;
use SET\InventoryDrawer;
use SET\InventoryReceiveMethod;
use SET\InventoryRoom;
use SET\InventorySafe;
use SET\InventoryType;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InventoryRequest $request)
    {
        $inventoryList = Inventory::withTrashed()->get();

        return view('inventory.index', compact('inventoryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(InventoryRequest $request)
    {
        $received_methods = InventoryReceiveMethod::all()->pluck('name', 'id')->toArray();
        $classificationTypes = InventoryClassification::all()->pluck('name', 'id')->toArray();
        $types = InventoryType::all()->pluck('name', 'id')->toArray();
        $rooms = InventoryRoom::all()->pluck('name', 'id')->toArray();
        $safes = InventorySafe::all()->pluck('name', 'id')->toArray();
        $drawers = InventoryDrawer::all()->pluck('name', 'id')->toArray();

        return view('inventory.create', compact('received_methods','classificationTypes',
            'types', 'rooms', 'safes', 'drawers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateInventoryRequest $request)
    {
        Inventory::create($request->all());

        return redirect()->action('InventoryController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->action('InventoryController@index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InventoryRequest $request, $id)
    {
        $inventoryItem = Inventory::withTrashed()->find($id);
        $received_methods = InventoryReceiveMethod::all()->pluck('name', 'id')->toArray();
        $classificationTypes = InventoryClassification::all()->pluck('name', 'id')->toArray();
        $types = InventoryType::all()->pluck('name', 'id')->toArray();
        $rooms = InventoryRoom::all()->pluck('name', 'id')->toArray();
        $safes = InventorySafe::all()->pluck('name', 'id')->toArray();
        $drawers = InventoryDrawer::all()->pluck('name', 'id')->toArray();

        return view('inventory.edit', compact('inventoryItem', 'received_methods',
            'classificationTypes', 'types', 'rooms', 'safes', 'drawers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateInventoryRequest $request, $id)
    {
        $item = Inventory::withTrashed()->find($id);
        if($item->trashed()) redirect()->action('InventoryController@index');
        $item->update($request->all());
        return redirect()->action('InventoryController@edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryRequest $request, $id)
    {
        $itemToDestory = Inventory::withTrashed()->find($id);;
        if(!$itemToDestory->trashed()) {
            $itemToDestory->delete();
        }
        return redirect()->action('InventoryController@index');
    }
}