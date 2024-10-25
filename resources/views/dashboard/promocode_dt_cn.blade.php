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
                                <h4 class="card-title"> Promotion List</h4>
                            </div>
                            <div class="pull-right pr-3">
                                <a href="/promocode_datatable">English</a> | <a href="/promocode_datatable/cn"> 中文 (香港)</a>
                            </div>
                            <div class="card-body">
                                <div class="table" id="hi">
                                    <table class="table" id="promocode_datatable">
                                        <thead class=" text-primary">
                                            <th>Sr.No</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Validity Period (by)</th>
                                            <th>Validity Period (To)</th>
                                            <th>Action</th>

                                        </thead>
                                        <tbody>
                                            @php                                      
                                                $count = 0;                                          
                                            @endphp
                                            
                                            @foreach ($data as $item)
                                                @if($item->code_cn !== null)
                                                @php  $count++; @endphp
                                                <tr>
                                                    <td>{{ $count }}</td>
                                                    <td>{{ $item->code_cn }}</td>
                                                    <td>{{ $item->name_cn }}</td>
                                                    <td>{{ $item->period_by_cn }}</td>
                                                    <td>{{ $item->period_to_cn }}</td>
                                                   

                                                    <td class='action'>
                                                        <a class="btn btn-info btn-sm"
                                                            href="/promocode_update/{{ $item->id }}"><i
                                                                class="fa fa-edit" style="color: #fff;"></i></a>
                                                        <a class="btn btn-danger btn-sm delete-confirm"
                                                            href="/promocode_delete/{{ $item->id }}"><i
                                                                class="fa fa-trash" style="color: #fff;"></i></a>
                                                    </td>

                                                </tr>                                              
                                                @endif
                                            @endforeach
                                           
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
                    $('#promocode_datatable').DataTable();
                });
            </script>
            @include('common.footer');
