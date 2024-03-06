@extends('admin.layouts.app')

@section('content')
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls = '';
            foreach ($breadcrumb as $key => $value) {
                $urls .= $value . '/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="' . url('/') . substr_replace($urls, '', -1) . '"> ' . $value . ' </a></li>';
            }
            ?>
        </ul>

    </div>

    @if (Session::has('message'))
        <div class="alert {{ Session::get('class') ? Session::get('class') : 'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $module_name }} Create
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action' => ['Admin\QuestionsController@store'], 'files' => true, 'class' => 'form-horizontal']) !!}
                    <div class="form-body">

                        @if ($prev_create == 'no')
                            <div class="form-group">
                                <label class="col-md-3 control-label">Subject (<i class="fa fa-asterisk ipd-star"
                                        style="font-size:9px;"></i>)</label>
                                <div class="col-md-4">
                                    @php  $subjects->prepend('Select Subject', ''); @endphp
                                    {!! Form::select('subject_id', $subjects, old('subject_id'), ['class' => 'form-control', 'required' => 'required']) !!}<i></i>
                                </div>
                            </div>

                            <div class="chapter">

                            </div>

                            <div class="topic">

                            </div>
                        @else
                            <div class="subject">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Subject (<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>)</label>
                                    <div class="col-md-4">
                                        @php  $subjects->prepend('Select Subject', ''); @endphp
                                        {!! Form::select('subject_id', $subjects, session('subject_id'), ['class' => 'form-control', 'required' => 'required']) !!}<i></i>
                                    </div>
                                </div>
                            </div>

                            <div class="chapter">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Chapter (<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>)</label>
                                    <div class="col-md-4">
                                        @php  $chapters->prepend('Select Chapter', ''); @endphp
                                        {!! Form::select('chapter_id', $chapters, session('chapter_id'), ['class' => 'form-control', 'required' => 'required']) !!}<i></i>
                                    </div>
                                </div>
                            </div>

                            <div class="topic">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Topic (<i class="fa fa-asterisk ipd-star"
                                            style="font-size:9px;"></i>)</label>
                                    <div class="col-md-4">
                                        @php  $topics->prepend('Select Topic', ''); @endphp
                                        {!! Form::select('topic_id', $topics, session('topic_id'), ['class' => 'form-control', 'required' => 'required']) !!}<i></i>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <div class="form-group">
                            <label class="col-md-2 control-label">Question (<i class="fa fa-asterisk ipd-star"
                                    style="font-size:9px;"></i>)</label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea type="text" id="question" placeholder="01. Inhibition of Na+, K+-ATPase would result in increased  (MD, MS, Basic, Paedi March-2019, 18)
