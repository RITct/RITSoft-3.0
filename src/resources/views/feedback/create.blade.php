@extends("layouts.layout")

@section("content")
    @foreach($format as $index => $question)
        <div class="feedback-question">
            <p>{{ $question["question"] }}</p>
            @if($question["type"] == \App\Enums\FeedbackQuestionType::TEXT)
                <input type="text" placeholder="Answer">
            @elseif($question["type"] == \App\Enums\FeedbackQuestionType::BOOLEAN)
                <p>YES <input type="radio" name="q-{{ $index }}"></p>
                <p>NO <input type="radio" name="q-{{ $index }}"></p>
            @elseif($question["type"] == \App\Enums\FeedbackQuestionType::MCQ)
                @foreach($question["options"] as $option)
                    <p>{{ $option["string"] }} <input type="radio" name="q-{{ $index }}" value="{{ $option["score"] }}"/></p>
                @endforeach
            @endif
        </div>
    @endforeach
@endsection
