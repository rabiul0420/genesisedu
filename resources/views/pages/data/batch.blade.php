<div class="row">
    <div class="col-12">
        <div class="___class_+?6___">
            <div class=" py-3" style="width: 100%; overflow: auto;">
                <table class="table w-100 d-none d-lg-table ">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th class="text-center">Actions</th>
                            <th>Batch Name</th>
                            <th>Starting from</th>
                            <th>Days</th>
                            <th>Time</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse( $batches as $batch)
                            <tr class="residency" @if($batch->created_at != $batch->updated_at) style="background: #eeee;" @endif>
                                <td>{{ $batch->course_name }}</td>
                                <td style="min-width: max-content;">
                                    <div style="min-width: max-content;" class="col text-center">
                                        <a class="btn btn-sm btn-success rounded-lg btn-batch"
                                            href="{{ url('batch-details/' . $batch->id) }}">Details</a>
                                        <a class="btn btn-sm btn-warning rounded-lg btn-batch {{ $batch->batch->id ?? 0 ? '' : 'disabled' }}"
                                            href="{{ url('view-batch-schedule/' . ($batch->batch->id ?? 0)) }}">
                                            Schedule
                                        </a>
                                    </div>
                                </td>
                                <td>{{ $batch->batch_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($batch->start_date)->format('d/m/Y') }}</td>
                                <td>{{ $batch->days }}</td>
                                <td>{{ $batch->time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-5 display-6 border-0">No Batch Available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <table class="table w-100 d-lg-none d-table">
                    @if (!empty(json_decode($batches)))
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Batch Name</th>
                            </tr>
                        </thead>
                    @endif
                    <tbody>
                        @forelse($batches as $batch)
                            
                            <tr class="residency">
                                <td>
                                    {{ $batch->course_name }}
                                </td>
                                <td>
                                    <p><a
                                            href="{{ url('batch-details/' . $batch->id) }}">{{ $batch->batch_name }}</a>
                                    </p>
                                    <p>{{ \Carbon\Carbon::parse($batch->start_date)->format('d/m/Y') }}
                                    </p>
                                    <p>{{ $batch->days }}</p>
                                    <p>{{ $batch->time }}</p>
                                    <p class="col text-center btn-1">
                                        <a class="btn btn-success rounded-lg btn-batch"
                                            href="{{ url('batch-details/' . $batch->id) }}">Details</a>
                                        <a class="btn btn-warning rounded-lg btn-batch {{ $batch->batch->id ?? 0 ? '' : 'disabled' }}"
                                            href="{{ url('view-batch-schedule/' . ($batch->batch->id ?? 0)) }}">View
                                            Schedule</a>
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center py-5 display-6 border-0">No Batch Available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">{{ $batches->links('components.paginator') }}
                </div>
            </div>
        </div>
    </div>
</div>