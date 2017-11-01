@extends ('orbit.master')

@section ('content')
    <!-- BEGIN PAGE HEADER-->
    <div class="portlet ">
        <h1 class="page-title col-xs-10"> Inbound Referrals </h1>
        <br>
    </div>
    <!-- END PAGE HEADER-->

    <!-- Begin: Datatable referrals -->
    <div class="portlet light portlet-fit portlet-datatable ">
        <div class="portlet-body">
            <div class="table-container">
                <div class="table-actions-wrapper">
                    <div id="service_filter" class="dataTables_filter"><label>Search: <input type="search" id="search_box" class="" placeholder="" aria-controls="service"></label></div>
                </div>
                <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax_referrals">
                    <thead class="table-head-inbound">
                        <tr role="row" class="heading">                            
                            <th colspan="5" class="text-left-padding">Details</th>
                            <th colspan="2">To</th>
                            <th colspan="1">From</th>
                        </tr>
                        <tr role="row" class="heading">
                            <th width="5%"> Id </th>
                            <th width="10%"> Date </th>
                            <th width="20%"> Legal Issue </th>
                            <th width="20%"> Location </th>
                            <th width="20%"> Reason </th>
                            <th width="20%"> Service Provider </th>
                            <th width="20%"> Service Name </th>
                            <th width="20%"> Service Provider </th>
                        </tr>
                    </thead>
                    <tbody class="table-body-inbound"> </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End: Datatable referrals -->
@endsection