<div class="grid gap-2 p-2 md:grid-cols-4 max-w-5xl mx-auto" id="filterContainer">
    <select onchange="changeYearHandler(this.value)" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" id="yearSelector">
        <option value="">-- Select Year --</option>
        @for($i = date("Y") + 1; $i >= 2017; $i--)
        <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
    <select onchange="changeCourseHandler(this.value)" class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" id="courseSelector">
        <option value="">-- Select Course --</option>
    </select>
    <select class="block w-full px-3 py-2 border border-gray-400 rounded focus:outline-0" id="sessionSelector">
        <option value="">Select Session</option>
    </select>
    <input id="filterActionButton" type="button" class="block w-full px-3 py-2 border border-gray-400 bg-sky-600 text-white rounded focus:outline-0 cursor-pointer" value="Filter" />
</div>

<script>
    function changeYearHandler(year) {
        const url = `{{ url('api/institutes') }}?with_courses=true&year=${year}`;

        const courseSelector = document.getElementById('courseSelector');
        const sessionSelector = document.getElementById('sessionSelector');

        courseSelector.innerHTML = `<option value="">-- Select Course --</option>`;
        sessionSelector.innerHTML = `<option value="">-- Select Session --</option>`;

        axios.get(url)
            .then((response) => {
                const institutes = response.data;

                Object.values(institutes).forEach((institute) => {
                    let options = '';
                    Object.values(institute.courses).forEach((course) => {
                        options += `<option value="${course.id}">${course.name}</option>`;
                    });
                    courseSelector.innerHTML += `<optgroup  label="${institute.name}">${options}</optgroup>`;
                });
            });
    }

    function changeCourseHandler(courseId) {
        const yearSelector = document.getElementById('yearSelector');

        const url = `{{ url('api/sessions') }}?year=${yearSelector.value}&course_id=${courseId}`;

        const sessionSelector = document.getElementById('sessionSelector');

        sessionSelector.innerHTML = `<option value="">-- Select Session --</option>`;

        axios.get(url)
            .then((response) => {
                const sessions = response.data;

                Object.values(sessions).forEach((session) => {
                    sessionSelector.innerHTML += `<option value="${session.id}">${session.name}</option>`;
                });
            });
    }
</script>