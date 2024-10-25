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
                                <h5 class="title">Update Client</h5>
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
                                    <input type="hidden" class="form-control" name="id" id="id"
                                        value="{{ $id }}">
                                    <div class="row">
                                        <div class="col-md-6 pr-1">
                                            <div class="form-group">
                                                <label>Update Partner Name</label>
                                                <input type="text"class="form-control" name="partner_name"
                                                    id="partner_name" autocomplete="off">

                                                <!-- <input type=" date text" class="form-control" placeholder="" name="period_by" id="period_by"> -->
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="">
                                                <label>Update Partner Logo</label>
                                                <!-- <input type="text" class="form-control" placeholder="" name="period_to" id="period_to"> -->
                                                <input type="file" class="form-control" id="partner_logo"
                                                    name="partner_logo">
                                                <span id="old"></span>
                                                <input type="hidden" name="oldfile" id="oldfileid"> 
                                                {{-- <input type="file" id="myfile" name="myfile"> --}}
                                            </div>
                                        </div>
                                    </div>


                                    {{-- {{dd($data)}} --}}

                                    <div id="more_content"></div>
                                    <div class="row">
                                        <div class="col-11">
                                            <input type="submit" class="btn btn-success" value="Submit">
                                            <a href="/clist"><button type="button"
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
                $(document).ready(function() {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('client.update') }}",
                        type: "GET",
                        data: {
                            // token: $("#api_token").html(),
                            id: $("#id").val()
                        },
                        success: function(response) {

                            console.log(response);
                            // CKEDITOR.instances['testimonialtextarea'].setData(response.testimonial); 
                            $('#partner_name').val(response.partner_name);
                            $('#old').html('<img src="/images/client/' + response.partner_logo +
                            '"calss="img-fluid"  alt="" style="width:50px; height:50px;"/>');
                            $('#oldfileid').val(response.partner_logo);


                        }
                    });
                    $("#clientform").submit(function(e) {
                e.preventDefault();
                var id = $('#id').val();
               
                var partner_name = $('#partner_name').val();
                let partner_logo = $("#partner_logo").prop('files')[0];
                let old = $("#oldfileid").val();

                var form_data = new FormData();
                form_data.append('id',id);
                form_data.append('partner_name',partner_name);
                form_data.append('partner_logo',partner_logo);
                form_data.append('oldfile',old);
               
                $.ajax({
                    url: "{{ route('client.update-save') }}",
                    type: "post",
                    datatype: "json",
                    processData: false,
                    contentType: false,
                    data:form_data,    
                    success: function(data) {
                       
                        if (data.success) {
                    swal('Testimonial Update successfully', '', 'success');
                    // $('#clientform')[0].reset();
                } else {
                    swal('Error saving testimonial', '', 'error');
                }

                       
                    },
                 
                });

            });
                });
            </script>



            @include('common.footer');
