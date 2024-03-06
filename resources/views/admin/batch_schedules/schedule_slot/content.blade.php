@php
    $content = $content ?? new \App\ScheduleDetail( );
    $isChild = $isChild ?? false;
    $contentType = $content->type ?? 'Class';
    $childClassBtnTxt = '';
    $parentContentType = $parentContentType ?? 'Class';

    if( !$isChild ) {
        $childClassBtnTxt = ( $contentType ?? 'Class' ) == 'Class' ? 'Add Feedback Class':'Add Solve Class';
    }

    $namePrefix = $isChild ? "details[][contents][][children][___][]" : "details[][contents][]";

    $isNewContent = ( $isNewContent ?? false );

    $isInactiveContent =  !$isNewContent && $contentType == 'Class' ? empty( $content->video->lecture_address ) ?? '' : ( $content->exam->status ?? '' ) == 2;

    $editContentLink = $contentType == 'Class' ? url( 'admin/lecture-video/'.($content->video->id ?? '0').'/edit' ) ?? '0' : url( 'admin/exam/'.( $content->exam->id ?? '0' ) .'/edit' );

@endphp


@if( !$isChild )
<div class="content" style="margin-top: 5px; margin-bottom: 0px; padding: 0px; padding-top: 10px; padding-bottom: 10px; border-top: 1px dashed #b8ccb8">
    <div class="main row" style="{{  $isInactiveContent ? 'background-color: #ffb8b8':'box-shadow: inset 0 0 20px #ddd' }}; padding-top: 8px; padding-bottom: 5px ">
@else
    <div class="form-group child" style="{{   $isInactiveContent ? 'background-color: #ffb8b8':'box-shadow: inset 0 0 20px #f7bdf5cc' }}; padding-top: 5px; padding-bottom: 3px ">
@endif

        <div class="col-lg-2" style="margin-bottom: 5px">
            <div class="row">
                <div class="col-lg-3 control-label" style="text-align: left">{{ $isChild ? '':'Type' }}</div>
                <div class="col-sm-4 col-md-7 col-lg-9" style="text-align: center">
                    @if( $action == 'edit' )
                        <input type="hidden" value="{{$content->id ?? ''}}" class="detail_id"
                               name="{{ $isChild ? str_replace( '___', 'id', $namePrefix ) : $namePrefix . '[id]'}}">
                    @endif

                    @if( $isChild )
                        <label class="control-label">
                            <input type="hidden" value="Class" class="child-content-type">
                            <input type="hidden" value="{{ $parentContentType == 'Class' ? 3 : 2 }}" class="class-type child-class-type">
                            <span class="class_type_text">{{ $parentContentType == 'Class' ? 'Feedback':'Solve' }}</span> Class
                        </label>
                    @else
                        <input type="hidden" value="1" class="class-type">
                        <select data-index="0" data-slot-index="0" required=""
                                name="{{ $namePrefix.'[type]'}}"
                                class="form-control type-selection">
                            <option value="Class" {{ $contentType == 'Class' ? 'selected':'' }}>Class</option>
                            <option value="Exam" {{ $contentType == 'Exam' ? 'selected':'' }}>Exam</option>
                        </select>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8" style="margin-bottom: 5px">
            <div class="row">
                <label  style="text-align: left"
                        class="col-lg-1 control-label {{ !$isChild ? 'content-type-label':'' }}">{{$contentType}}</label>
                <div class="col-lg-6">

                    <div class="row">
                        <div class="col-sm-10">
                            <select required
                                    class="form-control class-or-exam child-class"
                                    id="exam_or_class_id-0-0"
                                    name="{{$isChild ? str_replace( '___', 'class_or_exam_id', $namePrefix ):  $namePrefix . '[class_or_exam_id]'}}">
                                <option value="">--select--</option>
                                <option
                                        value="{{ $contentType == 'Class' ? ($content->video->id ?? '') : ($content->exam->id ?? '') }}"
                                        selected>{{ $contentType == 'Class' ? ($content->video->name ?? '') : ($content->exam->name ?? '') }}</option>
                            </select>
                        </div>
                        <div class="col-sm-2" style="margin-top: 2px">
                            <a href="{{ $editContentLink }}"  target="_blank" class="btn btn-sm {{ !$isInactiveContent ? 'disabled btn-success':'btn-info ' }}">Edit</a>
                        </div>
                    </div>

                </div>
                <label class="col-lg-2 control-label">Mentor</label>
                <div class="col-lg-3">
                <select required="" class="form-control mentor-list"
                        name="{{$isChild ? str_replace( '___', 'mentor_id', $namePrefix ) :  $namePrefix . '[mentor_id]'}}">
                    <option value="">--select mentor--</option>

                    @if( isset($mentors) && $mentors instanceof \Illuminate\Support\Collection)
                        @foreach( $mentors as $mentor )
                            <option value="{{ $mentor->id }}" {{ ($content->mentor_id ?? '') == $mentor->id ? 'selected':'' }} >{{ $mentor->name  }}</option>
                        @endforeach
                    @endif

                </select>
            </div>
            </div>
        </div>

        <div class="col-lg-2" style="margin-bottom: 5px">
            <div class="row">
                <div class="col-lg-12 pull-right" style="display: flex; justify-content: right;">
            @if( !$isChild )
                <a href="" class="float-right btn btn-sm btn-primary add-child-content" style="margin-top: 2px;"><span class="child_class_add_btn_text">{{$childClassBtnTxt}}</a>
            @endif
            <a class="float-right btn btn-danger btn-sm {{ $isChild ? 'remove-child-content' : 'remove-content' }}" style="margin-left: 10px; margin-top: 2px">&times;</a>
        </div>
            </div>
        </div>

@if( !( $isChild ) )
    </div>
    <div class="children" style="padding-top: 10px; padding-bottom: 10px">
        @if( ($content->lectures ?? []) && $content->lectures instanceof \Illuminate\Support\Collection )
            @foreach(($content->lectures ?? []) as $childContent )

                @include(

                    'admin.batch_schedules.schedule_slot.content',

                    [
                        'content'           => $childContent,
                        'parentContentType' => $content->type,
                        'isChild'           => true,
                        'class_type'        => ($content->type ?? '') == 'Exam' ? 2:3
                    ]

                )

            @endforeach
        @endif
    </div>
</div>
@else
    </div>
@endif
