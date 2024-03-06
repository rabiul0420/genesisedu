@php $payments = $doctor_course->payments(); @endphp
@if(isset($payments) && count($payments))
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
        <th>Payment Date</th><th>Paid Amount</th><th>Varify</th>
    </tr>
    @foreach($payments as $k=>$payment)
    <tr>
        <td width="200px">{{ date('Y-m-d ( h:i:s a )',$payment->created_at->timestamp) }}</td><td>{{ $payment->amount }}</td><td>{{'Bismillah'}}</td>
    </tr>
    @endforeach
    </table>
@endif
