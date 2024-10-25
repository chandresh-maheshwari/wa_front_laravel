@include('common.header')
@include('common.sidebar')

<body class="user-profile">
  <div class="wrapper ">

    <div class="main-panel" id="main-panel">
      <!-- Navbar -->
      @include('common.navbar')

      <!-- End Navbar -->
      <div class="panel-header panel-header-sm">
      </div>
      <div class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5 class="title">Add Product</h5>
              </div>
              <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#english">English</a></li>
                <li><a data-toggle="tab" href="#chinese">chinese</a></li>
              </ul>

              <div class="tab-content">
                <div id="english" class="tab-pane fade in active">
                  <div class="card-body">
                    <form method="post" action='{{route("add")}}' enctype="multipart/form-data">
                      @csrf
                      <div class="row">
                        <div class="col-md-12 pr-1">
                          <div class="form-group">
                            <label>Title</label>
                            <!-- <input type="text" name="text"> -->
                            <input type="text" class="form-control" placeholder="" name="title" id="title" required>
                          </div>
                        </div>
                      </div>                
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label>Minimum storage period </label>
                            <input type="number" class="form-control" placeholder="" name="storage" required>
                          </div>
                        </div>                   
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Free Insurance</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="free_insurance" id="free_insurance" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(3-5 Months)</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="price_per_month_6below" id="price_per_month" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(6+ Months)</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="price_per_month_6above" id="price_per_month" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label> Who to charge </label>
                            <input type="text" class="form-control" placeholder="" name="charge" id="charge" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label> Retrieve the object </label>
                            <input type="text" class="form-control" placeholder="" name="object" id="object" required>
                          </div>
                        </div>                    
                      </div>      
                      <div class="row">                           
                        <div class="col-md-6">
                          <div class="form-group">
                            <label> Transit time </label>
                            <input type="text" class="form-control" placeholder="" name="time" id="time" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="  ">
                            <label for="file">Image</label></div>
                            <div>
                            <input type="file" name="file" id="file" style="color: #9A9A9A;" required>
                            <!-- <input class="form-control form-control-sm" name="uploadfile" id="formFileSm" type="file" required><br> -->
                          </div>
                        </div>
                      </div>   
                        <div class="row">  
                        <div class="col-md-12 pr-1">
                          <div class="form-group">
                            <label>Description</label>
                            <textarea rows="4" cols="80" class="form-control" placeholder="" name="description" required></textarea>
                          </div>
                        </div>
                      </div>
                      <div>
    
                        <input type="submit" class="btn btn-success" value="Submit">
                        <a href="product_list"><button type="button" class="btn btn-outline-success">Cancel</button></a>
    
                      </div>
                    </form>
    
                  </div>
                </div>
                <div id="chinese" class="tab-pane fade">
                  <div class="card-body">
                    <form method="post" action='{{route("add")}}' enctype="multipart/form-data">
                      @csrf
                      <div class="row">
                        <div class="col-md-12 pr-1">
                          <div class="form-group">
                            <label>Title</label>
                            <!-- <input type="text" name="text"> -->
                            <input type="text" class="form-control" placeholder="" name="title_cn" id="title_cn" required>
                          </div>
                        </div>
                      </div>                
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label>Minimum storage period </label>
                            <input type="number" class="form-control" placeholder="" name="storage_cn" required>
                          </div>
                        </div>                   
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Free Insurance</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="free_insurance_cn" id="free_insurance_cn" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(3-5 Months)</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="price_per_month_6below_cn" id="price_per_month_6below_cn" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Price per month(6+ Months)</label>
                            <input type="number" step=0.01 class="form-control" placeholder="" name="price_per_month_6above_cn" id="price_per_month_6above_cn" required>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 pr-1">
                          <div class="form-group">
                            <label> Who to charge </label>
                            <input type="text" class="form-control" placeholder="" name="charge_cn" id="charge_cn" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label> Retrieve the object </label>
                            <input type="text" class="form-control" placeholder="" name="object_cn" id="object_cn" required>
                          </div>
                        </div>                    
                      </div>      
                      <div class="row">                           
                        <div class="col-md-6">
                          <div class="form-group">
                            <label> Transit time </label>
                            <input type="text" class="form-control" placeholder="" name="time_cn" id="time_cn" required>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="  ">
                            <label for="file_cn">Image</label></div>
                            <div>
                            <input type="file" name="file_cn" id="file_cn" style="color: #9A9A9A;" required>
                            <!-- <input class="form-control form-control-sm" name="uploadfile" id="formFileSm" type="file" required><br> -->
                          </div>
                        </div>
                      </div>   
                        <div class="row">  
                        <div class="col-md-12 pr-1">
                          <div class="form-group">
                            <label>Description</label>
                            <textarea rows="4" cols="80" class="form-control" placeholder="" name="description_cn" required></textarea>
                          </div>
                        </div>
                      </div>
                      <div>
    
                        <input type="submit" class="btn btn-success" value="Submit">
                        <a href="product_list/cn"><button type="button" class="btn btn-outline-success">Cancel</button></a>
    
                      </div>
                    </form>
    
                  </div>
                </div>                 
              </div>
            </div>


              
            </div>
          </div>
          <!-- <div class="col-md-4">
            <div class="card card-user">
              <div class="image">
                <img src="../assets/img/bg5.jpg" alt="...">
              </div>
              <div class="card-body">
                <div class="author">
                  <a href="#">
                    <img class="avatar border-gray" src="../assets/img/mike.jpg" alt="...">
                    <h5 class="title">Mike Andrew</h5>
                  </a>
                  <p class="description">
                    michael24
                  </p>
                </div>
                <p class="description text-center">
                  "Lamborghini Mercy <br>
                  Your chick she so thirsty <br>
                  I'm in that two seat Lambo"
                </p>
              </div>
              <hr>
              <div class="button-container">
                <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
                  <i class="fab fa-facebook-f"></i>
                </button>
                <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
                  <i class="fab fa-twitter"></i>
                </button>
                <button href="#" class="btn btn-neutral btn-icon btn-round btn-lg">
                  <i class="fab fa-google-plus-g"></i>
                </button>
              </div>
            </div>
          </div> -->
        </div>
      </div>
      @include('common.footer');