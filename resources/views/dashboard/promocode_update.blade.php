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
                                <h5 class="title">Update Promocode</h5>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="{{$data->code ? 'active show in' : ""  }}"><a data-toggle="tab" href="#english">English</a></li>
                                <li class="{{$data->code_cn ? 'active show in' : ""  }}"><a data-toggle="tab" href="#chinese">chinese</a></li>
                            </ul>
                            {{-- @if($data->title == null){
                            {{$active = 'active';}}                            
                            }@else{
                            {{$active_cn = 'active';}}
                            } --}}
                            <div class="tab-content">
                                <div id="english" class="tab-pane fade {{$data->code ? 'active show in' : ""  }}">
                                    <div class="card-body">
                                        <form method="POST" action='{{ route('update_promo') }}' enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 pr-1">
                                                    <div class="form-group">
                                                        <label>Code</label>
                                                        {{-- {{dd($data)}} --}}
                                                        <input type="hidden" class="form-control" placeholder="" name="id"
                                                            id="id" value="{{ $data->id }}">
        
                                                        <input type="text" class="form-control" placeholder="" name="code"
                                                            id="code" value="{{ $data->code }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" class="form-control" placeholder="" name="name"
                                                            id="name" value="{{ $data->name }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 pr-1">
                                                    <div class="form-group">
                                                        <label>Validity Period (by)</label>
                                                        <input type="text" class="form-control date-picker" placeholder=""
                                                            name="period_by" id="period_by" value="{{ $data->period_by }}" autocomplete="off">
                                                    </div>
                                                </div>
                                                    <?php //echo "<pre>"; print_r($data); ?>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Validity Period (To)</label>
                                                        <input type="text" class="form-control date-picker" placeholder=""
                                                            name="period_to" id="period_to" value="{{ $data->period_to }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                                {{-- {{dd($product_promocode_data)}} --}}
                                                @if($data->code != null)
                                                @php $i=0; @endphp
                                                @foreach ($product_promocode_data as $item)
                                                @php $i++; @endphp
        
                                                {{-- {{dd($item->price)}} --}}
                                                <div class="row" id="row_{{$i}}">
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Price</label>
                                                            <!-- <input type="text" class="form-control" placeholder="" name="period_to" id="period_to"> -->
                                                            <input type="number" step=0.01 class="form-control" name="price[]" value="{{ $item->price }}">
                                                            <input type="hidden" class="form-control" name="p_id[]" value="{{ $item->id }}">        
                                                        </div>
                                                    </div>
            
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Product List</label>
                                                            <select name="product_dropdown[]" id="product_dropdown_{{$i}}" class="form-control p-2 selectpicker">
                                                                 {{-- <option value="">-- Select Product --</option> --}}
                                                                 {{-- {{dd($all_product_list);}} --}}
                                                                 @foreach ($all_product_list as $product)
                                                                 {{-- {{$item->product_name}} --}}
                                                                 @if ($product->id == $item->product_name)
                                                                 <option value="{{ $product->id }}" selected="true">
                                                                     {{ $product->title }}</option>
                                                             @else
                                                                 <option value="{{ $product->id }}">{{ $product->title }}
                                                                 </option>
                                                             @endif
                                                                 {{-- <option value="{{ $datas->id }}">{{ $datas->title }}</option> --}}
                                                             @endforeach
                                                            </select>
                                                        </div>
                                                    </div>  
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            {{-- <label>sdas</label> --}}
                                                            {{-- <input class="btn btn-danger abcd remove" value="Remove" id="remove_{{$i}}"> --}}
                                                            <a class="btn btn-danger remove" id="remove_{{$i}}"><i class="fa fa-trash" style="color: #fff;"></i></a>
        
                                                        </div>
                                                    </div>                                          
                                                    {{-- <div><input class="btn btn-danger" value="Remove" id="remove_{{$i}}"></div> --}}
                                                </div>
                                                @endforeach
                                                @endif
        
                                                <div id="more_content"></div>
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <input type="submit" class="btn btn-success" value="Update">
                                                        <a href="../promocode_datatable"><button type="button" class="btn btn-outline-success">Cancel</button></a>                                                
                                                    </div>
                                                    <div class="col-md-1">
        
                                                        <input class="btn btn-sm btn-info px-1 py-2 add_new" value="Add New" id="more">
                                                    </div>
                                                    {{-- <button type="button" name="add" id="add" class="btn btn-success">Add More</button> --}}
                                                </div>
                                        </form>
        
                                    </div>
                                </div>
                                <div id="chinese" class="tab-pane fade {{$data->code_cn ? 'active show in' : ""  }}">
                                    <div class="card-body">
                                        <form method="POST" action='{{ route('update_promo') }}' enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6 pr-1">
                                                    <div class="form-group">
                                                        <label>Code</label>
                                                        {{-- {{dd($data)}} --}}
                                                        <input type="hidden" class="form-control" placeholder="" name="id"
                                                            id="id" value="{{ $data->id }}">
        
                                                        <input type="text" class="form-control" placeholder="" name="code_cn"
                                                            id="code_cn" value="{{ $data->code_cn }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" class="form-control" placeholder="" name="name_cn"
                                                            id="name_cn" value="{{ $data->name_cn }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 pr-1">
                                                    <div class="form-group">    
                                                        <label>Validity Period (by)</label>
                                                        <input type="text" class="form-control date-picker" placeholder=""
                                                            name="period_by_cn" id="period_by_cn" value="{{$data->period_by_cn ? $data->period_by_cn : ""  }}" autocomplete="off">
                                                    </div>  
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Validity Period (To)</label>
                                                        <input type="text" class="form-control date-picker" placeholder=""
                                                            name="period_to_cn" id="period_to_cn" value="{{ $data->period_to_cn ? $data->period_to_cn : "" }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                          
                                                {{-- {{dd($product_promocode_data)}} --}}
                                                @if($data->code_cn != null)
                                                @php $i=0; @endphp
                                                @foreach ($product_promocode_data as $item)
                                                @php $i++; @endphp
        
                                                {{-- {{dd($item->price)}} --}}
                                                <div class="row" id="row_cn_{{$i}}">
                                                    <div class="col-md-5">  
                                                        <div class="form-group">
                                                            <label>Price</label>
                                                            <!-- <input type="text" class="form-control" placeholder="" name="period_to" id="period_to"> -->
                                                            <input type="number" step=0.01 class="form-control" name="price[]" value="{{ $item->price }}">
                                                            <input type="hidden" class="form-control" name="p_id[]" value="{{ $item->id }}">
        
                                                        </div>
                                                    </div>
            
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Product List</label>
                                                            <select name="product_dropdown[]" id="product_dropdown_cn_{{$i}}" class="form-control p-2 selectpicker_cn">
                                                                 {{-- <option value="">-- Select Product --</option> --}}
                                                                 {{-- $product_promocode_data->name; --}}
                                                                 @foreach ($all_product_list_cn as $product)
                                                                 {{-- {{$item->product_name}} --}}
                                                                 @if ($product->id == $item->product_name)
                                                                 <option value="{{ $product->id }}" selected="true">
                                                                     {{ $product->title_cn }}</option>
                                                             @else
                                                                 <option value="{{ $product->id }}">{{ $product->title_cn }}
                                                                 </option>
                                                             @endif
                                                                 {{-- <option value="{{ $datas->id }}">{{ $datas->title }}</option> --}}
                                                             @endforeach
                                                            </select>
                                                        </div>
                                                    </div>  
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            {{-- <label>sdas</label> --}}
                                                            {{-- <input class="btn btn-danger abcd remove" value="Remove" id="remove_{{$i}}"> --}}
                                                            <a class="btn btn-danger remove" id="remove_{{$i}}"><i class="fa fa-trash" style="color: #fff;"></i></a>
        
                                                        </div>
                                                    </div>                                          
                                                    {{-- <div><input class="btn btn-danger" value="Remove" id="remove_{{$i}}"></div> --}}
                                                </div>
                                                @endforeach
                                                @endif
                                                <div id="more_content_cn"></div>
                                                <div class="row">
                                                    <div class="col-md-11">
                                                        <input type="submit" class="btn btn-success" value="Update">
                                                        <a href="../promocode_datatable/cn"><button type="button" class="btn btn-outline-success">Cancel</button></a>                                                
                                                    </div>
                                                    <div class="col-md-1">
        
                                                        <input class="btn btn-sm btn-info px-1 py-2 add_new" value="Add New" id="more_cn">
                                                    </div>
                                                    {{-- <button type="button" name="add" id="add" class="btn btn-success">Add More</button> --}}
                                                </div>
                                        </form>
        
                                    </div>
                                </div>                                                             
                              </div>
    
                        </div>
                    </div>

                </div>
            </div>
            
           <script type="text/javascript">
            $(document).ready(function() {
                // $('.date-picker').datepicker();
                var start = new Date();
                    // set end date to max one year period:
                    var end = new Date(new Date().setYear(start.getFullYear()+1));

                    $('#period_by').datepicker({
                        startDate : start,
                        endDate   : end
                    // update "toDate" defaults whenever "fromDate" changes
                    }).on('changeDate', function(){
                        // set the "toDate" start to not be later than "fromDate" ends:
                        $('#period_to').datepicker('setStartDate', new Date($(this).val()));
                    }); 

                    $('#period_to').datepicker({
                        startDate : start,
                        endDate   : end
                    // update "fromDate" defaults whenever "toDate" changes
                    }).on('changeDate', function(){
                        // set the "fromDate" end to not be later than "toDate" starts:
                        $('#period_by').datepicker('setEndDate', new Date($(this).val()));
                    });

                $('.selectpicker').select2();
                selectRefresh();

                // });

                let i = 0;
                $("#more").click(function() {
                    // alert("sdf");
                    i++;
                    console.log($('#pd_section').html());
                    // console.info('helloo');
                    var htmlCotent = `<div class="row" id="row_`+i+`">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Prices</label>
                                                <input type="number" step=0.01 class="form-control" name="price[]">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Products List</label>
                                                <select name="product_dropdown[]" id="product_dropdown_`+i+`"
                                                    class="form-control p-2 copied_section selectpicker">
                                                    <option value="">-- Select Product --</option> 

                                                    @foreach ($all_product_list as $datas)
                                                        <option value="{{ $datas->id }}">{{ $datas->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                                <div class="form-group">
                                                    {{-- <label>sdas</label> --}}
                                                    {{--<input class="btn btn-danger remove" value="Remove" id="remove_`+i+`">--}}
                                                    <a class="btn btn-danger remove" id="remove_`+i+`"><i class="fa fa-trash" style="color: #fff;"></i></a>

                                                </div>
                                            </div> 
                                    </div>`;
                    $("#more_content").append(htmlCotent);                                        
                      selectRefresh();                                 
            });
            $(document).on('click','.btn-danger',function(el){                    
                    $(this).closest('.row').remove();                
                });
        });


            function selectRefresh() {
                $('.selectpicker').select2({
                    tags: true,
                    placeholder: "Select an Option",    
                    allowClear: true,
                    width: '100%'
                });
            }

        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                // $('.date-picker').datepicker();
                var start = new Date();
                    // set end date to max one year period:
                    var end = new Date(new Date().setYear(start.getFullYear()+1));

                    $('#period_by_cn').datepicker({
                        startDate : start,
                        endDate   : end
                    // update "toDate" defaults whenever "fromDate" changes
                    }).on('changeDate', function(){
                        // set the "toDate" start to not be later than "fromDate" ends:
                        $('#period_to_cn').datepicker('setStartDate', new Date($(this).val()));
                    }); 

                    $('#period_to_cn').datepicker({
                        startDate : start,
                        endDate   : end
                    // update "fromDate" defaults whenever "toDate" changes
                    }).on('changeDate', function(){
                        // set the "fromDate" end to not be later than "toDate" starts:
                        $('#period_by_cn').datepicker('setEndDate', new Date($(this).val()));
                    });

                $('.selectpicker_cn').select2();
                selectRefresh_cn();

                // });

                let i = 0;
                $("#more_cn").click(function() {
                    i++;
                    console.log($('#pd_section').html());
                    var htmlCotent = `<div class="row" id="row_cn`+i+`">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Prices</label>
                                                <input type="number" step=0.01 class="form-control" name="price_cn[]">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Products List</label>
                                                <select name="product_dropdown_cn[]" id="product_dropdown_cn_`+i+`"
                                                    class="form-control p-2 copied_section selectpicker_cn">
                                                    @foreach ($all_product_list_cn as $datas)
                                                        <option value="{{ $datas->id }}">{{ $datas->title_cn }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                                <div class="form-group">
                                                    <a class="btn btn-danger remove" id="remove_cn_`+i+`"><i class="fa fa-trash" style="color: #fff;"></i></a>  
                                                </div>
                                            </div> 
                                    </div>`;
                    $("#more_content_cn").append(htmlCotent);                        
                    
                      selectRefresh();  
              
            });
            $(document).on('click','.btn-danger',function(el){
                   
                    $(this).closest('.row').remove();

                });
        });


            function selectRefresh_cn() {
                $('.selectpicker_cn').select2({
                    tags: true,
                    placeholder: "Select an Option",
                    allowClear: true,
                    width: '100%'
                });
            }

        </script>
            @include('common.footer');
