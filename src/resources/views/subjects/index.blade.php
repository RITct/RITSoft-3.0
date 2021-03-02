<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Subject Management</h2>
        </div>

        <div class="pull-right">
            @can('subject-create')
                <a class="btn btn-success" href="{{ route('subjects.create') }}"> Create New Subject</a>
            @endcan
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th width="280px">Action</th>
    </tr>

    @foreach ($data as $key => $subject)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $subject->name }}</td>
            <td>
                <a class="btn btn-info" href="{{ route('subjects.show',$subject->id) }}">Show</a>

                @can('subject-delete')
                    {!! Form::open(['method' => 'DELETE','route' => ['subjects.destroy', $subject->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}
                @endcan
            </td>
        </tr>
    @endforeach

</table>

{!! $data->render() !!}
