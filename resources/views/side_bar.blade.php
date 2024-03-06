
<span id="side_bar_btn">Dashboard</span>
<div id="side_bar_menu" class="side_bar_menu col-md-3 col-md-offset-0 mt-4">
	<div class="profile_header d-flex bg-white rounded shadow-sm align-items-center pl-2 pr-1">
		<div class="profile_box">
			<img
				style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; object-fit: cover;"
				src="https://genesisedu.info/{{ auth()->guard('doctor')->user()->photo ?? '' }}" alt="Photo"
			>
		</div>

		<div class="profile_text">
			<h6>
				{{ Auth::guard('doctor')->user()->name ?? '' }}		
			 	@if(Auth::user()->is_verified =='yes')
			 	<img src="{{ asset('img/verified.jpg') }}" alt=""style="width:30px;height:30px;">
				@endif
		    </h6>
			<span class="brand_color">{{ Auth::guard('doctor')->user()->bmdc_no ?? '' }}</span>
		</div>

	</div>
	<ul class="side_bar_ul pt-3 mb-4">
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='my-profile'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{url('my-profile')}}">
				<span>My Account</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='doctor-admissions'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('doctor-admissions') }}">
				<span>Admission Form</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='payment-details' || Request::segment(1)=='doctor-admission-submit'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{url('payment-details')}}">
				<span>Pay Course Fee</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='my-courses'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('my-courses') }}">
				<span>My Courses</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='doctor-result'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('doctor-result') }}">
				<span>My Results</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>


		{{-- <li class="bg-white msi-my-1 rounded shadow-sm {{ ( Request::segment(1)=='schedule' && Request::segment(2) == '' ) || Request::segment(1)=='doctor-course-batch-schedule'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('schedule') }}">
				<span>My Schedules</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li> --}}

		<li class="bg-white msi-my-1 rounded shadow-sm {{  Request::segment(1)=='schedule'  && Request::segment(2) == 'master-schedule' ? 'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('schedule/master-schedule') }}">
				<span>My Schedules</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='lecture-video' || Request::segment(1)=='doctor-course-lecture-video' || Request::segment(1)=='lecture-video-details' ? 'active' : '' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('lecture-video') }}">
				<span>Online Lecture Links</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm sidebar__item__badge {{ Request::segment(2)=='doctor-course-list-in-schedule' ? 'active' : '' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('doctor-course-list-in-schedule') }}">
				<span>New Schedule</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='request-lecture-video' ? 'active' : '' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('request-lecture-video') }}">
				<span>Pending Lecture Video</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm sidebar__item__badge {{ Request::segment(2)=='subscriptions' ? 'active' : '' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ route('my.subscriptions.index') }}">
				<span>Subscriptions</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='lecture-sheet-article'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('lecture-sheet-article') }}">
				<span>Lecture Sheet</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='online-exam' || Request::segment(1)=='doctor-course-online-exam'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('online-exam') }}">
				<span>Online Exam &amp; Results</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm ">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('complain') }}">
				<span>Complain Box</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>

		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='notice'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('notice') }}">
				<span>Notice</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='question-box' || Request::segment(1)=='view-answer'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('question-box') }}">
				<span>Question Box</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		{{-- <li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='complain-details'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('complain-box') }}">
				<span>Complain Box</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li> --}}
		<li class="bg-white msi-my-1 rounded shadow-sm {{ Request::segment(1)=='change-password'?'active':'' }}">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ url('change-password') }}">
				<span>Change Password</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
		</li>
		<li class="bg-white msi-my-1 rounded shadow-sm">
			<a class="msi-bold w-100 msi-dark d-flex  py-3 pl-3" href="{{ route('logout') }}" onclick="event.preventDefault();
			document.getElementById('logout-form').submit();">
				<span>Log Out</span>
				<i class="ml-auto msi-t-20 pr-3 fa fa-angle-right"></i>
			</a>
			<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
				{{ csrf_field() }}
			</form>
		</li>
	</ul>
</div>



<script>
	//sidebar
	const sideBarIcon = document.getElementById('side_bar_btn');
	const sideBarMenu = document.getElementById('side_bar_menu');
	sideBarIcon.addEventListener("click", function(){
		sideBarMenu.classList.toggle('side_bar_menu_click')
		this.classList.toggle('rotate_180')
	})
</script>