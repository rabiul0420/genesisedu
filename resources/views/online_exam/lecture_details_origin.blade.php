<span class="btn btn-sm btn-primary" data-toggle='modal' data-target='#myModal_{{$link->id}}'>{{ $link->name }}</span>
<div class='modal fade' id='myModal_{{$link->id}}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
    <div class='modal-dialog' role='document' style='width: 100%;'>
        <div class='modal-content'>

            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>{{ $link->name }}</h4>

                @if($link->password)

                    <h4><b>Video Password:</b> {{ $link->password }}</h4>
                @endif
            </div>
            <div class='modal-body'>
                <div class="col-md-6">
                    @if($browser == 'UCBrowser')
                        <p>Sorry this video does not support UC browser. Please use another browser.</p>
                    @else
                        <iframe width='100%' height='400' src='{{$link->lecture_address}}' frameborder='0' allow='accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>
                    @endif
                </div>
                <div class="col-md-6">
                    <iframe width='100%' height='500' src="pdf/{{$link->pdf_file}}"></iframe>
                </div>

            </div>

            <div class='modal-footer'>
                <button type='button' class='btn btn-sm bg-red' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

