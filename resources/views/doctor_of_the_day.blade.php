
<title>Doctor of the day</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link rel = "icon" href ="http://localhost:8000/images/logo.png" type = "image/x-icon"


<h1> Doctor of The Day</h1> <br>
<div class="photo">
<table class="table table-striped">
        <tr>
            <th>ID</th>
            <th>Dr Name</th>
            <th>Batch</th>
            <th>Image</th>
            <th>Exam</th>
            <th>Position</th>
            <th>Totoal Mark</th>
            <th>Negative Mark</th>
        </tr>

 @foreach($data as $dt)

        <tr>
            <td>{{$dt->id}}</td>
            <td>{{$dt->name}}</td>
            <td>{{$dt->batch}}</td>
            <td>{{$dt->image}}</td>
            <td>{{$dt->exam}}</td>
            <td>{{$dt->position}}</td>
            <td>{{$dt->total_mark}}</td>
            <td>{{$dt->negative_mark}}</td>
        </tr>
@endforeach
       
</table>

</div>


