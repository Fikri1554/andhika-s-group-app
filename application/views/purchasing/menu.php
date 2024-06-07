<?php
  if(!$this->session->userdata('idUserPurchase'))
  {
    redirect(base_url("purchasing"));
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="andhika group">
    <link rel="icon" href="<?php echo base_url("assets/img/andhika.gif"); ?>">

  <title>Purchasing</title>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.css"/>
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lineicons/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style-responsive.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui.css">

  <script src="<?php echo base_url();?>assets/js/jquery.js"></script>
  <!-- <script src="<?php echo base_url();?>assets/js/jquery-1.8.3.min.js"></script> -->
  <!-- <script src="https://code.jquery.com/jquery-1.9.0.js" ></script> -->
  <script type="text/javascript">
    $(document).ready(function(){
      var usrType = "<?php echo $this->session->userdata('userTypePurchase'); ?>";

      if( usrType == "administrator")
      {
        $("#idLiPurchasing").css("display","");
        $("#idLiRequest").css("display","");
        $("#idLiRequestOffice").css("display","");
        $("#idLiPrList").css("display","");
        $("#idLiApprovePr").css("display","");
        $("#idLiListPurchasing").css("display","");
        $("#idLiSetting").css("display","");
        $("#idLiQuotation").css("display","");
        $("#idLiDraftPO").css("display","");
        $("#idLiChangePass").css("display","");
      }else{
        var user = "<?php echo $this->session->userdata('userName'); ?>";

        $.post('<?php echo base_url("purchasing/cekMenuLogin"); ?>',
        { user : user },
          function(data) 
          {
            $.each(data, function(i, item)
            {
              if(item.menu == "request")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiRequest").css("display","");
              }
              else if(item.menu == "pr list")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiPrList").css("display","");
              }
              else if(item.menu == "approve pr")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiApprovePr").css("display","");
              }
              else if(item.menu == "list purchasing")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiListPurchasing").css("display","");
              }
              else if(item.menu == "quotation")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiQuotation").css("display","");
              }
              else if(item.menu == "request office")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiRequestOffice").css("display","");
              }
              else if(item.menu == "draft po")
              {
                $("#idLiPurchasing").css("display","");
                $("#idLiDraftPO").css("display","");
              }
              else if(item.menu == "change pass")
              {
                $("#idLiChangePass").css("display","");
              }
            });
          },
        "json"
        );
      }

    });
    function isNumber(evt) 
    {
      var iKeyCode = (evt.which) ? evt.which : evt.keyCode
      if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
      {
        return false;
      }
        return true;
    }
    function inputUpCase(idInput)
    {
      var valNya = "";
      valNya = $("#"+idInput).val().toUpperCase();
      $("#"+idInput).val(valNya);
    }
  </script>
   <style type="text/css">
    ul.sidebar-menu li a.active, ul.sidebar-menu li a:hover, ul.sidebar-menu li a:focus {
        background: #A70000;
    }
    ul.sidebar-menu li ul.sub li{
        background: #5F0000;
    }
  </style>
</head>
<body>
<section id="container">
  <header class="header black-bg" style="background-color:#A70000;border-bottom:1px solid #e7e7e7" id="idHeaderNya">
    <div class="sidebar-toggle-box" style="color:#fefefe;" >
      <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Menu"></div>
    </div>
    <!--logo start-->
      <a href="" class="logo"><b>ANDHIKA GROUP</b></a>
    <!--logo end-->
    <div class="nav notify-row" id="top_menu"></div>
  </header>
  <aside>
    <div id="sidebar"  class="nav-collapse" style="background-color:#545454;" >
      <!-- sidebar menu start-->
      <ul class="sidebar-menu" id="nav-accordion">
        <p class="centered"><a >
          <img src="<?php echo base_url("assets/img/iconMan.gif"); ?>" class="img-circle" width="60">
        </a></p>
        <h5 class="centered"><?php echo $this->session->userdata('fullName'); ?></h5>
        <li class="sub-menu" id="idLiPurchasing" style="display:none;">
          <a href="javascript:;" id="idMyApps">
            <i class="glyphicon glyphicon-shopping-cart"></i>
            <span>Purchasing</span>
          </a>
          <ul class="sub">
            <li id="idLiRequest" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('request/'); ?>"><i class="fa fa-pencil-square-o"></i> Request</a>
            </li>
            <li id="idLiRequestOffice" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('requestOffice/'); ?>"><i class="fa fa-pencil-square"></i> Request Office</a>
            </li>
            <li id="idLiPrList" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('listRequest/getListRequest'); ?>"><i class="glyphicon glyphicon-list-alt"></i> PR List</a>
            </li>
            <li id="idLiApprovePr" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('approve/getApprovePr'); ?>"><i class="fa fa-question-circle"></i> Approve PR</a>
            </li>
            <li id="idLiQuotation" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('offered/getListOffer'); ?>"><i class="fa fa-inbox"></i> Entry Quotation</a>
            </li>
            <li id="idLiDraftPO" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('approve/getApproveDraftPo'); ?>"><i class="fa fa-inbox"></i> Draft PO </a>
            </li>
            <li id="idLiListPurchasing" style="padding-left:25px;display:none;">
              <a href="<?php echo base_url('purchasing/getListPurchasing'); ?>"><i class="glyphicon glyphicon-list-alt"></i> List Purchasing</a>
            </li>
          </ul>
        </li>
        <li class="sub-menu" id="idLiSetting" style="display:none;">
            <a href="javascript:;" id="idSetting">
              <i class="fa fa-cogs"></i>
              <span>Setting</span>
            </a>
            <ul class="sub">
              <li><a href="<?php echo base_url('setting/getUserPurchase'); ?>">User</a></li>
            </ul>
            <ul class="sub">
              <li><a href="<?php echo base_url('setting/userSetting'); ?>">User Setting</a></li>
            </ul>
        </li>
        <li class="sub-menu" id="idLiChangePass" style="display:none;">
            <a href="<?php echo base_url('purchasing/getChangePass') ?>" id="idChangePass">
              <i class="fa fa-unlock"></i>
              <span>Change Password</span>
            </a>
        </li>
        <li>
          <a class="logout" href="<?php echo base_url('purchasing/logout'); ?>" >
           <i class="fa fa-lock"></i>
             <span>Logout</span>
          </a>
       </li>
      </ul>
      <!-- sidebar menu end-->
    </div>
  </aside>
</section>
</body>
</html>

<script src="<?php echo base_url();?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.dcjqaccordion.2.7.js" class="include" type="text/javascript" ></script>
<script src="<?php echo base_url();?>assets/js/jquery.scrollTo.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.sparkline.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="<?php echo base_url();?>assets/js/common-scripts.js"></script>
    
  
  
