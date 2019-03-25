<?php

namespace SET\Http\Controllers;

use SET\Http\Requests\StoreTrainingTypeRequest;
use SET\TrainingType;

class TrainingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view');
        $trainingtypes = TrainingType::orderBy('name')->get();

        return view('trainingtype.index', compact('trainingtypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('edit');

        return view('trainingtype.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTrainingTypeRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTrainingTypeRequest $request)
    {
        $trainingtype = TrainingType::create($request->all());

        return redirect()->action('TrainingTypeController@index')->with('status', 'Training Type Created');
    }

    /**
     * Show the individual training type record.
     *
     * @param $trainingtypeId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($trainingtypeId)
    {
        $this->authorize('view');
        $trainingtype = TrainingType::find($trainingtypeId);
        $trainings = $trainingtype->trainings;

        return view('trainingtype.show', compact('trainingtype', 'trainings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(TrainingType $trainingtype)
    {
        $this->authorize('edit');

        return view('trainingtype.edit', compact('trainingtype'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreTrainingTypeRequest $request, TrainingType $trainingtype)
    {
        $this->authorize('edit');
        $data = $request->all();
        $trainingtype->update($data);

        return redirect()->action('TrainingTypeController@show', $trainingtype->id)->with('status', 'Training Type Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('edit');
        $trainingtype = TrainingType::findOrFail($id);
        $trainingtype->trainings()->update(['training_type_id' => null]);
        $trainingtype->delete();
    }
}
