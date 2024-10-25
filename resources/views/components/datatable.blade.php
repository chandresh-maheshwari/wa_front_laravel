<div>

    {{-- {{dd($tablevar);}} --}}

    @if ($tablevar['TableHader'] != 'hide')
        <div class="card-header">
            <h5 class="title">{{ $tablevar['TableHader'] }}</h5>
        </div>
    @endif
    @if (!empty($tablevar['rightActionButton']))
    <div class="dt-buttons" id="action_filter1">

        {{-- @if (in_array('deleteRecordButton', $tablevar['rightActionButton']))
        <a class="dt-button buttons-html5btn btn btn-primary" id="showDeletedRecordBtn" href="javascript:void(0);" onclick="showDeletedRecord()" title="Show Deleted Records"><i class="fa fa-eye-slash"></i></a>
        @endif --}}
        @if ($tablevar['TableDeleteRoute'] != null)

        @if (in_array('deleteButton', $tablevar['rightActionButton']))
        <a class="dt-button buttons-html5btn btn btn-primary" id="delete_record" onclick="deleteRecord('{{ route($tablevar['TableDeleteRoute']) }}', '{{ '#' . $tablevar['TableId'] }}')" href="javascript:void(0);" title="Delete Selected Data"><i class="fa fa-trash"></i></a>
        @endif
        @endif
{{-- Add Data --}}
@if (in_array('createButton', $tablevar['rightActionButton']))
<a href="{{ route($tablevar['TableCreateRoute'], $RouteParam) }}" class="dt-button buttons-html5btn btn btn-primary" title="Add New {{ $tablevar['TableHader'] }}"><i class="fa fa-plus"></i></a>
@endif
        {{-- @if ($tablevar['TableDeleteRoute'] != null)
        <a class="dt-button buttons-html5btn btn btn-primary perm_delete_record" id="perm_delete_record" onclick="permanentlyDelete(this, '{{ '#' . $tablevar['TableId'] }}' ,'{{ route($tablevar['TableDeleteRoute']) }}')" href="javascript:void(0);" title="Permanently Delete Selected Data" style="display: none"><i class="fa fa-calendar-times-o"></i></a>
        @endif

        @if ($tablevar['TableRestoreRoute'] != null)
        <a class="dt-button buttons-html5btn btn btn-primary restore_record" id="restore_record" onclick="restoreRecord(this, '{{ '#' . $tablevar['TableId'] }}' ,'{{ route($tablevar['TableRestoreRoute']) }}')" href="javascript:void(0);" title="Restore Selected Data" style="display: none"><i class="fa fa-undo"></i></a>
        @endif --}}

        {{-- Add Data --}}
        {{-- @if (in_array('createButton', $tablevar['rightActionButton']))
        <a href="{{ route($tablevar['TableCreateRoute'], $RouteParam) }}" class="dt-button buttons-html5btn btn btn-primary" title="Add New {{ $tablevar['TableHader'] }}"><i class="fa fa-plus"></i></a>
        @endif --}}
    </div>
    @endif

</div>


    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        {{-- {{dd("FDsdfsdf")}} --}}
                        <div class="table" id="hi">
                            <table class="table table-condensed table-striped table-bordered jambo_table bulk_action table-hover no-margin"
                            width="100%" cellspacing="0" id="{{$tablevar['TableId']}}">
                                <thead>
                                    <tr>
                                        @foreach ($tablevar['TableColumnName'] as $item)
                                        @if ($item == 'CHECKBOX')
                                        <th class="wd-15p"><input type="checkbox" class="checkall" id="checkall"></th>
                                        @continue
                                        @endif
                                        <th>{{ $item }}</th>
                                    @endforeach
                                    </tr>
                                </thead>
                                {{-- <tr>
                                  

                                </tr> --}}
                            </table>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>



    <script>
        // $(document).ready(function () {

        //   var tableId = '{{ $tablevar['TableId'] }}' ;
        //   alert(tableId);
        //     $(tableId).dataTable();

        // ;
        // });

        // $(document).ready(function () {

        // var tableId = '{{ $tablevar['TableId'] }}';
        // $(tableId).dataTable();
        // });
        // $(document).ready(function () {

// var tableId = '#{{$tablevar['TableId']}}';

// alert(tableId);
// tableId.dataTable();

// $('#oooo').dataTable();

// });
    </script>
    <!-- The whole future lies in uncertainty: live immediately. - Seneca -->
</div>