a) Intracellular K+ concentration
b) Intracellular Ca++ concentration
c) Intracellular Na+ concentration
d) Na+-glucose cotransport
e) Na+-Ca++ counter transport
FTTFF" name="question_title" required rows="9" class="form-control">{!! old('question_title') !!} </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Discussion</label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea id="discussion" name="discussion" class="form-control">{{ old('discussion') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Reference</label>
                            <div class="col-md-6">
                                <div style="border: 1px dashed #ccc; border-radius: 10px; padding: 8px;">
                                    <div style="display: flex; flex-direction: column; gap: 8px;"
                                        id="reference_slot_container">
                                    </div>
                                    <div class="text-center" style="margin-top: 8px;">
                                        <button type="button" class="btn btn-sm btn-success"
                                            onclick="addNewReferenceSlot()">+ Add Slot</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-2 control-label">Question Sources (*)</label>
                            <div class="col-md-6">
                                @php  $question_references->prepend('Select Sources', ''); @endphp
                                {!! Form::select('reference_id[]', $question_references, old('reference_id'), ['class' => 'form-control  select2 ', 'multiple' => 'multiple', 'id' => 'reference_id', 'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group" name="add_name" id="add_name">
                            <label class="col-md-2 control-label">Video Link</label>
                            <div class="col-md-6">
                                <div class="input-icon right" id="dynamic_field">
                                    <div id="row2">
                                        <div style="display:flex; flex-direction:column; margin-top:0">
                                            <input type="text" name="videos[0][link]" value=""
                                                class="form-control name_list" placeholder="Video">
                                            <input type="text" name="videos[0][password]" value="" class="form-control"
                                                placeholder="Password" style="margin-top:15px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-icon right d-flex">
                                    <button type="button" name="add" id="add" class="btn btn-success">Add Video
                                        Link</button>

                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="width: 100%; border: 1px dashed #ccc; border-radius: 10px; padding: 8px;">
                            <label class="col-md-2 control-label">Labels</label>
                            <div class="col-md-6">
                                <select class="form-control select2" multiple name="labels[]">
                                    @foreach ($labels as $label)
                                    <option value="{{ $label->id }}">{{ $label->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info" name="submit" value="submit">Submit</button>
                                <a href="{{ url('admin/question') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>









    <!-- END PAGE CONTENT-->
@endsection

@section('js')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">

        function addNewReferenceSlot() {
            let divElement = document.createElement("div");

            divElement.setAttribute("style", "display: flex; gap: 8px;")

            divElement.innerHTML = `
                <select type="text" name="reference_books[]"  class="form-control"
                    placeholder="Select Book" required style="flex-shrink: 1; flex-grow: 1;">
                    <option value="">-- Select Book Name --</option>
                    @foreach ($question_reference_books as $key => $question_reference_book)
                        <option value="{{ $key }}">{{ $question_reference_book }}
                        </option>
                    @endforeach
                </select>
                <input type="number" name="reference_pages[]" value="" class="form-control"
                    placeholder="Page" style="flex-shrink: 0; flex-grow: 0; width: 80px;">
                <input type="button" onclick="removeReferenceSlot(this.parentElement)" class="btn btn-danger btn-sm" value="X"
                    style="flex-shrink: 0; flex-grow: 0; width: 40px;">
                `;

            return document.getElementById('reference_slot_container').appendChild(divElement);
        }

        function removeReferenceSlot(slotDivElement) {
            return slotDivElement.parentElement.removeChild(slotDivElement);
        }

        $(document).ready(function() {

            CKEDITOR.replace('question');
            CKEDITOR.replace('discussion');
            CKEDITOR.replace('reference');

            $("body").on("change", "[name='subject_id']", function() {
                var subject_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/ajax-question-chapter',
                    dataType: 'HTML',
                    data: {
                        subject_id: subject_id
                    },
                    success: function(data) {
                        var data = JSON.parse(data);
                        $('.chapter').html('');
                        $('.topic').html('');
                        $('.chapter').html(data['chapters']);
                    }
                });
            })

            $("body").on("change", "[name='chapter_id']", function() {
                var subject_id = $("[name='subject_id']").val();
                var chapter_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/ajax-question-topic',
                    dataType: 'HTML',
                    data: {
                        subject_id: subject_id,
                        chapter_id: chapter_id
                    },
                    success: function(data) {
                        var data = JSON.parse(data);
                        $('.topic').html('');
                        $('.topic').html(data['topics']);
                    }
                });
            })

            $('.select2').select2({});
        })

        $(document).ready(function() {
            var i = 1;
            $('#add').click(function() {
                i++;
                $('#dynamic_field').append('<div id="row' + i + '"> ' +
                    '<div style="display:flex;flex-direction:column; margin-top:15px"> ' +
                    '<input type="text" name="videos[' + i +
                    '][link]" value="{{ old('video_link') }}" class="form-control p-2" placeholder="Video" required/>' +
                    '<input type="text" name="videos[' + i +
                    '][password]" value="" class="form-control p-2" placeholder="Password" style="margin-top:15px" required/>' +
                    '<button type="button" name="remove" id="' + i +
                    '" class="btn btn-danger btn_remove p-2" style="width:10px; margin-top:15px;display:flex; justify-content: center;" >X</button></div></div>'
                );
            });
            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
        });
    </script>
@endsection
