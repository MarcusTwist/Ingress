@extends('layouts.app')

@section('main')
<div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">Here is are some json's!</div>
                <div class="panel-body">
                    <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Here are the Drawtools for you!</div>


                <div class="panel-body">
                        <div class="form-group">
                          <textarea class="form-control" rows="10" id="json">{{$json}}</textarea>
                        </div>
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
@endsection
