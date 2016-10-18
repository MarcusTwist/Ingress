@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">Here is your {{ strtolower($version) }} linkplan!</div>
                <div class="panel-body">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <th><h3 style="text-align:center;">From</h3></th>
                            <th><h3 style="text-align:center;">To</h3></th>
                        </thead>
                    <tbody>
                        <td>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Anker</th>
                                    <th>Portal name</th>
                                </thead>
                            @foreach($links as $number => $link)
                               <tr>
                                    <td> {{ $number+1 }}</td>
                                    <td>{{ $link['from']['anker'] }} </td>
                                    <td>{{ $link['from']['name'] }} </td>
                               </tr>
                            @endforeach
                          </table>
                        </td>
                        <td>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <th>Anker</th>
                                    <th>Portal name</th>
                                </thead>
                            @foreach($links as $link)
                               <tr>
                                  <td>{{ $link['to']['anker'] }} </td>
                                  <td>{{ $link['to']['name'] }} </td>
                               </tr>
                            @endforeach
                          </table>
                        </td>
                          </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Here are the Drawtools for you!</div>
            <div class="panel-body">
                <div class="form-group">
                    <textarea class="form-control" rows="10" id="json">{{$drawtools}}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">Here are the bookmarks you have given!</div>
            <div class="panel-body">
                <div class="form-group">
                    <textarea class="form-control" rows="10" id="json">{{$bookmarks}}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Portal overview!</div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                                <thead>
                                    <th>#</th>
                                    <th>Anker</th>
                                    <th>Portal name</th>
                                    <th>Intel link</th>
                                    <th>Google maps</th>
                                </thead>

                            @foreach($intel as $number => $portal)
                               <tr>
                                    <td>{{ $number+1 }}</td>
                                    <td>{{ $portal['anker'] }} </td>
                                    <td>{{ $portal['name'] }} </td>
                                    <td><a href="{{ $portal['intel'] }}">link to Intel</a></td>
                                    <td><a href="{{ $portal['maps'] }}">link to Intel</a></td>
                            </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>


</div>
@endsection
