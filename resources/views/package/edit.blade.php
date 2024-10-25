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
                                <h5 class="title">Update Packages</h5>
                            </div>
                            {{-- <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#english">English</a></li>
                                <li><a data-toggle="tab" href="#chinese">chinese</a></li>
                            </ul> --}}
                            {{-- <div class="tab-content"> --}}
                            {{-- <d iv id="english" class="tab-pane fade in active"> --}}
                            <div class="card-body">
                                <form method="post" action='#' enctype="multipart/form-data" id="packageform">
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
                                    <input type="hidden" class="form-control" name="id" id="id"
                                        value="{{ $id }}">
                                    <div class="row">
                                        <div class="col-md-12 pr-1">
                                            <div class="form-group">
                                                <label>Package Title</label>
                                                <input type="text"class="form-control" name="package_title"
                                                    autocomplete="off" id="package_title">

                                                <!-- <input type=" date text" class="form-control" placeholder="" name="period_by" id="period_by"> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 pr-1">
                                            <div class="form-group">
                                                <label>Package Description</label>
                                                <textarea rows="4" cols="80" class="form-control" class="testimonial" id="package_des" name="package_des">
                                                </textarea>
                                                <!-- <input type=" date text" class="form-control" placeholder="" name="period_by" id="period_by"> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 pr-1">
                                            <div class="form-group">
                                                <label>Package Price</label>
                                                <input type="text"class="form-control" name="package_price"
                                                    id="package_price" autocomplete="off" id="package_price">

                                                <!-- <input type=" date text" class="form-control" placeholder="" name="period_by" id="period_by"> -->
                                            </div>
                                        </div>
                                    </div>
                                    {{-- {{dd($data)}} --}}
                                    <div class="row">
                                        <div class="col-md-12 pr-1">
                                            <div class="form-group">
                                                <label>Additional Information</label>
                                                <textarea rows="4" cols="80" class="form-control" class="" id="additional_info" name="additional_info">
                                                </textarea>
                                                <!-- <input type=" date text" class="form-control" placeholder="" name="period_by" id="period_by"> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div id="more_content"></div>
                                    <div class="row">
                                        <div class="col-11">
                                            <input type="submit" class="btn btn-success" value="Submit">
                                            <a href="#"><button type="button"
                                                    class="btn btn-outline-success">Cancel</button></a>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <script>
                CKEDITOR.replace('package_des');
                CKEDITOR.replace('additional_info');
            </script>

            <script>
                $(document).ready(function() {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('package.update') }}",
                        type: "GET",
                        data: {
                            // token: $("#api_token").html(),
                            id: $("#id").val()
                        },
                        success: function(response) {

                            // console.log(response);

                            $('#package_title').val(response.package_title);
                            CKEDITOR.instances['package_des'].setData(response.package_des);
                            $('#package_price').val(response.package_price);
                            CKEDITOR.instances['additional_info'].setData(response.additional_info);




                        }
                    });

                    $("#packageform").submit(function(e) {
                        e.preventDefault();

                        //    var data = $(this).serialize();

                        //    console.log(data);
                        $.ajax({
                            url: "{{ route('package.update-save') }}",
                            type: "post",
                            data: $(this).serialize(),
                            // data: {
                            //     // data: $(this).serialize(),
                            // },
                            success: function(data) {

                                if (data.success) {
                                    swal('Package Update successfully', '', 'success');
                                } else {
                                    swal('Error saving Package', '', 'error');
                                }


                            },
                          
                        });

                    });
                });
            </script>



            @include('common.footer');
