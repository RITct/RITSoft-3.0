@extends("layouts.layout")

@section("head")
    <script>
        function submitForm() {
            let fields = Array.prototype.slice.call(document.getElementsByClassName("data"));
            let data = {};
            fields.forEach((field) => data[field.name] = field.value);
            fetch({{ route("faculty.update", $faculty->id) }}, {
                "method": "PATCH",
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                "body": JSON.stringify(data)
            }).then((r) => {
                if(r.ok)
                    alert("OK");
                else
                    alert("FAILURE");
            });
        }
    </script>
@endsection

@section("content")
        KTU ID: <input type="text" disabled value="{{ $faculty->id }}"><br/>
        Name: <input class="data" type="text" placeholder="Name" name="name" value="{{ $faculty->name }}"><br/>
        Phone: <input class="data" type="text" placeholder="Phone" name="phone" value="{{ $faculty->phone }}"><br/>
        Email: <input class="data" type="text" placeholder="Email" name="email" value="{{ $faculty->user->email }}"><br/>
        <button onclick="submitForm()">Confirm Changes</button>
@endsection
