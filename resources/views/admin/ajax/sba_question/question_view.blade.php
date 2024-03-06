<p>{!! $question->question_and_answers !!}</p><br>
<p><b>Dicussion:</b> {!! $question->discussion !!}</p>


<br>
<div>
    <h6>Old Reference:</h6>
    {!! $question->reference !!}
</div>
<br>

<h5>
    <b>References:</b>
</h5>
<ul>
@foreach ($question->reference_books as $reference_book)
    <li>
        <a target="_blank"
            href="{{ route('reference-book-detail', [$reference_book->reference_book_id, $reference_book->page_no]) }}"
            style="cursor: pointer">
            [Ref: {{ $reference_book->reference_book->name ?? '' }}/P-{{ $reference_book->page_no ?? '' }}]
        </a>
    </li>
@endforeach
</ul>