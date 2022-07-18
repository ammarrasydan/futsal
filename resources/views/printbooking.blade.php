<!DOCTPE html>
<html>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
table {
  border: 5px solid #000000;
  border-spacing: 70px;
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 85%;
}
td, th {
  border: 0px solid #dddddd;
  text-align: left;
  padding: 20px;
}
th {
  font-size: 30px;
}
@media print {
    #printbtn {
        display :  none;
    }
}
.button {
  background-color: #4CAF50; /* Green */
  border: none;
  color: white;
  padding: 16px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 20px;
  margin: 4px 2px;
  transition-duration: 0.4s;
  cursor: pointer;
}
.button1 {
  background-color: #007bff; 
  color: white; 
}
.button1:hover {
  background-color: #0C67C6;
}
.button1 {border-radius: 8px;}
</style>

<title>CRM</title>
</head>
<body>
<br><br><br><table border = "1">
@foreach ($bookings as $booking)
<tr>
<th><b>TAX INVOICE</b></th>
<td style="text-align: right;">INVOICE NO : <b>{{ $booking->id }}</b><br><br>DATE : <b>{{ $booking->date }}</b></td>
</tr><tr>
<td>RECEIVE FROM M/S : <b>{{ $booking->fullname }}</b></td>
<td></td>
</tr><tr>
<td>THE SUM OF RINGGIT MALAYSIA : <b>RM {{ $booking->amount }}</b></td>
<td></td>
</tr><tr>
<td>IN PAYMENT FOR COURT RETAIL : <b>{{ $booking->court }}</b> <b>: Duration {{ $booking->duration }} Mins</b></td>
<td></td>
</tr></tr>
<td><b>***GST @ INCLUDED IN TOTAL. GST = RM0.00. AMOUNT BEFORE GST = RM {{ $booking->amount }}</b></td>
<td></td>
</tr>

@endforeach

</table><br>
<div class="form-group">
<button id="printbtn" style="vertical-align:middle" class="button button1" onclick="window.print()" class="glyphicon glyphicon-print" aria-hidden="true"><span> Print </span><i class="fa fa-print"></i></button>
</div>
</body>
</html>
