@extends("layouts.layout")

@section("head")
    <script>
        function getValue(inputElement) {
            switch (inputElement.getAttribute("type")) {
                case "radio":
                    return (inputElement.checked) ? inputElement.value : null;
                case "text":
                    return inputElement.value;
            }
        }

        function submitForm() {
            let allData = document.getElementsByClassName("input");
            let feedbackData = {};
            for (let i=0; i<allData.length; i++) {
                let [facultyId, pos] = allData[i].getAttribute("name").split("-");
                if (!feedbackData[facultyId]) {
                    feedbackData[facultyId] = [];
                }
                // Handle null values from unchecked radio buttons
                const value = getValue(allData[i]);
                if (value)
                    feedbackData[facultyId][pos] = value;
            }
            console.log(feedbackData);

            fetch("{{ route("feedbacks.store", $courseId) }}",{
                method: "POST",
                body: JSON.stringify({"data": feedbackData}),
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
            });
        }
    </script>
@endsection

@section("content")

    @foreach($faculties as $faculty)
        <h2>{{ $faculty->name }}</h2>
        @foreach($format as $index => $question)
            <div class="feedback-question">
                <p>{{ $question["question"] }}</p>
                @if($question["type"] == \App\Enums\FeedbackQuestionType::TEXT)
                    <input type="text" placeholder="Answer" class="input" name="{{ $faculty->id }}-{{ $index }}">
                @elseif($question["type"] == \App\Enums\FeedbackQuestionType::BOOLEAN)
                    <p>YES <input type="radio" class="input" name="{{ $faculty->id }}-{{ $index }}" value="1"></p>
                    <p>NO <input type="radio" class="input" name="{{ $faculty->id }}-{{ $index }}" value="0"></p>
                @elseif($question["type"] == \App\Enums\FeedbackQuestionType::MCQ)
                    @foreach($question["options"] as $option)
                        <p>{{ $option["string"] }} <input class="input" type="radio" name="{{ $faculty->id }}-{{ $index }}" value="{{ $option["score"] }}"/></p>
                    @endforeach
                @endif
            </div>
        @endforeach
    @endforeach
    <button type="button" onclick="submitForm()">Submit</button>
@endsection
