<?php
include "model/model.php";
class controller{
	function __construct(){
		$this->model = new model();
	}
	function index(){
		if(file_exists("view/main.php")){
			require "view/main.php";
		}else{
			echo "Fungsi Utama pada Modul View Belum Ada!!";
		}
	}
	function logout(){
		session_destroy();
		echo "<script type=\"text/javascript\">window.location.href=\"./login\";</script>";
		/* if(file_exists("view/login.php")){
			require "view/login.php";
		}else{
			echo "Fungsi Logout Belum ada Pada Modul View!!";
		} */
	}
	function login(){
		if(file_exists("view/login.php")){
			require "view/login.php";
		}else{
			echo "Fungsi login Belum ada Pada Modul View!!";
		}
	}
	function tracking(){
		if(file_exists("view/tracking.php")){
			require "view/tracking.php";
		}else{
			echo "Fungsi Tracking Belum ada Pada Modul View!!";
		}
	}

	function disposisiprint(){
		require "view/view_disposisi_print.php";
	}
	function smprint_out(){
		require "view/sm_report_print.php";
	}
	function skprint_out(){
		require "view/sk_report_print.php";
	}
	function dispoprint_out(){
		require "view/dispo_report_print.php";
	}
	function arsip_print_out(){
		require "view/arsip_report_print.php";
	}
	function memoprint(){
		require "view/view_memo_print.php";
	}
	function smprint(){
		require "view/view_sm_accept_print.php";
	}
	function progressprint(){
		require "view/progress_report_print.php";
	}
}?>