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
                <h5 class="title">Update Product</h5>
              </div>
              <ul class="nav nav-tabs">
                <li class="{{$data->title ? 'active show in' : ""  }}"><a data-toggle="tab" href="#english">English</a></li>
                <li class="{{$data->title_cn ? 'active show in' : ""  }}"><a data-toggle="tab" href="#chinese">chinese</a></li>
              </ul>
              <div class="tab-content">
                <div id="english" class="tab-pane fade {{$data->title ? 'active show in' : ""  }}">
                  <div class="card-body">
                    <form action='{{route("update")}}' enctype="multipart/form-data" method="post">
                      @csrf
                      <div class="row">
                        <div class="col-md-12 pr-1">
                          <div class="form-group">
                            <label>Title</label>                        
                            <input type="hidden" value="{{$data->id}}" name="id">
                            <input type="text" value="{{$data->title}}" class="form-control" placeholder="" name="title" id="title" required>
                          </div>
                        </div>
                      </div>                                 
                      <div class="row">                  
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label>Minimum storage period </label>
                            <input type="number" class="form-control" placeholder="" name="storage" value="{{$data->storage}}" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Free Insurance</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="free_insurance" id="free_insurance" value="{{$data->free_insurance}}" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(3-5 Months)</label>
                            <input type="number" step=0.01 class="form-control" value="{{$data->price_per_month_6below}}" placeholder="Enter Months" name="price_per_month_6above" id="price_per_month" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(6+ Months)</label>
                            <input type="number" step=0.01 class="form-control" value="{{$data->price_per_month_6above}}" placeholder="Enter Months" name="price_per_month_6below" id="price_per_month" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label> Who to charge </label>
                            <input type="text" class="form-control" value="{{$data->charge}}" placeholder="" name="charge" id="charge" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label> Retrieve the object </label>
                            <input type="text" class="form-control" value="{{$data->object}}" placeholder="" name="object" id="object" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label> Transit time </label>
                            <input type="text" class="form-control" value="{{$data->time}}" placeholder="" name="time" id="time" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="  ">
                            <label for="file">Image</label></div>
                            <div>
                            <input type="file" name="file" id="file"style="color: #9A9A9A;">
                            <input type="hidden" name="old_file" id="old_file" value="{{$data->file}}" style="color: #9A9A9A;">
    
                            <img src="{{url('images/'.$data->file)}}" alt="" height="50" width="50">
                            <!-- <input class="form-control form-control-sm" name="uploadfile" id="formFileSm" type="file" required><br> -->
                          </div>
                        </div>
                      </div>                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Description</label>
                            <textarea rows="4" cols="80" class="form-control" placeholder="" name="description" required>{{$data->description}}</textarea>
                          </div>
                        </div>
                      </div>
                      <div>
    
                        <input type="submit" class="btn btn-success" value="Update">
                        <a href="../product_list"><button type="button" class="btn btn-outline-success">Cancel</button></a>
    
                      </div>
                    </form>
    
                  </div>
                </div>
                <div id="chinese" class="tab-pane fade {{$data->title_cn ? 'active show in' : ""  }}">
                  <div class="card-body">
                    <form action='{{route("update")}}' enctype="multipart/form-data" method="post">
                      @csrf
                      <div class="row">
                        <div class="col-md-12 pr-1">
                          <div class="form-group">
                            <label>Title</label>                        
                            <input type="hidden" value="{{$data->id}}" name="id">
                            <input type="text" value="{{$data->title_cn}}" class="form-control" placeholder="" name="title_cn" id="title_cn" required>
                          </div>
                        </div>
                      </div>                                 
                      <div class="row">                  
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label>Minimum storage period </label>
                            <input type="number" class="form-control" placeholder="" name="storage_cn" value="{{$data->storage_cn}}" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Free Insurance</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="free_insurance_cn" id="free_insurance_cn" value="{{$data->free_insurance_cn}}" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(3-5 Months)</label>
                            <input type="number" step=0.01 class="form-control" value="{{$data->price_per_month_6below_cn}}" placeholder="Enter Months" name="price_per_month_6above_cn" id="price_per_month_6above_cn" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(6+ Months)</label>
                            <input type="number" step=0.01 class="form-control" value="{{$data->price_per_month_6above_cn}}" placeholder="Enter Months" name="price_per_month_6below_cn" id="price_per_month_6below_cn" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label> Who to charge </label>
                            <input type="text" class="form-control" value="{{$data->charge_cn}}" placeholder="" name="charge_cn" id="charge_cn" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label> Retrieve the object </label>
                            <input type="text" class="form-control" value="{{$data->object_cn}}" placeholder="" name="object_cn" id="object_cn" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label> Transit time </label>
                            <input type="text" class="form-control" value="{{$data->time_cn}}" placeholder="" name="time_cn" id="time_cn" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="  ">
                            <label for="file">Image</label></div>
                            <div>
                            <input type="file" name="file_cn" id="file_cn"style="color: #9A9A9A;">
                            <input type="hidden" name="old_file_cn" id="old_file_cn" value="{{$data->file_cn}}" style="color: #9A9A9A;">
    
                            <img src="{{url('images/'.$data->file_cn)}}" alt="" height="50" width="50">
                            <!-- <input class="form-control form-control-sm" name="uploadfile" id="formFileSm" type="file" required><br> -->
                          </div>
                        </div>
                      </div>                     
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Description</label>
                            <textarea rows="4" cols="80" class="form-control" placeholder="" name="description_cn" required>{{$data->description_cn}}</textarea>
                          </div>
                        </div>
                      </div>
                      <div>
    
                        <input type="submit" class="btn btn-success" value="Update">
                        <a href="../product_list/cn"><button type="button" class="btn btn-outline-success">Cancel</button></a>
    
                      </div>
                    </form>
    
                  </div>
                </div>               
                
              </div>
              
            </div>
          </div>
          
        </div>
      </div>
      @include('common.footer');