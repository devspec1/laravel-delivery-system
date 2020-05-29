@extends('emails.template')
@section('emails.main')
<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
@if(isset($first_name))
<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
<p>Hi {{ $first_name }},</p>
</div>
@endif
</div>
<div style="margin:0;padding:0;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif;margin-top:1em">
    <p>Your funds related to delivery order #{{ $order_number }} were withheld.</p>
    <p>Order date: {{ $order_date }}.</p>
    <p>Order pick up address: {{ $pick_up }}.</p> 
    <p>Order drop off address: {{ $drop_off }}.</p> 
    <p>Order fee: {{ $fee }}.</p>
    <p><strong>Reason: {{ $reason }}</strong>.</p>
</div>
@stop