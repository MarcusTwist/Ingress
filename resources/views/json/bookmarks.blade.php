@section('bookmarks')
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Here are the bookmarks you have given!</div>


                <div class="panel-body">
                        <div class="form-group">
                          <textarea class="form-control" rows="10" id="json">{{$json}}</textarea>
                        </div>
                </div>
            </div>
        </div>
@endsection