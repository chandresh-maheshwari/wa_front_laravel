@include('common.header');
@include('common.sidebar');

<body class="">
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
                <h4 class="card-title"> Product List</h4>
              </div>
              {{-- <div class="pull-right pr-3">
                <a href="/account_list">English</a> | <a href="/account_list/cn"> 中文 (香港)</a>
            </div> --}}
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table" id="account_datatable">
                    <thead class=" text-primary">
                      <th>Sr.No</th>
                      <th>name</th>
                      <th>email</th>
                      <th>address</th>
                      <th>city</th>
                      <th>states</th>
                      <th>contact</th>
                      <th>pickup_address</th>
                      <th>date1</th>
                      <th>time1</th>
                      <th>date2</th>
                      <th>time2</th>
                      <th>date3</th>
                      <th>time3</th>                
                      <th>Action</th>

                    </thead>
                    <tbody>
                      <?php
                      //  echo "<pre>";
                      //  print_r($data->all());
                      //  die();
                      $count = 0;
                      // $a= date();
                      // {{dd($data);}}
                      foreach ($data as $item) {

                      if($item->title !== null){
                        $count++;
                      ?>
                        <tr>
                          <td>{{$count}}</td>
                          <td>{{$item->name}}</td>
                          <td>{{$item->email}}</td>
                          <td>{{$item->address}}</td>
                          <td>{{$item->city}}</td>
                          <td>{{$item->states}}</td>
                          <td>{{$item->contact}}</td>
                          <td>{{$item->pickup_address}}</td>
                          <td>{{$item->date1}}</td>
                          <td>{{$item->time1}}</td>
                          <td>{{$item->date2}}</td>
                          <td>{{$item->time2}}</td>
                          <td>{{$item->date3}}</td>
                          <td>{{$item->time3}}</td>

                         
                          <td class='action'>
                            <a class="btn btn-info btn-sm" href="/product_update/{{$item->id}}"><i class="fa fa-edit" style="color: #fff;"></i></a>
                            <a class="btn btn-danger btn-sm" href="/product_delete/{{$item->id}}"><i class="fa fa-trash" style="color: #fff;"></i></a>
                          </td>
                          </td>

                          <!-- <td>{{$item->title}}</td> -->
                        </tr>
                        <?php
                      }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    
      <script>
        $(document).ready(function() {
          $('#account_datatable').DataTable();
        });
      </script>
      @include('common.footer');    