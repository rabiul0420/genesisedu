@extends('admin.layouts.app')

@section('content')
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
                        <i class="fa fa-globe"></i>Setting
                    </div>
                </div>
                <div class="portlet-body">

                    {!! Form::open(['action' => ['Admin\SettingController@store'], 'method' => 'POST', 'files' => true, 'class' => 'form-horizontal']) !!}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Company Name</label>

                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <input type="text" name="value[company_name]" value="{{ $company_name->value ?? '' }}"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Year From</label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="text" name="value[year_from]" value="{{ $year_from->value ?? '' }}" class="form-control" required>
                                </div>
                            </div>
                            <label class="col-md-1 control-label">Year To</label>
                            <div class="col-md-2">
                                <div class="input-icon right">
                                    <input type="text" name="value[year_to]" value="{{ $year_to->value ?? '' }}" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">
                                Debugger
                            </label>

                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <label class="radio-inline">
                                        <input type="radio" name="value[debugger]" value="true"
                                            @if ($debugger) {{ $debugger->value === 'true' ? 'checked' : '' }} @endif
                                            required>
                                        True
                                    </label>
                                    <label class='radio-inline'>
                                        <input type="radio" name="value[debugger]" value="false"
                                            @if ($debugger) {{ $debugger->value === 'false' ? 'checked' : '' }} @endif
                                            required>
                                        False
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">
                                Select Executive Role
                            </label>

                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <select name="value[executive_role_id]" id="" class="form-control">
                                        @foreach ($roles as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ !empty($key) && ($executive_role_id->value ?? '') == $key ? 'selected' : ' ' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Terms & Conditions</label>

                            <div class="col-md-8">
                                <div class="input-icon right">
                                    <textarea name="value[terms_conditions]" class="form-control">{{ $terms_conditions->value ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Refund Policy</label>

                            <div class="col-md-8">
                                <div class="input-icon right">
                                    <textarea name="value[refund_policy]" class="form-control">{{ $refund_policy->value ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>


                        @if (Auth::user()->id == 48)
                            <div class="form-group">
                                <label class="col-md-2 control-label">Question Print Allow</label>
                                <div class="col-md-8">
                                    <select class="js-example-basic-multiple form-control"
                                        name="value[question_print_allow][]" multiple="multiple">
                                        <option value="2">Alabama</option>
                                        @foreach ($users as $key => $user)
                                            <option value="{{ $key }}"
                                                {{ in_array($key, json_decode($question_print_allow->value)) ? 'selected' : ' ' }}>
                                                {{ $user }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Question Print SMS Send</label>

                                <div class="col-md-8">
                                    <div style="border: 1px dashed #ccc; border-radius: 10px; padding: 8px;">
                                        <div style="display: flex; flex-direction: row; gap: 8px;"
                                            id="reference_slot_container">
                                            <div></div>
                                            @foreach (json_decode($question_print_sms->value) as $number)
                                                <div style="display: flex">
                                                    <input type="number" name="value[question_print_sms_to][]"
                                                        value="{{ $number }}" class="form-control"
                                                        placeholder="Page"
                                                        style="flex-shrink: 0; flex-grow: 0; width: 150px;">
                                                    <input type="button" onclick="removeReferenceSlot(this.parentElement)"
                                                        class="btn btn-danger btn-sm" value="X"
                                                        style="flex-shrink: 0; flex-grow: 0; width: 40px;">
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="text-center" style="margin-top: 8px;">
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="addNewReferenceSlot()">+ Add Number</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-md-2 control-label">Dashboard Modal Image</label>

                            <div class="col-md-5">
                                <div class="input-icon right">
                                    <input type="text" name="value[dashboard_modal_image]" value="{{ $dashboard_modal_image->value ?? '' }}"
                                        class="form-control">
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/sitesetup') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!

        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
        CKEDITOR.replace('value[terms_conditions]');
        CKEDITOR.replace('value[refund_policy]');
        CKEDITOR.replace('value[system_driven]');
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                "columnDefs": [{
                        "searchable": false,
                        "targets": 2
                    },
                    {
                        "orderable": false,
                        "targets": 2
                    }
                ]
            })
        })

        function addNewReferenceSlot() {
            let divElement = document.createElement("div");

            divElement.setAttribute("style", "display: flex; gap: 8px;")

            divElement.innerHTML = `
                <input type="number" name="value[question_print_sms_to][]" value="" class="form-control"
                    placeholder="Phone Number" style="flex-shrink: 0; flex-grow: 0; width: 150px;">
                <input type="button" onclick="removeReferenceSlot(this.parentElement)" class="btn btn-danger btn-sm" value="X"
                    style="flex-shrink: 0; flex-grow: 0; width: 40px;">
                `;

            return document.getElementById('reference_slot_container').appendChild(divElement);
        }

        function removeReferenceSlot(slotDivElement) {
            return slotDivElement.parentElement.removeChild(slotDivElement);
        }
    </script>
@endsection
