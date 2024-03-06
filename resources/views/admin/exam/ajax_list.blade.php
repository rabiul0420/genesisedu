<div>
    <input type="checkbox" id="question_id_{{$mcq_list->id}}" name="question_id" value="{{ $mcq_list->id }}" {{ $checked }}>
    <label id="label_{{$mcq_list->id}}" for="question_id_{{$mcq_list->id}}">{{ $question_info }}</label>
</div>
