@extends('layouts.app')

<style>
table, td, th {  
  border: 3px solid #000000;
  text-align: left;
  background-color: #FDEBD0;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 15px;
}
</style>

<div class="container">
  <!-- Button to Open the Modal -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
    Open modal
  </button>

  <!-- The Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Detail Booking</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

		 <!-- Modal body -->
		 <div class="modal-body">
        <table>
		@foreach ($users as $users)
		<tr>
        <th>Id</th>
        <td>{{ $users->id }}</td>
</tr><tr>
        <th>Full Name</th>
        <td>{{ $users->fullname }}</td>
</tr><tr>
        <th>Phone Number</th>
        <td>{{ $users->phonecode }}{{ $users->phonenumber }}</td>
</tr><tr>
        <th>Date</th>
        <td>{{ $users->date }}</td>
</tr><tr>
        <th>Times</th>
        <td>{{ $users->time_start }}</td>
</tr><tr>
        <th>Duration</th>
        <td>{{ $users->duration }}</td>
</tr><tr>
        <th>Court</th>
        <td>{{ $users->court }}</td>
</tr><tr>
        <th>Status</th>
        <td>{{ $users->status }}</td>
</tr><tr>
        <th>Payment</th>
        <td>{{ $users->payment }}</td>
@endforeach
</thead>
</table>
</body>
        </div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
  
</div>

</body>
</html>
