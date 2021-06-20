@extends("layouts.layout")

@section("head")
    <script>
        function deleteFaculty(facultyID) {
            fetch(`/faculty/${facultyID}`,{
                "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                "method": "DELETE",
            }).then((r) => {
                if (r.ok)
                    alert("OK");
                else
                    alert("Failed");
            });
        }
    </script>
@endsection
