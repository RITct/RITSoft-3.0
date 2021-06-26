@extends("layouts.layout")

@section("head")
    <script>
        function sendPatch(id) {
            fetch(`/requests/${id}/`, {
                "method": "PATCH",
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                "body": JSON.stringify({"state": "approved"})
            }).then((r) => {
                if (r.ok)
                    location.reload();
                else
                    alert("Failed");
            });
        }

    </script>
@endsection

@section("content")
    @foreach($requests as $request)
        <h2>{{ $request->type }}</h2>
        <h3>{{ $request->primary_value }}</h3>
        <p>{{ $request->payload }}</p>
        @if( app("request")->input("mode") != "applicant")
            <button onclick="sendPatch({{ $request->id }})">Approve</button>
        @endif
    @endforeach
@endsection
