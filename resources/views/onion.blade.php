@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Process to onion fields!</div>


                <div class="panel-body">
                    {!! Form::model(['Post' => 'OnionController@store']) !!}

                        <div class="form-group">
                            {!! Form::label('links', 'Links') !!}
                            {!! Form::text('links', '', ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('bookmarks', 'Bookmarks') !!}
                            {!! Form::text('bookmarks', '', ['class' => 'form-control']) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('version', 'Onion') !!}
                            {!! Form::radio('version', 'Onion', true) !!}
                            <br/>
                            {!! Form::label('version', 'Rose') !!}
                            {!! Form::radio('version', 'Rose') !!}
                        </div>                        

                        <button class="btn btn-success" type="submit">Make it!</button>

                      {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
