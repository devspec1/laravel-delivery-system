<title>Invoice</title>
@extends('template_without_header_footer')
@section('main')
<style>

    h1{
        padding-left    :   50px;
        font-size       :   30px;
        text-align: center;
        font-weight     :   bold;
    }
    table{
        border          :   1px solid black;
        border-collapse :   collapse;
        width           :   750px;
        font-size       :   16px;
    }
    tr, td{
        border          :   1px solid black;
        padding-left    :   15px;
    }
    th{
        padding-left    :   15px;
    }
    tr{
        border-collapse :   collapse;
    }
    div{
        padding-top     :   20px;
    }
    div{
        padding-top     :   25px;
    }
    img {
        border: 1px solid #c2c2c2;
        border-radius: 470% !important;
        object-fit: cover;
        height: 150px;
        width: 150px;
    }
    p{
        line-height: 15px;
    }
    .no-border,.no-border tr,.no-border td {
        border: none;
    }
    .width-60{
        width: 40%;
    }
    .width-40{
        width: 60%;
    }
    * {
        font-family: DejaVu Sans, sans-serif;
    }
</style>
<div style="padding-top: 10px;">
    <div ><h1>@lang('messages.dashboard.trip_invoice')</h1>
    </div>
    <div >
        <table >
            <thead >
                <tr>
                    <th>@lang('messages.dashboard.invoice_no')</th>
                    <th >@lang('messages.dashboard.trip_date')</th>
                    <th>@lang('messages.dashboard.invoice')</th>
                </tr>
            </thead>
            <tbody>
                <tr  >
                    <td data-title="Invoice Number">{{ $trip->id }}</td>
                    <td data-title="Trip date">{{ date('F d, Y',strtotime($trip->created_at))}}</td>
                    <td data-title="Invoice">{{ $trip->currency->original_symbol }}  {{ $trip->company_driver_earnings }}</td>
                </tr>
            </tbody>
        </table>
        <table class="no-border">
            <tr>
                <td class="width-60">
                    <div class="col-sm-6">
                        <img src="{{ $trip->rider_profile_picture }}" >
                    </div>
                </td>
                <td class="width-40">
                    <div class="col-sm-6">
                        <p>@lang('messages.dashboard.invoice_issued') {{ $site_name }} @lang('messages.dashboard.behalf')</p>
                        <p>{{ $trip->rider_name }}<br>
                            <p class="flush">{{ $trip->pickup_time }}</p>
                            <h6 class="color--neutral flush">{{ $trip->pickup_location }}</h6><br>
                            <p class="flush">{{ $trip->drop_time }}</p>
                            <h6 class="color--neutral flush">{{ $trip->drop_location }}</h6><br>
                        </p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <table>
            <thead class="cf">
                <tr>
                    <th> @lang('messages.dashboard.date') </th>
                    <th> @lang('messages.dashboard.desc') </th>
                    <th> @lang('messages.dashboard.net_amt') </th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice_data as $key => $invoice)
                    <tr class="trip-expand__origin collapsed text-{{ $invoice['colour'] }}">
                        <td> {{ ($key == 0) ? date('F d, Y') : ''}} </td>
                        <td class="text--left "> {{ $invoice['key'] }} </td>
                        <td class="text--right "> {{ $invoice['value'] }} </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
</div>
</main>
<style type="text/css">
</style>
@stop