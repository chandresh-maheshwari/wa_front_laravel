@include('common.header')
@include('common.sidebar')


<body class="">
  <div class="wrapper ">
    
    <div class="main-panel" id="main-panel">
      @include('common.navbar')
      
      <br>
      <div class="content mt-5">
        <h4 class="text-warning text-center mt-1 font-weight-bold">Welcome to WasAccountant Dashboard
        </h4>
		{{-- <div class="row">
		
          <div class="col-lg-4">
            <div class="card card-chart">
              <div class="card-header">
             
                <h6 class="card-title">UBX - UBOX</h6>
				<img src="#" style="height:250px;width:250px;"/>
               <h1 style="height:250px;width:250px;">Hello</h1>
              </div>
             
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="card card-chart">
              <div class="card-header">
               <h6 class="card-title">HKU - Hong Kong University</h6>
              <img src="#" style="height:250px;width:250px;"/>
              <h1 style="height:250px;width:250px;">Hello</h1>
              </div>
             
             
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="card card-chart">
              <div class="card-header">
              
                <h6 class="card-title">HKBU - Hong Kong Baptist 

</h6>
<img src="https://www.pngkey.com/png/full/287-2878016_man-moving-boxes-royalty-free-vector-clip-art.png"style="height:250px;width:250px;"/>
<h1 style="height:250px;width:250px;">Hello</h1>
              </div>
             
             
            </div>
          </div>
        </div>
        --}}
         
     
      <div class="content mt-1">
       
		{{-- <div class="row">
		
          <div class="col-lg-4">
            <div class="card card-chart">
              <div class="card-header">
             
                <h6 class="card-title">UBX - UBOX</h6>
				<img src="https://www.pngall.com/wp-content/uploads/2017/05/Danbo-PNG-Clipart.png" style="height:250px;width:250px;"/>
        <h1 style="height:250px;width:250px;">Hello</h1>
              </div>
             
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="card card-chart">
              <div class="card-header">
               <h6 class="card-title">HKU - Hong Kong University</h6>
              <img src="https://www.freepnglogos.com/uploads/box-png/box-rsc-boxes-benefit-shipping-and-retail-cactus-containers-12.png" style="height:250px;width:250px;"/>
              <h1 style="height:250px;width:250px;">Hello</h1>
              </div>
             
             
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="card card-chart">
              <div class="card-header">
              
                <h6 class="card-title">HKBU - Hong Kong Baptist 

</h6>
<img src="https://static.thenounproject.com/png/635130-200.png" style="height:250px;width:250px;"/>
<h1 style="height:250px;width:250px;">Hello</h1>
              </div>
             
             
            </div>
          </div>
        </div>
        --}}
         
     
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js"></script>
  <script src="{{url('/assets/js/core/popper.min.js')}}"></script>
  <script src="{{url('/assets/js/core/bootstrap.min.js')}}"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chart JS -->
  <script src="../assets/js/plugins/chartjs.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/now-ui-dashboard.min.js?v=1.5.0" type="text/javascript"></script><!-- Now Ui Dashboard DEMO methods, don't include it in your project! -->
  <script src="../assets/demo/demo.js"></script>
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      demo.initDashboardPageCharts();

    });
  </script>
</body>

</html>