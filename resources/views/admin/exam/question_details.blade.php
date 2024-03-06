<div>
    <div><h4>{!! $question->question_title !!}</h4></div>
    <div><h4>Stemps :</h4></div>
    @foreach($question->question_answers as $question_answer)
    <div>
        {!! $question_answer->answer !!}
    </div>
    @endforeach

    <div class="modal-footer">
        <input type="checkbox" id="question_id_{{$question->id}}" name="question_id" value="{{ $question->id }}" style="display:none;">
        <label id="label_m_{{$question->id}}" for="question_id_{{$question->id}}" class="btn btn-info" style="border:10px;padding:5px;">Add to exam</label>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
    
</div>
