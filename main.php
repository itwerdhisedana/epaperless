<?php
error_reporting(0);
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
require 'PHPMailer-master/PHPMailerAutoload.php';
function slugify($text){
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);
  // trim
  $text = trim($text, '-');
  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);
  // lowercase
  $text = strtolower($text);
  if (empty($text)) {
    return 'n-a';
  }
  return $text;
}
if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
	$CekAkses = $this->model->selectprepare("user_level", $field=null, $params=null, $where=null, "WHERE id_user='$_SESSION[id_user]'");
	$HakAkses = $CekAkses->fetch(PDO::FETCH_OBJ);?>
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			<meta charset="utf-8" />
			<title>Sistem Informasi Arsip Surat - SIAS</title>

			<meta name="description" content="overview &amp; stats" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
			
			<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
			<link rel="icon" href="/favicon.ico" type="image/x-icon">

			<!-- bootstrap & fontawesome -->
			<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
			<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

			<!-- page specific plugin styles -->
			<link rel="stylesheet" href="assets/css/bootstrap-datepicker3.min.css" />
			<link rel="stylesheet" href="assets/css/daterangepicker.min.css" />
			<link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css" />

			<!-- text fonts -->
			<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

			<!-- ace styles -->
			<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

			<!--[if lte IE 9]>
				<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
			<![endif]-->
			<link rel="stylesheet" href="assets/css/ace-skins.min.css" />
			<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

			<!--[if lte IE 9]>
			  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
			<![endif]-->

			<!-- inline styles related to this page -->

			<!-- ace settings handler -->
			<script src="assets/js/ace-extra.min.js"></script>
			
			<!-- multiple choose combobox-->
			<link rel="stylesheet" href="assets/css/chosen.min.css" />

			<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

			<!--[if lte IE 8]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
			<![endif]-->
		</head>

		<body class="no-skin">
			<div id="navbar" class="navbar navbar-default ace-save-state">
				<div class="navbar-container ace-save-state" id="navbar-container">
					<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
						<span class="sr-only">Toggle sidebar</span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>

						<span class="icon-bar"></span>
					</button>

					<div class="navbar-header pull-left">
						<a href="./" class="navbar-brand">
							<small>
								<i class="fa fa-leaf"></i>
								SIAS
							</small>
						</a>
					</div>

					<div class="navbar-buttons navbar-header pull-right" role="navigation">
						<ul class="nav ace-nav">
							<li class="purple dropdown-modal">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#"><?php
									$cekSM = $this->model->selectprepare("arsip_sm a", $field=null, $params=null, $where=null, "WHERE a.tujuan_surat LIKE '%\"$_SESSION[id_user]\"%' AND a.id_sm NOT IN (SELECT b.id_sm FROM surat_read b WHERE b.id_user='$_SESSION[id_user]' AND b.kode='SM')");
									
									$cekSM2 = $this->model->selectprepare("arsip_sm a", $field=null, $params=null, $where=null, "WHERE a.tujuan_surat LIKE '%\"$_SESSION[id_user]\"%' AND a.id_sm NOT IN (SELECT b.id_sm FROM surat_read b WHERE b.id_user='$_SESSION[id_user]' AND b.kode='SM')", "ORDER BY a.tgl_terima DESC LIMIT 3");
									$teks = "Surat masuk baru";
									$teks1 = "Lihat semua surat";
									$link="./index.php?op=memo";
									while($dataSM= $cekSM2->fetch(PDO::FETCH_OBJ)){
										$dumpSM[]=$dataSM;
									}
									if($cekSM->rowCount() >= 1){?>
										<i class="ace-icon fa fa-envelope-o icon-animated-bell"></i>
										<span class="badge badge-important"><?php echo $cekSM->rowCount();?></span><?php
									}else{?>
										<i class="ace-icon fa fa-envelope-o"></i><?php
									}?>
								</a>
								<ul class="dropdown-menu-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
									<li class="dropdown-header">
										<i class="ace-icon fa fa-exclamation-triangle"></i><?php
										if($cekSM->rowCount() >= 1){?>
											<?php echo $cekSM->rowCount();?> <?php echo $teks;
										}else{
											echo "Tidak ada ".$teks;
										}?>
									</li>
									<li class="dropdown-content">
										<ul class="dropdown-menu dropdown-navbar navbar-pink"><?php
											if($cekSM->rowCount() >= 1){
												foreach($dumpSM as $key => $object){
													$params = array(':id_user' => $object->id_user);
													$cek_pengirim = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $order=null);
													$data_cek_pengirim = $cek_pengirim->fetch(PDO::FETCH_OBJ);?>
													<li>
														<a href="./index.php?op=memo&memoid=<?php echo $object->id_sm;?>" class="clearfix">
															<img src="assets/images/avatars/<?php echo $data_cek_pengirim->picture;?>" class="msg-photo" alt="User" />
															<span class="msg-body">
																<span class="msg-title">
																	<span class="blue"><?php echo $data_cek_pengirim->nama;?></span>
																	<?php echo $object->perihal;?>
																</span>
																<span class="msg-time">
																	<i class="ace-icon fa fa-clock-o"></i>
																	<span><?php echo tgl_indo($object->tgl_terima);?></span>
																</span>
															</span>
														</a>
													</li><?php
												}
											}?>
										</ul>
									</li>
									<li class="dropdown-footer">
										<a href="<?php echo $link;?>">
											<?php echo $teks1;?>
											<i class="ace-icon fa fa-arrow-right"></i>
										</a>
									</li>
								</ul>
							</li>
							<li class="grey dropdown-modal">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#"><?php
									$field = array("a.id_user as userDis","a.*","b.*");
									$cekDispo = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.disposisi LIKE '%\"$_SESSION[id_user]\"%' AND a.id_sm NOT IN (SELECT c.id_sm FROM surat_read c WHERE c.id_user='$_SESSION[id_user]' AND c.kode='DIS')");
									
									$cekDispo2 = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.disposisi LIKE '%\"$_SESSION[id_user]\"%' AND a.id_sm NOT IN (SELECT c.id_sm FROM surat_read c WHERE c.id_user='$_SESSION[id_user]' AND c.kode='DIS') ORDER BY b.tgl_terima DESC LIMIT 3");
									
									$teks = "Disposisi baru";
									$teks1 = "Lihat semua dispposisi";
									$link="./index.php?op=disposisi";
									while($dataDispo= $cekDispo2->fetch(PDO::FETCH_OBJ)){
										$dumpDispo[]=$dataDispo;
									}
									if($cekDispo->rowCount() >= 1){?>
										<i class="ace-icon fa fa-external-link-square icon-animated-vertical"></i>
										<span class="badge badge-important"><?php echo $cekDispo->rowCount();?></span><?php
									}else{?>
										<i class="ace-icon fa fa-external-link-square"></i><?php
									}?>
								</a>

								<ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
									<li class="dropdown-header">
										<i class="ace-icon fa fa-check"></i><?php
										if($cekDispo->rowCount() >= 1){?>
											<?php echo $cekDispo->rowCount();?> <?php echo $teks;
										}else{
											echo "Tidak ada ".$teks;
										}?>
									</li>

									<li class="dropdown-content">
										<ul class="dropdown-menu dropdown-navbar"><?php
											if($cekDispo->rowCount() >= 1){
												foreach($dumpDispo as $key => $object){
													$params = array(':id_user' => $object->userDis);
													$cek_pengirim = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $order=null);
													$data_cek_pengirim = $cek_pengirim->fetch(PDO::FETCH_OBJ);
													$tgl_disposisi= substr($object->tgl,0,10);?>
													<li>
														<a href="./index.php?op=disposisi&smid=<?php echo $object->id_sm;?>" class="clearfix">
															<img src="assets/images/avatars/<?php echo $data_cek_pengirim->picture;?>" class="msg-photo" alt="User" />
															<span class="msg-body">
																<span class="msg-title">
																	<span class="blue"><?php echo $data_cek_pengirim->nama;?></span>
																	<?php echo $object->perihal;?>
																</span>
																<span class="msg-time">
																	<i class="ace-icon fa fa-clock-o"></i>
																	<span><?php echo tgl_indo($tgl_disposisi);?></span>
																</span>
															</span>
														</a>
													</li><?php
												}
											}?>
										</ul>
									</li>

									<li class="dropdown-footer">
										<a href="<?php echo $link;?>">
											<?php echo $teks1;?>
											<i class="ace-icon fa fa-arrow-right"></i>
										</a>
									</li>
								</ul>
							</li>
							<li class="green dropdown-modal">
								<a data-toggle="dropdown" class="dropdown-toggle" href="#"><?php
									$field = array("a.id_user as userDis","a.*","b.*");
									$cekTembusan = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.tembusan LIKE '%\"$_SESSION[id_user]\"%' AND a.id_sm NOT IN (SELECT c.id_sm FROM surat_read c WHERE c.id_user='$_SESSION[id_user]' AND c.kode='CC')");
									
									$cekTembusan2 = $this->model->selectprepare("memo a join arsip_sm b on a.id_sm=b.id_sm", $field, $params=null, $where=null, "WHERE a.tembusan LIKE '%\"$_SESSION[id_user]\"%' AND a.id_sm NOT IN (SELECT c.id_sm FROM surat_read c WHERE c.id_user='$_SESSION[id_user]' AND c.kode='CC') ORDER BY a.tgl DESC LIMIT 3");
									
									$teks = "Tembusan surat baru";
									$teks1 = "Lihat tembusan surat";
									$link="./index.php?op=sm";
									while($dataTembusan = $cekTembusan2->fetch(PDO::FETCH_OBJ)){
										$dumpTembusan[]=$dataTembusan;
									}
									if($cekTembusan->rowCount() >= 1){?>
										<i class="ace-icon fa fa-repeat icon-animated-bell"></i>
										<span class="badge badge-success"><?php echo $cekTembusan->rowCount();?></span><?php
									}else{?>
										<i class="ace-icon fa fa-repeat"></i><?php
									}?>
								</a>
								<ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
									<li class="dropdown-header">
										<i class="ace-icon fa fa-envelope-o"></i><?php
										if($cekTembusan->rowCount() >= 1){?>
											<?php echo $cekTembusan->rowCount();?> <?php echo $teks;
										}else{
											echo "Tidak ada ".$teks;
										}?>
									</li>
									<li class="dropdown-content">
										<ul class="dropdown-menu dropdown-navbar"><?php
											if($cekTembusan->rowCount() >= 1){
												foreach($dumpTembusan as $key => $object){
													$params = array(':id_user' => $object->userDis);
													$cek_pengirim = $this->model->selectprepare("user", $field=null, $params, "id_user=:id_user", $order=null);
													$data_cek_pengirim = $cek_pengirim->fetch(PDO::FETCH_OBJ);
													$tgl_tembusan= substr($object->tgl,0,10);?>
													<li>
														<a href="./index.php?op=sm&smid=<?php echo $object->id_sm;?>" class="clearfix">
															<img src="assets/images/avatars/<?php echo $data_cek_pengirim->picture;?>" class="msg-photo" alt="User" />
															<span class="msg-body">
																<span class="msg-title">
																	<span class="blue"><?php echo $data_cek_pengirim->nama;?></span>
																	<?php echo $object->perihal;?>
																</span>
																<span class="msg-time">
																	<i class="ace-icon fa fa-clock-o"></i>
																	<span><?php echo tgl_indo($tgl_tembusan);?></span>
																</span>
															</span>
														</a>
													</li><?php
												}
											}?>
										</ul>
									</li>

									<li class="dropdown-footer">
										<a href="<?php echo $link;?>">
											<?php echo $teks1;?>
											<i class="ace-icon fa fa-arrow-right"></i>
										</a>
									</li>
								</ul>
							</li>

							<li class="light-blue dropdown-modal">
								<a data-toggle="dropdown" href="#" class="dropdown-toggle">
									<img class="nav-user-photo" src="assets/images/avatars/<?php echo $_SESSION['picture'];?>" alt="SIAS" />
									<span class="user-info">
										<small>Welcome,</small>
										<?php echo $_SESSION['nama'];?>
									</span>

									<i class="ace-icon fa fa-caret-down"></i>
								</a>

								<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close"><?php
									if($_SESSION['hakakses'] == "Admin"){?>
									<!--<li>
										<a href="#">
											<i class="ace-icon fa fa-cog"></i>
											Settings
										</a>
									</li>-->
									<li>
										<a href="./index.php?op=user">
											<i class="ace-icon fa fa-user"></i>
											User
										</a>
									</li><?php
									}?>
										
									<li class="divider"></li>
									<li>
										<a href="./index.php?op=profil">
											<i class="ace-icon fa fa-user"></i>
											Profile
										</a>
									</li>
									<li>
										<a href="./keluar">
											<i class="ace-icon fa fa-power-off"></i>
											Logout
										</a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div><!-- /.navbar-container -->
			</div>

			<div class="main-container ace-save-state" id="main-container">
				<script type="text/javascript">
					try{ace.settings.loadState('main-container')}catch(e){}
				</script>

				<div id="sidebar" class="sidebar                  responsive                    ace-save-state">
					<script type="text/javascript">
						try{ace.settings.loadState('sidebar')}catch(e){}
					</script>
					
					<!--
					<div class="sidebar-shortcuts" id="sidebar-shortcuts">
						<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
							<button class="btn btn-success">
								<i class="ace-icon fa fa-signal"></i>
							</button>

							<button class="btn btn-info">
								<i class="ace-icon fa fa-pencil"></i>
							</button>

							<button class="btn btn-warning">
								<i class="ace-icon fa fa-users"></i>
							</button>

							<button class="btn btn-danger">
								<i class="ace-icon fa fa-cogs"></i>
							</button>
						</div>

						<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
							<span class="btn btn-success"></span>

							<span class="btn btn-info"></span>

							<span class="btn btn-warning"></span>

							<span class="btn btn-danger"></span>
						</div>
					</div><!-- /.sidebar-shortcuts -->

					<ul class="nav nav-list"><?php
						if(isset($_GET['op']) AND ($_GET['op'] == "sm" OR $_GET['op'] == "add_sm")){
							$StatSM = 'active open';
							if($_GET['op'] == "sm"){ $StatDataSM = 'active'; }else{ $StatDataSM = ''; }
							if($_GET['op'] == "add_sm"){ $StatEntriSM = 'active'; }else{ $StatEntriSM = ''; }
						}else{
							$StatSM = '';
						}
						if(isset($_GET['op']) AND ($_GET['op'] == "sk" OR $_GET['op'] == "add_sk")){
							$StatSK = 'active open';
							if($_GET['op'] == "sk"){ $StatDataSK = 'active'; }else{ $StatDataSK = ''; }
							if($_GET['op'] == "add_sk"){ $StatEntriSK = 'active'; }else{ $StatEntriSK = ''; }
						}else{
							$StatSK = '';
						}
						if(isset($_GET['op']) AND ($_GET['op'] == "data_memo" OR $_GET['op'] == "add_memo")){
							$StatArsipMemo = 'active open';
							if($_GET['op'] == "data_memo"){ $StatDataMemo = 'active'; }else{ $StatDataMemo = ''; }
							if($_GET['op'] == "add_memo"){ $StatEntriMemo = 'active'; }else{ $StatEntriMemo = ''; }
						}else{
							$StatArsipMemo = '';
						}
						if(isset($_GET['op']) AND ($_GET['op'] == "report_sm" OR $_GET['op'] == "report_sk" OR $_GET['op'] == "report_disposisi" OR $_GET['op'] == "report_arsip" OR $_GET['op'] == "report_progress")){
							$StatReport = 'active open';
							if($_GET['op'] == "report_sm"){ $StatRSM = 'active'; }else{ $StatRSM = ''; }
							if($_GET['op'] == "report_sk"){ $StatRSK = 'active'; }else{ $StatRSK = ''; }
							if($_GET['op'] == "report_disposisi"){ $StatDIS = 'active'; }else{ $StatDIS = ''; }
							if($_GET['op'] == "cari_arsip"){ $StatCariArsip = 'active'; }else{ $StatCariArsip = ''; }
							if($_GET['op'] == "report_arsip"){ $StatReportArsip = 'active'; }else{ $StatReportArsip = ''; }
							if($_GET['op'] == "report_progress"){ $StatReportProgress = 'active'; }else{ $StatReportProgress = ''; }
						}else{
							$StatReport = $StatCariArsip = '';
						}
						if(isset($_GET['op']) AND ($_GET['op'] == "arsip_file")){
							$StatArsipLeader = 'active open';
							if($_GET['op'] == "arsip_file"){ $StatArsipFileView = 'active'; }else{ $StatArsipFileView = ''; }
						}else{
							$StatArsipLeader = '';
						}
						
						if(isset($_GET['op']) AND ($_GET['op'] == "arsip_file" OR $_GET['op'] == "add_arsip" OR $_GET['op'] == "cari_arsip")){
							$StatArsipFile = 'active open';
							
							if($_GET['op'] == "add_arsip"){ $StatArsipFileEntri = 'active'; }else{ $StatArsipFileEntri = ''; }
							if($_GET['op'] == "arsip_file"){ $StatArsipFileView = 'active'; }else{ $StatArsipFileView = ''; }
							
							if($_GET['op'] == "cari_arsip"){ $StatCariFile = 'active'; }else{ $StatCariFile = ''; }
						}else{
							$StatArsipFile = $StatArsipFileEntri = $StatArsipFileView = $StatCariFile = '';
						}
						
						if(isset($_GET['op']) AND ($_GET['op'] == "klasifikasi" OR $_GET['op'] == "klasifikasi_sk" OR $_GET['op'] == "user" OR $_GET['op'] == "setting" OR $_GET['op'] == "klasifikasi_file")){
							$StatAtur = 'active open';
						}else{
							$StatAtur = '';
						}
						
						if(isset($_GET['op']) AND $_GET['op'] == "arsip_sk"){ $StatArsipSK = 'active open'; }else{ $StatArsipSK = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "arsip_sm"){ $StatArsipSM = 'active open'; }else{ $StatArsipSM = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "user"){ $StatUser = 'active open'; }else{ $StatUser = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "setting"){ $StatSetting = 'active open'; }else{ $StatSetting = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "klasifikasi_file"){ $StatKlasFile = 'active open'; }else{ $StatKlasFile = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "klasifikasi"){ $StatKlasSM = 'active open'; }else{ $StatKlasSM = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "klasifikasi_sk"){ $StatKlasSK = 'active open'; }else{ $StatKlasSK = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "memo"){ $StatMemo = 'active open'; }else{ $StatMemo = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "disposisi"){ $StatDisposisi = 'active open'; }else{ $StatDisposisi = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "tembusan"){ $StatTembusan = 'active open'; }else{ $StatTembusan = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "info"){ $StatInfo = 'active open'; }else{ $StatInfo = ''; }
						if(isset($_GET['op']) AND $_GET['op'] == "info"){ $StatInfo = 'active open'; }else{ $StatInfo = ''; }
						if(!isset($_GET['op'])){
							$StatBeranda = 'active';
						}else{
							$StatBeranda = '';
						}?>
						
						
						<li class="<?php echo $StatBeranda;?>">
							<a href="./">
								<i class="menu-icon fa fa-tachometer"></i>
								<span class="menu-text"> Dashboard </span>
							</a>

							<b class="arrow"></b>
						</li>
						
						<li class="<?php echo $StatMemo;?>">
							<a href="index.php?op=memo">
								<i class="menu-icon fa fa-envelope-o"></i>
								<span class="menu-text"> Surat Masuk </span><?php
								if($cekSM->rowCount() >= 1){?>
									<span class="badge badge-warning"><?php echo $cekSM->rowCount();?></span><?php
								}?>
							</a>
							<b class="arrow"></b>
						</li>
						<li class="<?php echo $StatDisposisi;?>">
							<a href="index.php?op=disposisi">
								<i class="menu-icon fa fa-external-link-square"></i>
								<span class="menu-text"> Disposisi </span><?php
								if($cekDispo->rowCount() >= 1){?>
									<span class="badge badge-warning"><?php echo $cekDispo->rowCount();?></span><?php
								}?>
							</a>
							<b class="arrow"></b>
						</li>
						<li class="<?php echo $StatTembusan;?>">
							<a href="index.php?op=tembusan">
								<i class="menu-icon fa fa-repeat"></i>
								<span class="menu-text"> Tembusan </span><?php
								if($cekTembusan->rowCount() >= 1){?>
									<span class="badge badge-warning"><?php echo $cekTembusan->rowCount();?></span><?php
								}?>
							</a>
							<b class="arrow"></b>
						</li><?php
						/*Cek Info Memo*/
						$cekInfo = $this->model->selectprepare("info a", $field=null, $params=null, $where=null, "WHERE a.tujuan_info LIKE '%\"$_SESSION[id_user]\"%' AND a.id_info NOT IN (SELECT b.id_sm FROM surat_read b WHERE b.id_user='$_SESSION[id_user]' AND b.kode='INFO')");
						$cekInfo2 = $this->model->selectprepare("info a", $field=null, $params=null, $where=null, "WHERE a.tujuan_info LIKE '%\"$_SESSION[id_user]\"%' AND a.id_info NOT IN (SELECT b.id_sm FROM surat_read b WHERE b.id_user='$_SESSION[id_user]' AND b.kode='INFO')", "ORDER BY a.tgl_info DESC LIMIT 6");
						$teks = "Memo masuk baru";
						$teks1 = "Lihat semua memo";
						$link="./index.php?op=memo";
						while($DataInfo= $cekInfo2->fetch(PDO::FETCH_OBJ)){
							$dumpInfo[]=$DataInfo;
						}?>
						<li class="<?php echo $StatInfo;?>">
							<a href="index.php?op=info"><i class="menu-icon fa fa-weixin"></i>
								<span class="menu-text"> Memo </span><?php
								if($cekInfo->rowCount() >= 1){?>
									<span class="badge badge-warning"><?php echo $cekInfo->rowCount();?></span><?php
								}?>
							</a>
							<b class="arrow"></b>
						</li><?php
						if($HakAkses->sm == "W"){?>
							<li class="<?php echo $StatSM;?>">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-inbox"></i>
									<span class="menu-text">
										Arsip Surat Masuk
									</span>
									<b class="arrow fa fa-angle-down"></b>
								</a>
								<b class="arrow"></b>
								<ul class="submenu">
									<li class="<?php echo $StatEntriSM;?>">
										<a href="./index.php?op=add_sm">
											<i class="menu-icon fa fa-caret-right"></i>
											Entri baru
										</a>
										<b class="arrow"></b>
									</li>
									<li class="<?php echo $StatDataSM;?>">
										<a href="./index.php?op=sm">
											<i class="menu-icon fa fa-caret-right"></i>
											Data surat masuk
										</a>
										<b class="arrow"></b>
									</li>
								</ul>
							</li><?php
						}
						if($HakAkses->sm == "R"){?>
							<li class="<?php echo $StatArsipSM;?>">
								<a href="./index.php?op=arsip_sm">
									<i class="menu-icon fa fa-inbox"></i>
									<span class="menu-text"> Arsip Surat Masuk</span>
								</a>
								<b class="arrow"></b>
							</li><?php
						}
						if($HakAkses->sk == "W"){?>
							<li class="<?php echo $StatSK;?>">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-send-o"></i>
									<span class="menu-text">
										Arsip Surat Keluar
									</span>
									<b class="arrow fa fa-angle-down"></b>
								</a>
								<b class="arrow"></b>
								<ul class="submenu">
									<li class="<?php echo $StatEntriSK;?>">
										<a href="./index.php?op=add_sk">
											<i class="menu-icon fa fa-caret-right"></i>
											Entri baru
										</a>
										<b class="arrow"></b>
									</li>
									<li class="<?php echo $StatDataSK;?>">
										<a href="./index.php?op=sk">
											<i class="menu-icon fa fa-caret-right"></i>
											Data surat keluar
										</a>
										<b class="arrow"></b>
									</li>
								</ul>
							</li><?php
						}
						if($HakAkses->sk == "R"){?>
							<li class="<?php echo $StatArsipSK;?>">
								<a href="./index.php?op=arsip_sk">
									<i class="menu-icon fa fa-tags"></i>
									<span class="menu-text"> Arsip Surat Keluar</span>
								</a>
								<b class="arrow"></b>
							</li><?php
						}
						if($HakAkses->info == "Y"){?>
							<li class="<?php echo $StatArsipMemo;?>">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-flickr"></i>
									<span class="menu-text">
										Arsip Memo
									</span>
									<b class="arrow fa fa-angle-down"></b>
								</a>
								<b class="arrow"></b>
								<ul class="submenu">
									<li class="<?php echo $StatEntriMemo;?>">
										<a href="./index.php?op=add_memo">
											<i class="menu-icon fa fa-caret-right"></i>
											Entri baru
										</a>
										<b class="arrow"></b>
									</li>
									<li class="<?php echo $StatDataMemo;?>">
										<a href="./index.php?op=data_memo">
											<i class="menu-icon fa fa-caret-right"></i>
											Data Memo
										</a>
										<b class="arrow"></b>
									</li>
								</ul>
							</li><?php
						}
						if($HakAkses->arsip == "W"){?>						
							<li class="<?php echo $StatArsipFile;?>">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-th-large"></i>
									<span class="menu-text">
										Arsip Digital
									</span>
									<b class="arrow fa fa-angle-down"></b>
								</a>
								<b class="arrow"></b>
								<ul class="submenu">
									<li class="<?php echo $StatArsipFileEntri;?>">
										<a href="./index.php?op=add_arsip">
											<i class="menu-icon fa fa-caret-right"></i>
											Entri baru
										</a>
										<b class="arrow"></b>
									</li>
									<li class="<?php echo $StatArsipFileView;?>">
										<a href="./index.php?op=arsip_file">
											<i class="menu-icon fa fa-caret-right"></i>
											Data file arsip
										</a>
										<b class="arrow"></b>
									</li>
									<li class="<?php echo $StatCariFile;?>">
										<a href="./index.php?op=cari_arsip">
											<i class="menu-icon fa fa-caret-right"></i>
											Pencarian Arsip
										</a>
										<b class="arrow"></b>
									</li>
								</ul>
							</li><?php
						}
						if($HakAkses->arsip == "R"){?>
							<li class="<?php echo $StatArsipFileView;?>">
								<a href="./index.php?op=arsip_file">
									<i class="menu-icon fa fa-th-large"></i>
									Data Arsip Digital
								</a>
								<b class="arrow"></b>
							</li><?php
						}
						if($HakAkses->atur_layout == "Y" OR $HakAkses->atur_klasifikasi_sm == "Y" OR $HakAkses->atur_klasifikasi_sk == "Y" OR $HakAkses->atur_klasifikasi_arsip == "Y" OR $HakAkses->atur_user == "Y"){?>
						<li class="<?php echo $StatAtur;?>">
							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-cogs"></i>
								<span class="menu-text">
									Pengaturan
								</span>
								<b class="arrow fa fa-angle-down"></b>
							</a>
							<b class="arrow"></b>
							<ul class="submenu">
								<?php /*
								<li class="<?php echo $StatAsalSurat;?>">
									<a href="./index.php?op=asalsurat">
										<i class="menu-icon glyphicon glyphicon-repeat"></i>
										<span class="menu-text"> Asal Surat </span>
									</a>
									<b class="arrow"></b>
								</li>*/
								if($HakAkses->atur_layout == "Y"){?>
									<li class="<?php echo $StatSetting;?>">
										<a href="./index.php?op=setting">
											<i class="menu-icon fa fa-cog"></i>
											<span class="menu-text"> Atur Layout </span>
										</a>
										<b class="arrow"></b>
									</li><?php
								}
								if($HakAkses->atur_klasifikasi_sm == "Y"){?>
									<li class="<?php echo $StatKlasSM;?>">
										<a href="./index.php?op=klasifikasi">
											<i class="menu-icon fa fa-tags"></i>
											<span class="menu-text"> Klasifikasi Surat Masuk </span>
										</a>
										<b class="arrow"></b>
									</li><?php
								}
								if($HakAkses->atur_klasifikasi_sk == "Y"){?>
									<li class="<?php echo $StatKlasSK;?>">
										<a href="./index.php?op=klasifikasi_sk">
											<i class="menu-icon fa fa-tags"></i>
											<span class="menu-text"> Klasifikasi Surat Keluar </span>
										</a>
										<b class="arrow"></b>
									</li><?php
								}
								/* if($HakAkses->atur_klasifikasi_sk == "Y"){?>
									<li class="<?php echo $StatKlasifikasiSK;?>">
										<a href="./index.php?op=klasifikasi_sk">
											<i class="menu-icon fa fa-tags"></i>
											<span class="menu-text"> Klasifikasi Surat Keluar </span>
										</a>
										<b class="arrow"></b>
									</li><?php
								} */
								if($HakAkses->atur_klasifikasi_arsip == "Y"){?>
									<li class="<?php echo $StatKlasFile;?>">
										<a href="./index.php?op=klasifikasi_file">
											<i class="menu-icon fa fa-file-text"></i>
											<span class="menu-text"> Klasifikasi File Arsip </span>
										</a>
										<b class="arrow"></b>
									</li><?php
								}
								if($HakAkses->atur_user == "Y"){?>
									<li class="<?php echo $StatUser;?>">
										<a href="./index.php?op=user">
											<i class="menu-icon fa fa-user"></i>
											<span class="menu-text"> Data User </span>
										</a>
										<b class="arrow"></b>
									</li><?php
								}?>
							</ul>
						</li><?php
						}
						
						if($HakAkses->report_sm == "Y" OR $HakAkses->report_sk == "Y" OR $HakAkses->report_arsip == "Y"){?>	
							<li class="<?php echo $StatReport;?>">
								<a href="#" class="dropdown-toggle">
									<i class="menu-icon fa fa-bar-chart"></i>
									<span class="menu-text">
										Laporan
									</span>
									<b class="arrow fa fa-angle-down"></b>
								</a>
								<b class="arrow"></b>
								<ul class="submenu"><?php
									if($HakAkses->report_dispo == "Y"){?>
										<li class="<?php echo $StatDIS;?>">
											<a href="./index.php?op=report_disposisi">
												<i class="menu-icon fa fa-caret-right"></i>
												Disposisi
											</a>
											<b class="arrow"></b>
										</li><?php
									}
									if($HakAkses->report_progress == "Y"){?>
										<li class="<?php echo $StatReportProgress;?>">
											<a href="./index.php?op=report_progress">
												<i class="menu-icon fa fa-caret-right"></i>
												Progress Surat
											</a>
											<b class="arrow"></b>
										</li><?php
									}
									if($HakAkses->report_sm == "Y"){?>
									<li class="<?php echo $StatRSM;?>">
										<a href="./index.php?op=report_sm">
											<i class="menu-icon fa fa-caret-right"></i>
											Surat Masuk
										</a>
										<b class="arrow"></b>
									</li><?php
									}
									if($HakAkses->report_sk == "Y"){?>
									<li class="<?php echo $StatRSK;?>">
										<a href="./index.php?op=report_sk">
											<i class="menu-icon fa fa-caret-right"></i>
											Surat Keluar
										</a>
										<b class="arrow"></b>
									</li><?php
									}
									if($HakAkses->report_arsip == "Y"){?>
									<li class="<?php echo $StatReportArsip;?>">
										<a href="./index.php?op=report_arsip">
											<i class="menu-icon fa fa-caret-right"></i>
											Data Arsip Digital
										</a>
										<b class="arrow"></b>
									</li><?php
									}?>
								</ul>
							</li><?php
						}?>
					</ul><!-- /.nav-list -->

					<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
						<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
					</div>
				</div>

				<div class="main-content">
					<div class="main-content-inner">
						<div class="breadcrumbs ace-save-state" id="breadcrumbs">
							<ul class="breadcrumb">
								<li>
									<i class="ace-icon fa fa-home home-icon"></i>
									<a href="#">Home</a>
								</li>
								<li class="active">Dashboard</li>
							</ul><!-- /.breadcrumb -->
							<?php
							if(isset($_GET['op']) AND $_GET['op'] == "sm"){
								$titlePlace = "Cari Surat Masuk ...";
								$value = "sm";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_sm"){
								$titlePlace = "Cari Surat Masuk ...";
								$value = "arsip_sm";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_sk"){
								$titlePlace = "Cari Surat Keluar ...";
								$value = "arsip_sk";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "sk"){
								$titlePlace = "Cari Surat Keluar ...";
								$value = "sk";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "memo"){
								$titlePlace = "Cari Surat Masuk ...";
								$value = "memo";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "disposisi"){
								$titlePlace = "Cari Disposisi ...";
								$value = "disposisi";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "tembusan"){
								$titlePlace = "Cari Tembusan ...";
								$value = "tembusan";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_file"){
								$titlePlace = "Cari Arsip ...";
								$value = "arsip_file";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "info"){
								$titlePlace = "Cari Memo ...";
								$value = "info";
							}elseif(isset($_GET['op']) AND $_GET['op'] == "data_memo"){
								$titlePlace = "Cari Arsip Memo ...";
								$value = "data_memo";
							}else{
								$titlePlace = null;
							}
							if($titlePlace != null){?>
								<div class="nav-search" id="nav-search">
									<form class="form-search" method="GET" action="<?php echo $_SESSION['url'];?>">
										<span class="input-icon">
											<input type="hidden" name="op" value="<?php echo $value;?>"/>
											<input type="text" placeholder="<?php echo $titlePlace;?>" class="nav-search-input" name="keyword" autocomplete="off" />
											<i class="ace-icon fa fa-search nav-search-icon"></i>
										</span>
									</form>
								</div><?php
							}?>
						</div>

						<div class="page-content">
							<div class="ace-settings-container" id="ace-settings-container">
								<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
									<i class="ace-icon fa fa-cog bigger-130"></i>
								</div>

								<div class="ace-settings-box clearfix" id="ace-settings-box">
									<div class="pull-left width-50">
										<div class="ace-settings-item">
											<div class="pull-left">
												<select id="skin-colorpicker" class="hide">
													<option data-skin="no-skin" value="#438EB9">#438EB9</option>
													<option data-skin="skin-1" value="#222A2D">#222A2D</option>
													<option data-skin="skin-2" value="#C6487E">#C6487E</option>
													<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
												</select>
											</div>
											<span>&nbsp; Choose Skin</span>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
											<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
											<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
											<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
											<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
											<label class="lbl" for="ace-settings-add-container">
												Inside
												<b>.container</b>
											</label>
										</div>
									</div><!-- /.pull-left -->

									<div class="pull-left width-50">
										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
											<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
											<label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
										</div>

										<div class="ace-settings-item">
											<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
											<label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
										</div>
									</div><!-- /.pull-left -->
								</div><!-- /.ace-settings-box -->
							</div><!-- /.ace-settings-container -->

							<div class="page-header">
								<h1><?php
									if(isset($_GET['op'])){
										if(isset($_GET['op']) AND $_GET['op'] == "sm"){
											$field = array("id_sm","DATE_FORMAT(tgl_terima, '%Y') as thn");
											$arsip_sm = $this->model->selectprepare("arsip_sm", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_terima, '%Y') order by DATE_FORMAT(tgl_terima, '%Y') DESC");
											if($arsip_sm->rowCount() >= 1){?>
												<div class="col-xs-2">
													<form method="GET">
														<input type="hidden" name="op" value="sm" />
														<select class="form-control" name="yearsm" id="form-field-select-1" onchange='this.form.submit()'><?php
															while($dataArsip_sm = $arsip_sm->fetch(PDO::FETCH_OBJ)){
																if(isset($_GET['yearsm'])){
																	if($_GET['yearsm'] == $dataArsip_sm->thn){?>
																		<option value="<?php echo $dataArsip_sm->thn;?>" selected>Surat Masuk <?php echo $dataArsip_sm->thn;?></option><?php
																	}else{?>
																		<option value="<?php echo $dataArsip_sm->thn;?>">Surat Masuk <?php echo $dataArsip_sm->thn;?></option><?php
																	}
																}else{?>
																	<option value="<?php echo $dataArsip_sm->thn;?>">Surat Masuk <?php echo $dataArsip_sm->thn;?></option><?php
																}
															}?>
														</select>
													</form>
												</div><br/><?php
											}
										}elseif(isset($_GET['op']) AND $_GET['op'] == "sk"){
											$field = array("id_sk","DATE_FORMAT(tgl_surat, '%Y') as thn");
											$arsip_sk = $this->model->selectprepare("arsip_sk", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_surat, '%Y') order by DATE_FORMAT(tgl_surat, '%Y') DESC");
											if($arsip_sk->rowCount() >= 1){?>
												<div class="col-xs-2">
													<form method="GET">
														<input type="hidden" name="op" value="sk" />
														<select class="form-control" name="yearsk" id="form-field-select-1" onchange='this.form.submit()'><?php
															while($dataArsip_sk = $arsip_sk->fetch(PDO::FETCH_OBJ)){
																if(isset($_GET['yearsk'])){
																	if($_GET['yearsk'] == $dataArsip_sk->thn){?>
																		<option value="<?php echo $dataArsip_sk->thn;?>" selected>Surat Keluar<?php echo $dataArsip_sk->thn;?></option><?php
																	}else{?>
																		<option value="<?php echo $dataArsip_sk->thn;?>">Surat Keluar<?php echo $dataArsip_sk->thn;?></option><?php
																	}
																}else{?>
																	<option value="<?php echo $dataArsip_sk->thn;?>">Surat Keluar<?php echo $dataArsip_sk->thn;?></option><?php
																}
															}?>
														</select>
													</form>
												</div>
												<br/><?php
											}?><?php
										}elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_file"){
											$field = array("id_arsip","DATE_FORMAT(tgl_arsip, '%Y') as thn");
											$arsip_file = $this->model->selectprepare("arsip_file", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_arsip, '%Y') order by DATE_FORMAT(tgl_arsip, '%Y') DESC");
											if($arsip_file->rowCount() >= 1){?>
												<div class="col-xs-2">
													<form method="GET">
														<input type="hidden" name="op" value="arsip_file" />
														<select class="form-control" name="yearfile" id="form-field-select-1" onchange='this.form.submit()'><?php
															while($dataArsip_file = $arsip_file->fetch(PDO::FETCH_OBJ)){
																if(isset($_GET['yearfile'])){
																	if($_GET['yearfile'] == $dataArsip_file->thn){?>
																		<option value="<?php echo $dataArsip_file->thn;?>" selected>Arsip Tahun<?php echo $dataArsip_file->thn;?></option><?php
																	}else{?>
																		<option value="<?php echo $dataArsip_file->thn;?>">Arsip Tahun<?php echo $dataArsip_file->thn;?></option><?php
																	}
																}else{?>
																	<option value="<?php echo $dataArsip_file->thn;?>">Arsip Tahun<?php echo $dataArsip_file->thn;?></option><?php
																}
															}?>
														</select>
													</form>
												</div>
												<br/><?php
											}?><?php
										}elseif(isset($_GET['op']) AND $_GET['op'] == "data_memo"){
											$field = array("id_info","DATE_FORMAT(tgl_info, '%Y') as thn");
											$arsip_info = $this->model->selectprepare("info", $field, $params=null, $where=null, "GROUP BY DATE_FORMAT(tgl_info, '%Y') order by DATE_FORMAT(tgl_info, '%Y') DESC");
											if($arsip_info->rowCount() >= 1){?>
												<div class="col-xs-3">
													<form method="GET">
														<input type="hidden" name="op" value="data_memo" />
														<select class="form-control" name="yearinfo" id="form-field-select-1" onchange='this.form.submit()'><?php
															while($dataArsip_Info = $arsip_info->fetch(PDO::FETCH_OBJ)){
																if(isset($_GET['yearinfo'])){
																	if($_GET['yearinfo'] == $dataArsip_Info->thn){?>
																		<option value="<?php echo $dataArsip_Info->thn;?>" selected>Arsip Tahun <?php echo $dataArsip_Info->thn;?></option><?php
																	}else{?>
																		<option value="<?php echo $dataArsip_Info->thn;?>">Arsip Tahun <?php echo $dataArsip_Info->thn;?></option><?php
																	}
																}else{?>
																	<option value="<?php echo $dataArsip_Info->thn;?>">Arsip Tahun <?php echo $dataArsip_Info->thn;?></option><?php
																}
															}?>
														</select>
													</form>
												</div>
												<br/><?php
											}?><?php
										}else{
											echo "Dashboard";
										}
									}else{
										echo "Welcome";
									}
									/* if(isset($_GET['op']) AND $_GET['op'] == "add_sm"){
										echo "Entri surat masuk";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "sm"){
										echo "Data surat masuk";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "add_sk"){
										echo "Entri surat keluar";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "sk"){
										echo "Data surat keluar";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "memo"){
										echo "Inbox Memo";
									}else{ */
										
									//}?>
								</h1>
							</div><!-- /.page-header -->

							<div class="row">
								<div class="col-xs-12">
									<!-- PAGE CONTENT BEGINS --><?php
									if(isset($_GET['op']) AND $_GET['op'] == "add_sm"){
										if($HakAkses->sm == "W"){
											require_once "entry_sm.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "sm"){
										if($HakAkses->sm == "W" OR $HakAkses->sm == "R"){
											require_once "view_sm.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "add_sk"){
										if($HakAkses->sk == "W"){
											require_once "entry_sk.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "sk"){
										if($HakAkses->sk == "W" OR $HakAkses->sk == "R"){
											require_once "view_sk.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "klasifikasi"){
										if($HakAkses->atur_klasifikasi_sm == "Y"){
											require_once "klasifikasi.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "klasifikasi_sk"){
										if($HakAkses->atur_klasifikasi_sk == "Y"){
											require_once "klasifikasi_sk.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_sm"){
										if($HakAkses->sm == "W" OR $HakAkses->sm == "R"){
											require_once "view_arsip_sm.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_sk"){
										if($HakAkses->sk == "W" OR $HakAkses->sk == "R"){
											require_once "view_arsip_sk.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "user"){
										if($HakAkses->atur_user == "Y"){
											require_once "user.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "report_sm"){
										if($HakAkses->report_sm == "Y"){
											require_once "sm_report.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "report_sk"){
										if($HakAkses->report_sk == "Y"){
											require_once "sk_report.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "setting"){
										if($HakAkses->atur_layout == "Y"){
											require_once "setting.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "memo"){
										require_once "view_memo.php";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "disposisi"){
										require_once "view_disposisi.php";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "tembusan"){
										require_once "view_tembusan.php";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "profil"){
										require_once "profile.php";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "cari_sm"){
										if($HakAkses->cari_surat_masuk == "Y"){
											require_once "cari_surat.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "cari_sk"){
										if($HakAkses->cari_surat_keluar == "Y"){
											require_once "cari_surat.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "cari_arsip" OR $_GET['op'] == "report_arsip"){
										if($HakAkses->report_arsip == "Y" OR $HakAkses->arsip == "W"){
											require_once "cari_arsip.php";
										}else{
											require_once "invalid_akses.php";
										}
									}/* elseif(isset($_GET['op']) AND $_GET['op'] == "template"){
										if($HakAkses->atur_template == "Y"){
											require_once "template_surat.php";
										}else{
											require_once "invalid_akses.php";
										}
									} */
									elseif(isset($_GET['op']) AND $_GET['op'] == "view_surat"){
										require_once "cari_surat_view.php";
									}/* elseif(isset($_GET['op']) AND $_GET['op'] == "sk_temp_tgs"){
										if($HakAkses->template_surat == "W"){
											require_once "entry_sk_temp.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "view_sk_temp"){
										if($HakAkses->template_surat == "W" OR $HakAkses->template_surat == "R"){
											require_once "view_arsip_sk_temp.php";
										}else{
											require_once "invalid_akses.php";
										}
									} */
									elseif(isset($_GET['op']) AND $_GET['op'] == "arsip_file"){
										if($HakAkses->arsip == "W" OR $HakAkses->arsip == "R"){
											require_once "view_arsip_file.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "add_arsip"){
										if($HakAkses->arsip == "W"){
											require_once "entry_filearsip.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "klasifikasi_file"){
										if($HakAkses->atur_klasifikasi_arsip == "Y"){
											require_once "klasifikasi_arsip_file.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "report_disposisi"){
										if($HakAkses->report_dispo == "Y"){
											require_once "disposisi_report.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "report_progress"){
										if($HakAkses->report_dispo == "Y"){
											require_once "progress_report.php";
										}else{
											require_once "invalid_akses.php";
										}
									}elseif(isset($_GET['op']) AND $_GET['op'] == "info"){
										require_once "view_info.php";
									}elseif(isset($_GET['op']) AND $_GET['op'] == "add_memo" OR $_GET['op'] == "data_memo"){
										if($HakAkses->info == "Y" AND $_GET['op'] == "add_memo"){
											require_once "entry_info.php";
										}elseif($HakAkses->info == "Y" AND $_GET['op'] == "data_memo"){
											require_once "view_data_memo.php";
										}else{
											require_once "invalid_akses.php";
										}
									}else{?>
										<?php
										//if(($_SESSION['hakakses'] == "Sekretaris") || ($_SESSION['hakakses'] == "Manager")){?>
											<div class="row">
												<div class="col-sm-12">
												<div class="alert alert-block alert-success">
													<button type="button" class="close" data-dismiss="alert">
														<i class="ace-icon fa fa-times"></i>
													</button>
													<i class="ace-icon fa fa-check green"></i>
													Selamat datang di Aplikasi Manajemen Surat (SIAS).
												</div>
												<center><?php
													if($HakAkses->sm == "W" OR $HakAkses->sm == "R"){?>
													<a href="./index.php?op=sm">
														<div class="infobox infobox-green">
															<div class="infobox-icon">
																<i class="ace-icon fa fa-inbox"></i>
															</div><?php
															//statistik
															$JlhArsipSM = $this->model->selectprepare("arsip_sm", $field=null, $params=null, $where=null, $order=null);?>
															<div class="infobox-data">
																<span class="infobox-data-number"><?php echo $JlhArsipSM->rowCount();?> Arsip</span>
																<div class="infobox-content">Surat Masuk</div>
															</div>
														</div>
													</a><?php
													}
													if($HakAkses->sk == "W" OR $HakAkses->sk == "R"){?>
													<a href="./index.php?op=sk">
														<div class="infobox infobox-blue">
															<div class="infobox-icon">
																<i class="ace-icon fa fa-send-o"></i>
															</div><?php
															//statistik
															$JlhArsipSK = $this->model->selectprepare("arsip_sk", $field=null, $params=null, $where=null, $order=null);?>
															<div class="infobox-data">
																<span class="infobox-data-number"><?php echo $JlhArsipSK->rowCount();?> Arsip</span>
																<div class="infobox-content">Surat Keluar</div>
															</div>
														</div>
													</a><?php
													}
													/*
													$HitMemo = $this->model->selectprepare("memo", $field=null, $params=null, $where=null, $order=null);?>
													<a href="./index.php?op=memo">
														<div class="infobox infobox-pink">
															<div class="infobox-icon">
																<i class="ace-icon fa fa-comments-o"></i>
															</div>

															<div class="infobox-data">
																<span class="infobox-data-number"><?php echo $HitMemo->rowCount();?></span>
																<div class="infobox-content">Memo </div>
															</div>
														</div>
													</a><?php */
													if($HakAkses->arsip == "W" OR $HakAkses->arsip == "R"){
													$arsip_file = $this->model->selectprepare("arsip_file", $field=null, $params=null, $where=null, $order=null);?>
													<a href="./index.php?op=arsip_file">
														<div class="infobox infobox-red">
															<div class="infobox-icon">
																<i class="ace-icon fa fa-book"></i>
															</div>

															<div class="infobox-data">
																<span class="infobox-data-number"><?php echo $arsip_file->rowCount();?> Arsip</span>
																<div class="infobox-content">File Digital </div>
															</div>
														</div>
													</a><?php
													}?>
													</center>
												</div>
											</div><!-- /.row --><?php
										//}
									}?>
									<!--<div class="hr hr32 hr-dotted"></div>-->
									<p></p>

									<!-- PAGE CONTENT ENDS -->
								</div><!-- /.col -->
							</div><!-- /.row -->
						</div><!-- /.page-content -->
					</div>
				</div><!-- /.main-content -->
				
				<div class="footer">
					<div class="footer-inner">
						<div class="footer-content">
							<span class="bigger-120">
								<span class="blue bolder">Sistem Informasi Arsip Surat (SIAS) </span>
								&copy; <?php echo date('Y');?> 
							</span>
							<!--
							&nbsp; &nbsp;
							<span class="action-buttons">
								<a href="https://twitter.com/Lensakom/" target="_blank">
									<i class="ace-icon fa fa-twitter-square light-blue bigger-80"></i>
								</a>

								<a href="https://www.facebook.com/lensakom" target="_blank">
									<i class="ace-icon fa fa-facebook-square text-primary bigger-70"></i>
								</a>

								<a href="http://www.lensakom.com/" target="_blank">
									<i class="ace-icon fa fa-rss-square orange bigger-70"></i>
								</a>
							</span>-->
						</div>
					</div>
				</div>

				<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
					<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
				</a>
			</div><!-- /.main-container -->

			<!-- basic scripts -->

			<!--[if !IE]> -->
			<script src="assets/js/jquery-2.1.4.min.js"></script>

			<!-- <![endif]-->

			<!--[if IE]>
				<script src="assets/js/jquery-1.11.3.min.js"></script>
			<![endif]-->
			<script type="text/javascript">
				if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
			</script>
			<script src="assets/js/bootstrap.min.js"></script>

			<!-- page specific plugin scripts -->
			<script src="assets/js/jquery.maskedinput.min.js"></script>
			
			<script src="assets/js/chosen.jquery.min.js"></script>
			<script src="assets/js/bootstrap-datepicker.min.js"></script>
			<script src="assets/js/moment.min.js"></script>
			<script src="assets/js/daterangepicker.min.js"></script>
			<script src="assets/js/autosize.min.js"></script>
			<script src="assets/js/jquery.inputlimiter.min.js"></script>
			<script src="assets/js/jquery.maskedinput.min.js"></script>

			<!--[if lte IE 8]>
			  <script src="assets/js/excanvas.min.js"></script>
			<![endif]-->
			<script src="assets/js/jquery-ui.custom.min.js"></script>
			<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
			<script src="assets/js/jquery.easypiechart.min.js"></script>
			<script src="assets/js/jquery.sparkline.index.min.js"></script>
			<script src="assets/js/jquery.flot.min.js"></script>
			<script src="assets/js/jquery.flot.pie.min.js"></script>
			<script src="assets/js/jquery.flot.resize.min.js"></script>

			<!-- ace scripts -->
			<script src="assets/js/ace-elements.min.js"></script>
			<script src="assets/js/ace.min.js"></script>

			<!-- inline scripts related to this page -->
			<script type="text/javascript">
				jQuery(function($) {
					//datepicker plugin
					//link
					$('.date-picker').datepicker({
						autoclose: true,
						todayHighlight: true
					})
					//show datepicker when clicking on the icon
					.next().on(ace.click_event, function(){
						$(this).prev().focus();
					});
				
					//or change it into a date range picker
					$('.input-daterange').datepicker({autoclose:true});
				
					
					//choosen
					if(!ace.vars['touch']) {
						$('.chosen-select').chosen({allow_single_deselect:true}); 
						//resize the chosen on window resize
				
						$(window)
						.off('resize.chosen')
						.on('resize.chosen', function() {
							$('.chosen-select').each(function() {
								 var $this = $(this);
								 $this.next().css({'width': $this.parent().width()});
							})
						}).trigger('resize.chosen');
						//resize chosen on sidebar collapse/expand
						$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
							if(event_name != 'sidebar_collapsed') return;
							$('.chosen-select').each(function() {
								 var $this = $(this);
								 $this.next().css({'width': $this.parent().width()});
							})
						});
						
						
						//chosen plugin inside a modal will have a zero width because the select element is originally hidden
						//and its width cannot be determined.
						//so we set the width after modal is show
						$('#modal-form').on('shown.bs.modal', function () {
							if(!ace.vars['touch']) {
								$(this).find('.chosen-container').each(function(){
									$(this).find('a:first-child').css('width' , '210px');
									$(this).find('.chosen-drop').css('width' , '210px');
									$(this).find('.chosen-search input').css('width' , '200px');
								});
							}
						})
						/**
						//or you can activate the chosen plugin after modal is shown
						//this way select element becomes visible with dimensions and chosen works as expected
						$('#modal-form').on('shown', function () {
							$(this).find('.modal-chosen').chosen();
						})
						*/
						//choosen
						
				
						$('#chosen-multiple-style .btn').on('click', function(e){
							var target = $(this).find('input[type=radio]');
							var which = parseInt(target.val());
							if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
							 else $('#form-field-select-4').removeClass('tag-input-style');
						});
					}
					
					//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
					$('input[name=rangetgl]').daterangepicker({
						'applyClass' : 'btn-sm btn-success',
						'cancelClass' : 'btn-sm btn-default',
						locale: {
							applyLabel: 'Apply',
							cancelLabel: 'Cancel',
						}
					})
					.prev().on(ace.click_event, function(){
						$(this).next().focus();
					});
					
					$('.sparkline').each(function(){
						var $box = $(this).closest('.infobox');
						var barColor = !$box.hasClass('infobox-dark') ? $box.css('color') : '#FFF';
						$(this).sparkline('html',
										 {
											tagValuesAttribute:'data-values',
											type: 'bar',
											barColor: barColor ,
											chartRangeMin:$(this).data('min') || 0
										 });
					});
				
					$('[data-rel=tooltip]').tooltip({container:'body'});
					$('[data-rel=popover]').popover({container:'body'});
					
					$.mask.definitions['~']='[+-]';
					$('.input-mask-date').mask('99/99/9999');
					$('.input-mask-phone').mask('(999) 999-9999');
					$('.input-mask-eyescript').mask('~9.99 ~9.99 999');
					$(".input-mask-product").mask("a*-999-a999",{placeholder:" ",completed:function(){alert("You typed the following: "+this.val());}});
				
					$('#id-input-file-1 , #id-input-file-2').ace_file_input({
						no_file:'No File ...',
						btn_choose:'Pilih file',
						btn_change:'Change',
						droppable:false,
						onchange:null,
						thumbnail:false //| true | large
						//whitelist:'gif|png|jpg|jpeg'
						//blacklist:'exe|php'
						//onchange:''
						//
					});
					//pre-show a file name, for example a previously selected file
					//$('#id-input-file-1').ace_file_input('show_file_list', ['myfile.txt'])
					
					//flot chart resize plugin, somehow manipulates default browser resize event to optimize it!
					//but sometimes it brings up errors with normal resize event handlers
					$.resize.throttleWindow = false;
				
				  var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
				  var data = [
					{ label: "social networks",  data: 38.7, color: "#68BC31"},
					{ label: "search engines",  data: 24.5, color: "#2091CF"},
					{ label: "ad campaigns",  data: 8.2, color: "#AF4E96"},
					{ label: "direct traffic",  data: 18.6, color: "#DA5430"},
					{ label: "other",  data: 10, color: "#FEE074"}
				  ]
				  function drawPieChart(placeholder, data, position) {
					  $.plot(placeholder, data, {
						series: {
							pie: {
								show: true,
								tilt:0.8,
								highlight: {
									opacity: 0.25
								},
								stroke: {
									color: '#fff',
									width: 2
								},
								startAngle: 2
							}
						},
						legend: {
							show: true,
							position: position || "ne", 
							labelBoxBorderColor: null,
							margin:[-30,15]
						}
						,
						grid: {
							hoverable: true,
							clickable: true
						}
					 })
				 }
				 drawPieChart(placeholder, data);
				
				 /**
				 we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
				 so that's not needed actually.
				 */
				 placeholder.data('chart', data);
				 placeholder.data('draw', drawPieChart);
				
				
				  //pie chart tooltip example
				  var $tooltip = $("<div class='tooltip top in'><div class='tooltip-inner'></div></div>").hide().appendTo('body');
				  var previousPoint = null;
				
				  placeholder.on('plothover', function (event, pos, item) {
					if(item) {
						if (previousPoint != item.seriesIndex) {
							previousPoint = item.seriesIndex;
							var tip = item.series['label'] + " : " + item.series['percent']+'%';
							$tooltip.show().children(0).text(tip);
						}
						$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
					} else {
						$tooltip.hide();
						previousPoint = null;
					}
					
				 });
				
					/////////////////////////////////////
					$(document).one('ajaxloadstart.page', function(e) {
						$tooltip.remove();
					});
				
				
				
				
					var d1 = [];
					for (var i = 0; i < Math.PI * 2; i += 0.5) {
						d1.push([i, Math.sin(i)]);
					}
				
					var d2 = [];
					for (var i = 0; i < Math.PI * 2; i += 0.5) {
						d2.push([i, Math.cos(i)]);
					}
				
					var d3 = [];
					for (var i = 0; i < Math.PI * 2; i += 0.2) {
						d3.push([i, Math.tan(i)]);
					}
					
				
					var sales_charts = $('#sales-charts').css({'width':'100%' , 'height':'220px'});
					$.plot("#sales-charts", [
						{ label: "Domains", data: d1 },
						{ label: "Hosting", data: d2 },
						{ label: "Services", data: d3 }
					], {
						hoverable: true,
						shadowSize: 0,
						series: {
							lines: { show: true },
							points: { show: true }
						},
						xaxis: {
							tickLength: 0
						},
						yaxis: {
							ticks: 10,
							min: -2,
							max: 2,
							tickDecimals: 3
						},
						grid: {
							backgroundColor: { colors: [ "#fff", "#fff" ] },
							borderWidth: 1,
							borderColor:'#555'
						}
					});
				
				
					$('#recent-box [data-rel="tooltip"]').tooltip({placement: tooltip_placement});
					function tooltip_placement(context, source) {
						var $source = $(source);
						var $parent = $source.closest('.tab-content')
						var off1 = $parent.offset();
						var w1 = $parent.width();
				
						var off2 = $source.offset();
						//var w2 = $source.width();
				
						if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
						return 'left';
					}
				
				
					$('.dialogs,.comments').ace_scroll({
						size: 300
					});
					
					
					//Android's default browser somehow is confused when tapping on label which will lead to dragging the task
					//so disable dragging when clicking on label
					var agent = navigator.userAgent.toLowerCase();
					if(ace.vars['touch'] && ace.vars['android']) {
					  $('#tasks').on('touchstart', function(e){
						var li = $(e.target).closest('#tasks li');
						if(li.length == 0)return;
						var label = li.find('label.inline').get(0);
						if(label == e.target || $.contains(label, e.target)) e.stopImmediatePropagation() ;
					  });
					}
				
					$('#tasks').sortable({
						opacity:0.8,
						revert:true,
						forceHelperSize:true,
						placeholder: 'draggable-placeholder',
						forcePlaceholderSize:true,
						tolerance:'pointer',
						stop: function( event, ui ) {
							//just for Chrome!!!! so that dropdowns on items don't appear below other items after being moved
							$(ui.item).css('z-index', 'auto');
						}
						}
					);
					$('#tasks').disableSelection();
					$('#tasks input:checkbox').removeAttr('checked').on('click', function(){
						if(this.checked) $(this).closest('li').addClass('selected');
						else $(this).closest('li').removeClass('selected');
					});
				
				
					//show the dropdowns on top or bottom depending on window height and menu position
					$('#task-tab .dropdown-hover').on('mouseenter', function(e) {
						var offset = $(this).offset();
				
						var $w = $(window)
						if (offset.top > $w.scrollTop() + $w.innerHeight() - 100) 
							$(this).addClass('dropup');
						else $(this).removeClass('dropup');
					});
				
				})
			</script>
		</body>
	</html><?php
}else{
	/* echo "Sesssion belum ada<br/>";
	echo $_SESSION['atra_id']."  pass ".$_SESSION['hakakses']; */
	echo "<script type=\"text/javascript\">window.location.href=\"./login\";</script>";
}?>
