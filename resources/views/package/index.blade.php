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
                            {{-- <div class="card-header"> --}}
                            {{-- <h5 class="title">Add Client</h5> --}}
                            {{-- </div> --}}
                            {{-- <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#english">English</a></li>
                                <li><a data-toggle="tab" href="#chinese">chinese</a></li>
                            </ul> --}}
                            {{-- <div class="tab-content"> --}}
                            {{-- <div id="english" class="tab-pane fade in active"> --}}
                            <div class="card-body">

                                @php
                                    $DatbleVariable['TableHader'] = 'Package';
                                    $DatbleVariable['TableId'] = 'package';
                                    $DatbleVariable['TableCreateRoute'] = 'package_create';
                                    $DatbleVariable['TableDeleteRoute'] = 'package.destroy';
                                    $DatbleVariable['TableColumnName'] = ['CHECKBOX','ID', 'PACKAGE TITLE', 'PACKAGE DESCRIPTION', ' Package Price','ADDITIONAL INFO','ACTION'];
                                    $DatbleVariable['rightActionButton'] = ['deleteButton','createButton'];
                                @endphp
                                <x-Datatable :tablevar=$DatbleVariable />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- {{ dd($data); }}   --}}
            <script>    
                $(document).ready(function() {


                    let tableId = "#package";
                    let route = '{{ route('package.listing') }}';
                    let method = "POST";
                    let pagination = true;
                    let deleteRoute = '{{ route('package.destroy') }}';
                    DatatableRenderFunction_ss(tableId, bottomInfo = true,id = null, pagination = false, route, method, location,deleteRoute);
                });
            </script>

            @include('common.footer');
