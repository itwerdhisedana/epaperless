<?php
//error_reporting(0);
ini_set('date.timezone', 'Asia/Jakarta');
require_once "view/indo_tgl.php";
require_once "htmlpurifier/library/HTMLPurifier.auto.php";
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$cari = htmlspecialchars($purifier->purify(trim($_POST['cari'])), ENT_QUOTES);
	$params = array(':custom_noagenda' => $cari, ':no_sm' => $cari);
	$cek = $this->model->selectprepare("arsip_sm", $field=null, $params, "custom_noagenda =:no_agenda OR no_sm=:no_sm");
	if($cek->rowCount() >= 1){
		$dataSurat = $cek->fetch(PDO::FETCH_OBJ);
	}else{
		$dataSurat = 0;
	}
	$layout = "error-container";
	$title = "Hasil Tracking Status Surat";
}else{
	$layout = "login-container";
	$title = "Tracking Status Surat";
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Login Page - SIAS</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
	</head>

	<body class="login-layout light-login">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-6 col-sm-offset-3">
						<div class="center">
							<h1>
								<i class="ace-icon fa fa-leaf green"></i>
								<span class="red">SIAS</span>
								<span class="brown" id="id-text2">Application</span>
							</h1>
							<h4 class="blue" id="id-company-text">&copy; <?php echo date('Y'); ?> Sistem Informasi Arsip Surat (SIAS) </h4>
						</div>
						<div class="space-6"></div>
						<div class="<?php echo $layout;?>">
							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header green lighter bigger">
												<i class="ace-icon fa fa-search blue"></i>
												<?php echo $title;?>
											</h4>
											<div class="space-6"></div><?php
											if(isset($dataSurat) && !is_numeric($dataSurat)){
												$ListUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "ORDER BY a.nama ASC");
												$TujuanSurat = "";
												$TargetDisposisi = "";
												while($dataListUser = $ListUser->fetch(PDO::FETCH_OBJ)){
													if(false !== array_search($dataListUser->id_user, json_decode($dataSurat->tujuan_surat, true))){
														$TujuanSurat .= '- '.$dataListUser->nama .' ('.$dataListUser->nama_jabatan .')<br/>';
													}
													
												}
												$tgl_diteruskan = substr($dataSurat->created,0,10);?>
												<p><center>NoAgenda / NoSurat : <b><?php echo $cari;?></b>, Pengirim: <b><?php echo $dataSurat->pengirim;?></b></center></p>
												<table id="dynamic-table" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<td width="250">Status surat</td>
															<td width="140">update</td>
														</tr>
													</thead>
													<tbody>	
														<tr>
															<td colspan="2"><b><center>SURAT DITERUSKAN KE</center></b></td>
														</tr>
														<tr>
															<td><?php if($TujuanSurat != ''){ echo "$TujuanSurat"; }?></td>
															<td><?php echo tgl_indo1($tgl_diteruskan);?>, <?php echo substr($dataSurat->created,-9,-3);?> WIB</td>
														</tr>
														<tr>
															<td colspan="2"><b><center>RIWAYAT DISPOSISI</center></b></td>
														</tr><?php
														$params = array(':id_sm' => $dataSurat->id_sm);
														$cekDisposisi = $this->model->selectprepare("memo a join user b on a.id_user=b.id_user", $field=null, $params, "a.id_sm=:id_sm", "ORDER BY a.tgl ASC");
														if($cekDisposisi->rowCount() >= 1){
															while($dataDisposisi= $cekDisposisi->fetch(PDO::FETCH_OBJ)){
																$ListDisposisi2 = json_decode($dataDisposisi->disposisi, true);
																$tgl_dispolevel = substr($dataDisposisi->tgl,0,10);?>
																<tr>
																	<td><?php
																	echo "Disposisi dari <b>".$dataDisposisi->nama."</b> ke <br/>";
																		foreach($ListDisposisi2 as $listdispo){
																			$TampilUser = $this->model->selectprepare("user a join user_jabatan b on a.jabatan=b.id_jab", $field=null, $params=null, $where=null, "WHERE a.id_user='$listdispo'")->fetch(PDO::FETCH_OBJ);
																			echo "- ".$TampilUser->nama." ($TampilUser->nama_jabatan)<br/>";?><?php
																		}?>
																	</td>
																	<td><?php echo tgl_indo1($tgl_dispolevel);?>, <?php echo substr($dataDisposisi->tgl,-9,-3);?> WIB</td>
																</tr><?php
															}
														}
														$CekProgress = $this->model->selectprepare("status_surat", $field=null, $params=null, $where=null, "WHERE id_sm = '$dataSurat->id_sm' order by statsurat ASC");
														if($CekProgress->rowCount() >= 1){?>
															<tr>
																<td colspan="2"><b><center>PROGRESS SURAT</center></b></td>
															</tr><?php
															while($dataCekProgress = $CekProgress->fetch(PDO::FETCH_OBJ)){
																$CekUser = $this->model->selectprepare("user", $field=null, $params=null, $where=null, "WHERE id_user = '$dataCekProgress->id_user'")->fetch(PDO::FETCH_OBJ);
																if($dataCekProgress->statsurat == 1){
																	$statusSirat = "Sedang diproses";
																}elseif($dataCekProgress->statsurat == 2){
																	$statusSirat = "Selesai";
																}elseif($dataCekProgress->statsurat == 0){
																	$statusSirat = "Dibatalkan";
																}?>
																<tr>
																	<td>
																		<b><?php echo $CekUser->nama;?></b> status : <b><?php echo $statusSirat;?></b> <br/><?php echo $dataCekProgress->ket;?>
																	</td>
																	<td>
																		<?php echo tgl_indo1($dataCekProgress->created);?>, <?php echo substr($dataCekProgress->created,-9,-3);?> WIB
																	</td>
																</tr><?php
															}
														}?>
													</tbody>
												</table><?php
											}else{
												if(isset($dataSurat) && $dataSurat == 0){?>
													<div class="alert alert-danger">
														<p>
															<strong><i class="ace-icon fa fa-check"></i>Perhatian!</strong>
															Data status surat tidak ditemukan. Cek kembali nomor surat yang anda masukkan. Terimakasih.
														</p>
													</div><?php
												}else{?>
													<p> Masukkan Nomor Surat: </p>
													<form class="form-login" method="POST" autocomplete="off" action="<?php echo $_SESSION['url'];?>">
														<fieldset>
															<label class="block clearfix">
																<span class="block input-icon input-icon-right">
																	<input type="text" name="cari" class="form-control" placeholder="Nomor Surat" />
																	<i class="ace-icon fa fa-archive"></i>
																</span>
															</label>
															<div class="clearfix">
																<button type="submit" class="width-30 pull-left btn btn-sm btn-success">
																	<i class="ace-icon fa fa-search"></i>
																	<span class="bigger-110">Cari</span>
																</button>
															</div>
														</fieldset>
													</form><?php
												}
											}?>
										</div>
										
										<div class="toolbar clearfix">
											<div>
												<a href="./tracking" class="forgot-password-link">
													<i class="ace-icon fa fa-arrow-left"></i>
													Kembali
												</a>
											</div>
											<div>
												<a href="./login" class="user-signup-link">
													Login
													<i class="ace-icon fa fa-arrow-right"></i>
												</a>
											</div>
										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->
								
							</div><!-- /.position-relative -->

							<div class="navbar-fixed-top align-right">
								<br />
								&nbsp;
								<a id="btn-login-dark" href="#">Dark</a>
								&nbsp;
								<span class="blue">/</span>
								&nbsp;
								<a id="btn-login-blur" href="#">Blur</a>
								&nbsp;
								<span class="blue">/</span>
								&nbsp;
								<a id="btn-login-light" href="#">Light</a>
								&nbsp; &nbsp; &nbsp;
							</div>
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
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

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			jQuery(function($) {
			 $(document).on('click', '.toolbar a[data-target]', function(e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible');//hide others
				$(target).addClass('visible');//show target
			 });
			});
			
			
			
			//you don't need this, just used for changing background
			jQuery(function($) {
			 $('#btn-login-dark').on('click', function(e) {
				$('body').attr('class', 'login-layout');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'blue');
				
				e.preventDefault();
			 });
			 $('#btn-login-light').on('click', function(e) {
				$('body').attr('class', 'login-layout light-login');
				$('#id-text2').attr('class', 'grey');
				$('#id-company-text').attr('class', 'blue');
				
				e.preventDefault();
			 });
			 $('#btn-login-blur').on('click', function(e) {
				$('body').attr('class', 'login-layout blur-login');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'light-blue');
				
				e.preventDefault();
			 });
			 
			});
		</script>
	</body>
</html><?php
//}?>
