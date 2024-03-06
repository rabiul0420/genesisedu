@php $installments = $doctor_course->installments(); @endphp
@if(isset($installments) && count($installments))
    <style>
        .custom
        {
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
            --input-padding-x: 0.75rem;
            --input-padding-y: 0.75rem;
            font-family: 'Open Sans', sans-serif;
            font-size: 13px;
            direction: ltr;
            color: #222222;
            text-align: center;
            line-height: 1.42857143;
            white-space: initial !important;
            cursor: pointer;
            box-sizing: border-box;
            border-spacing: 0;
            border-collapse: collapse;
            width: 100%;
            max-width: 100%;
            border: 2px solid #31b0d5;
            border-top: 2px solid #31b0d5 !important;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            background-color: #fff;

        }

        div.dataTables_scrollBody tbody tr:first-child th, div.dataTables_scrollBody tbody tr:first-child td
        {
            border-top: 1px solid #ddd !important;
        }
    </style>
    <table id="table_2" class="table custom">
    <tr>
        <th>Installment Last Date</th><th>Installment Amount</th><th>SMS</th>
    </tr>
    @foreach($installments as $k=>$installment)
    <tr  <?php echo $doctor_course->check_paid_installment($k)?'style="background-color:green;color:white;"':'style="background-color:red;color:white;"'?> id="{{ $doctor_course->id }}">
        <td >{{ $installment->payment_date }}</td><td>{{ $doctor_course->installment_gap($k-1,$k) }}</td>
        <td>
            @if($doctor_course->check_paid_installment($k) == false && $doctor_course->next_installment_last_date() == $installment->payment_date )
                <span id="{{ $doctor_course->id }}" class="btn btn-xs btn-primary send-sms">Send</span>
            @endif
        </td>
    </tr>
    @endforeach
    </table>
@endif
