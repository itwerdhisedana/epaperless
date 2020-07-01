<?php
session_start();
error_reporting(0);
include "controller/controller.php";
$main = new controller();
//URL WEB
if ($_SERVER['HTTP_HOST'] == "localhost"){
	$_SESSION['url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}else{
	//$_SESSION['url'] = "http://$_SERVER[HTTP_HOST]";
	$_SESSION['url'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}
if(isset($_GET['op']) AND $_GET['op']=="login"){
	$main->login();
}elseif(isset($_GET['op']) AND $_GET['op']=="out"){
	$main->logout();
}elseif(isset($_GET['op']) AND $_GET['op']=="tracking"){
	$main->tracking();
}elseif(isset($_GET['op']) AND $_GET['op']=="memoprint"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->memoprint();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="disposisiprint"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->disposisiprint();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="report_sm_print"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->smprint_out();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="report_sk_print"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->skprint_out();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="report_dispo_print"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->dispoprint_out();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="report_arsip_print"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->arsip_print_out();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="smprint"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->smprint();
	}else{
		$main->logout();
	}
}elseif(isset($_GET['op']) AND $_GET['op']=="report_progress_print"){
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->progressprint();
	}else{
		$main->logout();
	}
}else{
	if(isset($_SESSION['atra_id']) AND isset($_SESSION['atra_pass'])){
		$main->index();
	}else{
		$main->tracking();
	}
}?>