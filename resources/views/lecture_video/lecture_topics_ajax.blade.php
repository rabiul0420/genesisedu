
                                        <div class="row mx-0">                             
                                            <div class="col p-0 pt-3">
                                                <table class="bg-white table text-center table-striped table-bordered rounded p-1 table-hover datatable">
                                                    <thead>
                                                        <tr>
                                                            <th>SL</th>
                                                            <th>Date</th>
                                                            <th>Action</th>
                                                            <th>Video Title</th>
                                                        </tr>
                                                    </thead> 
                                                    <tbody>
                                                        @foreach($lecture_video_batch as $k => $lecture_video)
                                                        <tr>
                                                            <td class="pt-3">{{ $k+1 }}</td>
                                                            <td class="pt-3">{{ date('d-m-Y',strtotime($lecture_video->created_time)) }}</td>
                                                            <td class="">
                                                                
                                                                <a class="btn btn-primary btn-sm text-white  venobox" 
                                                                @if( $lecture_video->password)
                                                                    title="{{ '<h6 class="text-warning"> Password: '. $lecture_video->password .'</h6>'  }}"
                                                                @endif
                                                                data-autoplay="true" data-gall="gallery01" data-vbtype="video"
                                                                href="{{ $lecture_video->lecture_address }}">
                                                                play
                                                                </a>


                                                                @if($lecture_video->pdf_file)
                                                                    <a class="btn btn-info btn-sm text-white" href="{{url('lecture-video-details/'.$lecture_video->id)}}">PDF</a>
                                                                @endif
                                                            </td>
                                                            <td class="pt-3 text-left">{{ $lecture_video->name }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                        </div>
                                         