@include('common.header');
@include('common.sidebar');
<body class="user-profile">
    <div class="wrapper ">

        <div class="main-panel" id="main-panel">
            <!-- Navbar -->
            @include('common.navbar');

            <!-- End Navbar -->
            <div class="panel-header panel-header-sm">
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="title">Add Client</h5>
                            </div>
                            {{-- <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#english">English</a></li>
                                <li><a data-toggle="tab" href="#chinese">chinese</a></li>
                            </ul> --}}
                            {{-- <div class="tab-content"> --}}
                            {{-- <d iv id="english" class="tab-pane fade in active"> --}}
                            <div class="card-body">
                                <form method="post" action='#' enctype="multipart/form-data" id="clientform">
                                    @csrf
                                    {{-- <div class="row">
                                        <div class="col-md-12 pr-1">
                                            <div class="form-group">
                                                <label>Testimonial</label>
                                                <textarea rows="4" cols="80" class="form-control"  class="testimonial" id="testimonialtextarea" name="testimonialtextarea">
                                                </textarea>
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label>Add Partner Name</label>
                                                <input type="text"class="form-control" name="partner_name"
                                                    id="partner_name" autocomplete="off" id="add_by">

                                                <!-- <input type=" date text" class="form-control" placeholder="" name="period_by" id="period_by"> -->
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="">
                                                <label>Add Partner Logo</label>
                                                <!-- <input type="text" class="form-control" placeholder="" name="period_to" id="period_to"> -->
                                                <input type="file" class="form-control" id="partner_logo"
                                                    name="partner_logo">
                                                {{-- <input type="file" id="myfile" name="myfile"> --}}
                                            </div>
                                        </div>
                                    </div>


                                    {{-- {{dd($data)}} --}}

                                    <div id="more_content"></div>
                                    <div class="row">
                                        <div class="col-11">
                                            <input type="submit" class="btn btn-success" value="Submit">
                                            <a href="{{ url('/clist') }}"><button type="button"
                                                    class="btn btn-outline-success">Cancel</button></a>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- <script>
                CKEDITOR.replace('testimonial');
            </script> --}}
            {{-- <script>
                CKEDITOR.replace('testimonialtextarea');
            </script> --}}
            <script>
                $(document).ready(function() {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $('#clientform').submit(function(e) {
                        e.preventDefault();

                        let frmData = new FormData(this);
                    
                        $.ajax({
                            url: "{{ route('client.store') }}",
                            type: 'POST',
                            data: frmData,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                if (data.success) {
                                    swal('Client saved successfully', '', 'success');
                                    $('#clientform')[0].reset();
                                } else {
                                    swal('Error saving Client', '', 'error');
                                }
                            },
                           
                        });
                    });
                });
            </script>


            @include('common.footer');
