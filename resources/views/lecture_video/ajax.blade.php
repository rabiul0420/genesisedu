<div class="row mx-0">                             
    <div class="col p-0 pt-3">
        <table id="example" class="bg-white table text-center table-striped table-bordered rounded p-1 table-hover datatable">
            {{-- <thead>
                <tr>
                    <th>SL</th>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Video Title</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($lecture_video_batch as $k => $lecture_video)
                @if($loop->index < 0) @continue @endif
                @if(($loop->index) == 10) @break @endif
                <tr>
                    <td class="pt-3">{{ $k+1 }}</td>
                    <td class="pt-3">{{ date('d-m-Y',strtotime($lecture_video->created_time)) }}</td>
                    <td class="">
                        <a title="{{ $lecture_video->name }}" 
                            class="btn btn-info btn-sm text-white" 
                            href="{{ url( 'lecture-video-details/'.$lecture_video->id ) }}">
                            Play
                        </a>
                    </td>
                    <td class="pt-3 text-left">{{ $lecture_video->name }}</td>
                </tr>
                @endforeach
            </tbody> --}}
            
        </table>
    </div>
    
</div>

{{-- <div class="row mx-0">
    <div class="col-12 ">                                        
        <div class="text-center">
            <style>
                .pagination_box .pagination li {padding: 3px 5px; margin: 1px; border: 1px solid #707070;cursor: pointer; background: #fff !important;}
            </style>
            <div class="w-100 pagination_box pt-2 pb-4">
                @php $max = round($lecture_video_batch->count()/10) @endphp
                @for ($i = 0; $i < $max; $i++)
                <span class="border py-1 px-2" style="cursor: pointer" onclick="pagination({{ $i + 1 }})">
                    {{ $i + 1 }}
                </span>
                @endfor
            </div>
        </div>
    </div>
</div> --}}

<div class="row mx-0">
    <div class="col-12 ">                                        
        <div class="text-center">
            <style>
                .pagination_box .pagination li {padding: 3px 5px; margin: 1px; border: 1px solid #707070;cursor: pointer; background: #fff !important;}
            </style>
            <div class="w-100 pagination_box pt-2 pb-4">
                {{ $lecture_video_batch->links() }}
            </div>
        </div>
    </div>
</div>