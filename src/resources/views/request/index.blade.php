@extends("layouts.layout")

@section("head")
    <script>
        function sendPatch(id) {
           fetch(`/requests/${id}/`, {
               "method": "PATCH",
               "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
               "body": JSON.stringify({"state": "approved"})
           }).then((r) => console.log(r));
        }
    </script>
@endsection

@section("content")
    @foreach($requests as $request)
        <h2>{{ $request->id }}</h2>
        <button onsubmit="sendPatch({{ $request->id }})">Approve</button>
    @endforeach
@endsection
