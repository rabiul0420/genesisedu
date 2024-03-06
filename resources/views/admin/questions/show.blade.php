@extends('admin.layouts.app')

@section('content')

    <div id="main" role="main">

        <div id="content">

            @if(Session::has('message'))
                <div class="allert-message alert-success-message pgray  alert-lg" role="alert">
                    <p class=""> {{ Session::get('message') }}</p>
                </div>
        @endif


        <!-- widget grid -->
            <section id="widget-grid" class="">



                <article class="">
                    <div class="jarviswidget" id="wid-id-1" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
                        <header>

                        </header>

                        <div class="widget-body no-padding">

                            <div class="row">
                                <div class="portlet">
                                    <div class="portlet-body">
                                        <div id="question">
                                            @if(isset($question->question_title))

                                                <div>
                                                    <h4 class='modal-title' id='myModalLabel'>{!! $question->question_title !!}</h4>
                                                </div>

                                                <table class="table table-borderless" style="table-layout: auto;">
                                                    @if($question->type == "1")
                                                        @foreach($question->question_answers as $k=>$answer)

                                                            <tr>
                                                                <td>
                                                                    {!! isset($answer->answer)? $answer->answer:'' !!}
                                                                </td>

                                                                <td style="width: 99px;">
                                                                    <label class='radio-inline'><input type='radio' name="{{ $answer->sl_no }}" value='T' {{ ( $answer->correct_ans == 'T' ) ? "checked":'' }} > T </label>
                                                                    <label class='radio-inline'><input type='radio' name="{{ $answer->sl_no }}" value='F' {{ ( $answer->correct_ans == 'F' ) ? "checked":'' }} > F </label>
                                                                </td>

                                                            </tr>

                                                        @endforeach
                                                    @else
                                                        @foreach($question->question_answers as $k=>$answer)

                                                            <tr>
                                                                <td>
                                                                    {!! isset($answer->answer)? $answer->answer:'' !!}
                                                                </td>
                                                            </tr>

                                                        @endforeach
                                                        <tr>
                                                            <td>
                                                                <label class='radio-inline'><input type='radio' name='ans_sba' value='A' {{ ( $question->question_answers[0]->correct_ans == 'A' ) ? "checked":'' }} > A </label>
                                                                <label class='radio-inline'><input type='radio' name='ans_sba' value='B' {{ ( $question->question_answers[0]->correct_ans == 'B' ) ? "checked":'' }} > B </label>
                                                                <label class='radio-inline'><input type='radio' name='ans_sba' value='C' {{ ( $question->question_answers[0]->correct_ans == 'C' ) ? "checked":'' }} > C </label>
                                                                <label class='radio-inline'><input type='radio' name='ans_sba' value='D' {{ ( $question->question_answers[0]->correct_ans == 'D' ) ? "checked":'' }} > D </label>
                                                                <label class='radio-inline'><input type='radio' name='ans_sba' value='E' {{ ( $question->question_answers[0]->correct_ans == 'E' ) ? "checked":'' }} > E </label>

                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            @endif


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>



            </section>



        </div>


    </div>
@endsection













