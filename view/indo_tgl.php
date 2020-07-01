<?php
function tgl_indo($tgl){
		$hari = date('l',strtotime($tgl));
		if ($hari=="Monday"){
            $hari="Senin";
        }elseif($hari=="Tuesday"){
            $hari="Selasa";
        }elseif($hari=="Wednesday"){
            $hari="Rabu";
        }elseif($hari=="Thursday"){
            $hari="Kamis";
        }elseif($hari=="Friday"){
            $hari="Jumat";
        }elseif($hari=="Saturday"){
            $hari='Sabtu';
        }elseif($hari=="Sunday"){
            $hari="Minggu";
        }else{
            $hari="ERROR!";
        }
		$tanggal = substr($tgl,8,2);
		$bulan = getBulan(substr($tgl,5,2));
		$tahun = substr($tgl,0,4);
		return $tanggal.' '.$bulan.' '.$tahun;		 
}
function tgl_indo1($tgl){
		$hari = date('l',strtotime($tgl));
		if ($hari=="Monday"){
            $hari="Senin";
        }elseif($hari=="Tuesday"){
            $hari="Selasa";
        }elseif($hari=="Wednesday"){
            $hari="Rabu";
        }elseif($hari=="Thursday"){
            $hari="Kamis";
        }elseif($hari=="Friday"){
            $hari="Jumat";
        }elseif($hari=="Saturday"){
            $hari='Sabtu';
        }elseif($hari=="Sunday"){
            $hari="Minggu";
        }else{
            $hari="ERROR!";
        }
		$tanggal = substr($tgl,8,2);
		$bulan = getBulan_e(substr($tgl,5,2));
		$tahun = substr($tgl,0,4);
		return $tanggal.'/'.$bulan.'/'.$tahun;		 
}		

function getBulan($bln){
	switch ($bln){
		case 1: 
			return "Januari";
			break;
		case 2:
			return "Februari";
			break;
		case 3:
			return "Maret";
			break;
		case 4:
			return "April";
			break;
		case 5:
			return "Mei";
			break;
		case 6:
			return "Juni";
			break;
		case 7:
			return "Juli";
			break;
		case 8:
			return "Agustus";
			break;
		case 9:
			return "September";
			break;
		case 10:
			return "Oktober";
			break;
		case 11:
			return "November";
			break;
		case 12:
			return "Desember";
			break;
	}
} 

function tgl_eng($tgl_d){
		$tanggal_d = substr($tgl_d,8,2);
		//$tanggal_dt = substr($tgl_d,8,2);
		$bulan_d = getBulan_d(substr($tgl_d,5,2));
		$tahun_d = substr($tgl_d,0,4);
		/*
		if(($tanggal_dt = "1") OR ($tanggal_dt = "21") OR ($tanggal_dt = "31")){
			$sup_word = "st";
		}else if(($tanggal_dt = "2") OR ($tanggal_dt = "22")){
			$sup_word = "nd";
		}else if(($tanggal_dt = "3") OR ($tanggal_dt = "23")){
			$sup_word = "rd";
		}else{
			$sup_word = "th";
		}
		*/
		
    if (!in_array(($tanggal_d % 100),array(11,12,13))){
      switch ($tanggal_d % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $bulan_d.' '.$tanggal_d.'<sup>st</sup>, '.$tahun_d;	
        case 2:  return $bulan_d.' '.$tanggal_d.'<sup>nd</sup>, '.$tahun_d;	
        case 3:  return $bulan_d.' '.$tanggal_d.'<sup>rd</sup>, '.$tahun_d;	
      }
    }
    
		return $bulan_d.' '.$tanggal_d.'<sup>th</sup>, '.$tahun_d;		 
}	
function getBulan_d($bln_d){
	switch ($bln_d){
		case 1: 
			return "January";
			break;
		case 2:
			return "February";
			break;
		case 3:
			return "March";
			break;
		case 4:
			return "April";
			break;
		case 5:
			return "May";
			break;
		case 6:
			return "June";
			break;
		case 7:
			return "July";
			break;
		case 8:
			return "August";
			break;
		case 9:
			return "September";
			break;
		case 10:
			return "October";
			break;
		case 11:
			return "November";
			break;
		case 12:
			return "December";
			break;
	}
} 

function getBulan_e($bln_d){
	switch ($bln_d){
		case 1: 
			return "01";
			break;
		case 2:
			return "02";
			break;
		case 3:
			return "03";
			break;
		case 4:
			return "04";
			break;
		case 5:
			return "05";
			break;
		case 6:
			return "06";
			break;
		case 7:
			return "07";
			break;
		case 8:
			return "08";
			break;
		case 9:
			return "09";
			break;
		case 10:
			return "10";
			break;
		case 11:
			return "11";
			break;
		case 12:
			return "12";
			break;
	}
} 
?>


