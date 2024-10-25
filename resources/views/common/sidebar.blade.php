<div class="sidebar" data-color="orange">
    <!--
        Tip 1: You can change the color of the sidebar using: data-color="blue | green | orange | red | yellow"
      -->
  <div class="logo">
    <img src="images/WasteAccountant_LOGO_White 1.png" width="100%">
    {{-- <a href="http://www.creative-tim.com" class="simple-text logo-normal">
      Modern Technology
    </a> --}}
  </div>
  <div class="sidebar-wrapper" id="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item nav-dropdown">
        <!-- <a href="./dashboard"> -->
        <a href="{{url('dashboard')}}">

          <i class="now-ui-icons"></i>
          <p>Dashboard</p>
        </a>
      </li>
      <!-- <li>
            <a href="./product">
            
              <p>Product</p>
            </a>
          </li> -->
      <li class="nav-item nav-dropdown">
        <a id='product' class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Package</a>
        <ul class="nav-dropdown-items">
          <!-- <li class="nav-item"><a class="nav-link" href="./product_add"><i class="nav-icon la la-user"></i> <span>Add Product</span></a></li> -->
          <li class="nav-item"><a class="nav-link" href="{{url('/packages')}}"><i class="nav-icon la la-user"></i> <span>Add Package</span></a></li>

          <!-- <li class="nav-item"><a class="nav-link" href="./product_list"><i class="nav-icon la la-group"></i> <span>Product List</span></a></li> -->
          <li class="nav-item"><a class="nav-link" href="{{url('/package_list')}}"><i class="nav-icon la la-group"></i> <span>Package List</span></a></li>

        </ul>
      </li>
      <li class="nav-item nav-dropdown">
        <a id='promotion' class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Services</a>
        <ul class="nav-dropdown-items-promotion">
          <!-- <li class="nav-item"><a class="nav-link" href="./promocode_add"><i class="nav-icon la la-user"></i> <span>Add Promocode</span></a></li> -->
          <li class="nav-item"><a class="nav-link" href="{{url('/services')}}"><i class="nav-icon la la-user"></i> <span>Add Services</span></a></li>

          <!-- <li class="nav-item"><a class="nav-link" href="./promocode_datatable"><i class="nav-icon la la-group"></i> <span>Promocode List</span></a></li> -->
          <li class="nav-item"><a class="nav-link" href="{{url('/service_list')}}"><i class="nav-icon la la-group"></i> <span>Services List</span></a></li>

        </ul>
      </li>
      <li>
          <a id='client' class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Client</a>
          <ul class="nav-dropdown-items-client">
            <li class="nav-item"><a class="nav-link" href="{{url('client')}}"><i class="#"></i> <span>Add client</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{url('/clist')}}"><i class="#"></i> <span>Client List</span></a></li>
          </ul>
      </li>
      <li class="nav-item nav-dropdown">
        <a id='testimonial' class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Testimonials</a>
        <ul class="nav-dropdown-items-testimonial">
          <li class="nav-item"><a class="nav-link" href="{{url('testionmonial')}}"><i class="#"></i> <span>Add Testimonial </span></a></li>
          <li class="nav-item"><a class="nav-link" href="{{url('/list')}}"><i class="#"></i> <span>Testimonial List</span></a></li>
        </ul>
      </li>
      <li>
        <a href="./notifications.html">

          <p>Products</p>
        </a>
      </li>
      {{-- <li>
        <a href="./user.html">

          <p>Customer List</p>
        </a>
      </li> --}}
      {{-- <li>
        <a href="./tables.html">

          <p>Promotion List</p>
        </a>
      </li> --}}
      <li>
        <a href="./typography.html">

          <p>Users</p>
        </a>
      </li>

    </ul>
  </div>
</div>
<style>
  .nav-dropdown-items {
    display: none;
  }

  .nav-dropdown-items {
    list-style: none;
  }


  .nav-dropdown-items-promotion {
    display: none;
    list-style: none;
  }
  .nav-dropdown-items-client {

    display: none;
    list-style: none;
  }
  .nav-dropdown-items-testimonial{

    display: none;
    list-style: none;
  }

  /* .nav-dropdown-items {
  } */
  /* .nav-dropdown-toggle a:hover .nav-dropdown-items{
    display: block; 
  } */
</style>
<script>
  $("#product").click(function() {
    $(".nav-dropdown-items").toggle();
  });

  $("#promotion").click(function() {
    $(".nav-dropdown-items-promotion").toggle();
  });
  $("#testimonial").click(function() {
    $(".nav-dropdown-items-testimonial").toggle();
  });
  $("#client").click(function() {
    $(".nav-dropdown-items-client").toggle();
  });
</script>