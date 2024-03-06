
<a href="{{ url('admin/doctors-courses-untrash/'.$doctors_courses_list->id) }}" class="btn btn-xs btn-primary">Back to the list</a>


<div class='modal fade' id='myModal_{{$doctors_courses_list->id}}' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
    <div class='modal-dialog' role='document'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h4 class='modal-title' id='myModalLabel'>Payment</h4>
            </div>
            <div class='modal-body'>
            	<table class="table table-striped table-bordered table-hover datatable">
                    <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Payment Date</th>
                        <th>Paid Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    
	                   <?php 
		            		$payments = \App\DoctorCoursePayment::select('*')->where('doctor_course_id', $doctors_courses_list->id)->get();
			                foreach ($payments as $key => $payment){
			                    echo "<tr>";
			                    echo "<td>".++$key."</td>";
			                    echo "<td>".substr($payment->created_at, 0, 10)."</td>";
			                    echo "<td>".$payment->amount."</td>";
			                    echo "</tr>";
			                }
				            if (empty($payment->amount)){echo "<tr>"; echo "<td colspan=3>No Payment</td>"; echo "</tr>";}
			            ?>
				    
                    </tbody>
                </table>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-sm bg-red' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
</div>

