@extends('layouts.master')

@section('title', "Training Type Directory")

@section('content')
    <div class="row">
        <div class="col s12 m8">
            <div class="card">
                <div class="card-content">
                    <span class="card-title">
                        {{ $trainingtype->name }}
                        @can('edit')
                            <a href="{{ url("/trainingtype/$trainingtype->id/edit") }}"
                                class="btn-flat btn-sm"><i class="material-icons">mode_edit</i></a>
                        @endcan
                    </span>
                    <div class="input-field col s12" id="trainingtype_status">
                        Status: {{ $trainingtype->status>0?'Active':'Inactive' }}
                    </div>
                    <div class="input-field col s12" id="trainingtype_description">
                        Description: {{ $trainingtype->description }}
                    </div>
                    <div class="divider col s12">
                        <hr/>
                    </div>
                    <table class="row-border hover data-table">
                        <thead><th>Associated Trainings</th></thead>
                        <tbody>
                            @if ($trainings->count()>0)
                              @foreach($trainings as $training)
                                  <tr class="tr{{$training->id}}">
                                      <td class="training_name">
                                          <a href="{{ url('/training', $training->id) }}">{{ $training->name }}</a>
                                      </td>
                                  </tr>
                              @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop

@section('help')
    <strong>Status</strong>
    <p>Status indicates whether the Training Type is active or inactive.  The Training Type must be active to associate a Training to it.</p>

    <strong>Trainings</strong>
    <p>This table displays the trainings associated with this type.</p>
    <p>Click a Training to view the training's details.</p>
@stop
