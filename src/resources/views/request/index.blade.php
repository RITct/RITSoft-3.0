@extends("layouts.layout")

@section("head")
    <script>
        function sendPatch(id, state) {
            let remark = document.getElementsByName("remark")[0].value;
            let url = `/requests/${id}/`;
            fetch(url, {
                "method": "PATCH",
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                "body": JSON.stringify({"state": state, "remark": remark})
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
        @if( app("request")->input("mode") != "applicant" && $request->state == \App\Enums\RequestStates::PENDING)
            <input type="text" name="remark" placeholder="Remarks">
            <button onclick="sendPatch({{ $request->id }}, '{{ \App\Enums\RequestStates::APPROVED }}')">Approve</button>
            <button onclick="sendPatch({{ $request->id }}, '{{ \App\Enums\RequestStates::REJECTED }}')">Reject</button>
        @else
            Status: {{ $request->state }}<br/>
            <p>Remarks</p>
            <ul>
                @foreach($request->getRemarks() as $remark)
                    <li>{{ $remark }}</li>
                @endforeach
            </ul>
        @endif
    @endforeach
@endsection
