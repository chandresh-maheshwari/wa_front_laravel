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
              <div class="pull-right pr-3">
                <a href="product_list">English</a> | <a href="product_list_cn"> 中文 (香港)</a>
            </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table" id="product_datatable">
                    <thead class=" text-primary">
                      <th>Sr.No</th>
                      <th>Title</th>
                      <th>File</th>
                      <th>Storage</th>
                      <th>Price per month 6Above</th>
                      <th>Price per month 6Below</th>
                      <th>Free Insurance</th>
                      <th>Charge</th>
                      <th>Object</th>
                      <th>Time</th>
                      <!-- <th>Description</th> -->
                      {{-- <th>Created</th>
                      <th>Updated</th> --}}
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
                        if($item->title_cn !== null){   
                        $count++;
                      ?>
                        <tr>
                          <td>{{$count}}</td>
                          <td>{{$item->title_cn}}</td>
                          <td><img src="{{url('images/'.$item->file_cn)}}" alt="" height="50" width="50"></td>
                          <td>{{$item->storage_cn}}</td>
                          <td>{{$item->price_per_month_6above_cn}}</td>
                          <td>{{$item->price_per_month_6below_cn}}</td>
                          <td>{{$item->free_insurance_cn}}</td>
                          <td>{{$item->charge_cn}}</td>
                          <td>{{$item->object_cn}}</td>
                          <td>{{$item->time_cn}}</td>
                          <!-- <td>{{$item->description_cn}}</td> -->
                          {{-- <td>{{$item->created_at}}</td> --}}
                          {{-- <td>{{$item->updated_at}}</td> --}}
                          <td class='action'>
                            <a class="btn btn-info btn-sm " href="/product_update/{{$item->id}}"><i class="fa fa-edit" style="color: #fff;"></i></a>
                            <a class="btn btn-danger btn-sm delete-confirm" href="/product_delete/{{$item->id}}"><i class="fa fa-trash" style="color: #fff;"></i></a>
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
          <!-- <div class="col-md-12">
            <div class="card card-plain">
              <div class="card-header">
                <h4 class="card-title"> Table on Plain Background</h4>
                <p class="category"> Here is a subtitle for this table</p>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class=" text-primary">
                      <th>
                        Name
                      </th>
                      <th>
                        Country
                      </th>
                      <th>
                        City
                      </th>
                      <th class="text-right">
                        Salary
                      </th>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          Dakota Rice
                        </td>
                        <td>
                          Niger
                        </td>
                        <td>
                          Oud-Turnhout
                        </td>
                        <td class="text-right">
                          $36,738
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Minerva Hooper
                        </td>
                        <td>
                          Curaçao
                        </td>
                        <td>
                          Sinaai-Waas   
                        </td>
                        <td class="text-right">
                          $23,789
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Sage Rodriguez
                        </td>
                        <td>
                          Netherlands
                        </td>
                        <td>
                          Baileux
                        </td>
                        <td class="text-right">
                          $56,142
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Philip Chaney
                        </td>
                        <td>
                          Korea, South
                        </td>
                        <td>
                          Overland Park
                        </td>
                        <td class="text-right">
                          $38,735
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Doris Greene
                        </td>
                        <td>
                          Malawi
                        </td>
                        <td>
                          Feldkirchen in Kärnten
                        </td>
                        <td class="text-right">
                          $63,542
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Mason Porter
                        </td>
                        <td>
                          Chile
                        </td>
                        <td>
                          Gloucester
                        </td>
                        <td class="text-right">
                          $78,615
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Jon Porter
                        </td>
                        <td>
                          Portugal
                        </td>
                        <td>
                          Gloucester
                        </td>
                        <td class="text-right">
                          $98,615
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> -->
        </div>
      </div>
    
      <script>
        $(document).ready(function() {
          $('#product_datatable').DataTable();
        });
      </script>
      @include('common.footer');    