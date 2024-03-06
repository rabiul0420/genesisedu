@foreach($lecture_sheets as $lecture_sheet)
    <div class="row">
        <div class="col-md-4">
            <h2><a href="{{ url( 'lecture-details/'.$lecture_sheet->id ) }}">{{ $lecture_sheet->title }}</a></h2>
                <p>{{ substr($lecture_sheet->description,0,100) }} <a href="{{ url( 'lecture-details/'.$lecture_sheet->id ) }}">Continue Reading...</a></p>
        </div>
        <!-- <div class="col-md-4">
            <h2><a href="{{ url('lecture-detail/3') }}">This For Heading</a></h2>
            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that </p>
        </div>
        <div class="col-md-4">
            <h2><a href="{{ url('lecture-detail/3') }}">This For Heading</a></h2>
            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that </p>
        </div> -->
    </div>
@endforeach