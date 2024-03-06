@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center pt-3">
                        <h2 class="h2 brand_color">Add Subscriptions</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>

                <div class="col-md-12">
                    <div class="d-flex justify-content-between py-2">
                        <a class="btn btn-secondary" href="{{ route('my.subscriptions.index') }}">
                            <b>&#8592;</b> Back to Subscriptions
                        </a>
                    </div>
                </div>

                <hr>

                <form action="{{ route('my.subscriptions.add-subscription') }}" method="POST" class="p-4 bg-white position-relative">
                    <div id="loading" hidden class="w-100 h-100 position-absolute" style="inset: 0; background: #1234; display: flex; justify-content: center; align-items: center; text-align: center; font-size: 24px; color: #fff;">
                        <div>Loading ...</div>
                    </div>
                    {{ csrf_field() }}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <select class="form-select" id="courseSelector" name="course_id" required>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <select class="form-select" id="yearSelector" name="year" required>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Session</label>
                            <select class="form-select" id="sessionSelector" name="session_id" required>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4" hidden id="facultySelectorContainer">
                        <div class="col-md-6">
                            <label class="form-label">Faculty</label>
                            <select class="form-select" id="facultySelector" name="faculty_id">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4" hidden id="disciplineSelectorContainer">
                        <div class="col-md-6">
                            <label class="form-label">Discipline</label>
                            <select class="form-select" id="disciplineSelector" name="discipline_id">
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-info w-100">
                                Next
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>


</div>
@endsection

@section('js')
<script>
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer {{ $token }}`,
    };

    const loadingItems = {
        'course': false,
        'year': false,
        'session': false,
        'faculty': false,
        'discipline': false,
    };

    const courseSelector = document.getElementById('courseSelector');
    const yearSelector = document.getElementById('yearSelector');
    const sessionSelector = document.getElementById('sessionSelector');
    const facultySelector = document.getElementById('facultySelector');
    const disciplineSelector = document.getElementById('disciplineSelector');

    const facultySelectorContainer = document.getElementById('facultySelectorContainer');
    const disciplineSelectorContainer = document.getElementById('disciplineSelectorContainer');

    function loading($item, $status) {
        loadingItems[$item] = $status;

        if (Object.values(loadingItems).find(item => item === true)) {
            document.getElementById('loading').hidden = false;
        } else {
            document.getElementById('loading').hidden = true;
        }
    }

    function callCourseApi() {
        loading('course', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/courses`;

        courseSelector.innerHTML = '<option value=""> -- Select Course -- </option>';
        
        axios.get(url, {
            headers
        })
        .then(({data}) => {
            Object.values(data.courses).forEach((course) => {
                courseSelector.innerHTML += `<option value="${course.id}">${course.name}</option>`;
            });
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            loading('course', false);  
        });
    }

    function callYearApi(courseId) {
        loading('year', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/years`;

        yearSelector.innerHTML = '<option value=""> -- Select Year -- </option>';

        const data = {
            course_id: courseId,
        };
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            Object.values(data.years).forEach((year) => {
                yearSelector.innerHTML += `<option value="${year.value}">${year.label}</option>`;
            });
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            loading('year', false);  
        });
    }

    function callSessionApi(courseId, year) {
        loading('session', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/sessions`;

        sessionSelector.innerHTML = '<option value=""> -- Select Session -- </option>';

        const data = {
            course_id: courseId,
            year: year,
        };
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            Object.values(data.sessions).forEach((session) => {
                sessionSelector.innerHTML += `<option value="${session.id}">${session.name}</option>`;
            });
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            loading('session', false);  
        });
    }

    function callFacultyApi(year, courseId, sessionId) {
        loading('faculty', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/faculties`;

        facultySelector.innerHTML = '<option value=""> -- Select Faculty -- </option>';

        const data = {
            year: year,
            course_id: courseId,
            session_id: sessionId,
        };
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            if(data.faculty_status) {
                facultySelectorContainer.hidden = false;
                facultySelector.required = true;

                Object.values(data.faculties).forEach((faculty) => {
                    facultySelector.innerHTML += `<option value="${faculty.id}">${faculty.name}</option>`;
                });
            } else {
                facultySelectorContainer.hidden = true;
                facultySelector.required = false;
            }
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            loading('faculty', false);  
        });
    }

    function callDisciplineApi(year, courseId, sessionId) {
        loading('discipline', true);

        const url = `{{ env('API_BASE_URL') }}/v2/doctor/subscriptions/disciplines`;

        disciplineSelector.innerHTML = '<option value=""> -- Select Discipline -- </option>';

        const data = {
            year: year,
            course_id: courseId,
            session_id: sessionId,
        };
        
        axios.get(url, {
            params: data,
            headers,
        })
        .then(({data}) => {
            if(data.discipline_status) {
                disciplineSelectorContainer.hidden = false;
                disciplineSelector.required = true;

                Object.values(data.disciplines).forEach((discipline) => {
                    disciplineSelector.innerHTML += `<option value="${discipline.id}">${discipline.name}</option>`;
                });
            } else {
                disciplineSelectorContainer.hidden = true;
                disciplineSelector.required = false;
            }
        })
        .catch((error) => {
            console.log(error);
        })
        .finally(() => {
            loading('discipline', false);  
        });
    }

    callCourseApi();

    courseSelector.addEventListener('change', () => {
        const courseId = courseSelector.value;

        if(courseId) {
            callYearApi(courseId);
        }
    });

    yearSelector.addEventListener('change', () => {
        const courseId = courseSelector.value;
        const year = yearSelector.value;

        if(courseId && year) {
            callSessionApi(courseId, year);
        }
    });

    sessionSelector.addEventListener('change', () => {
        const courseId = courseSelector.value;
        const year = yearSelector.value;
        const sessionId = sessionSelector.value;

        if(courseId && year && sessionId) {
            callFacultyApi(year, courseId, sessionId);
            callDisciplineApi(year, courseId, sessionId);
        }
    });

</script>
@endsection
